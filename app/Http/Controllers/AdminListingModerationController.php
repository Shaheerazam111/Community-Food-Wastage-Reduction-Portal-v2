<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminListingModerationController extends Controller
{
    // Food listings pending admin approval
    public function index()
    {
        $pending = FoodListing::with('donor:id,name,business_name,email')
            ->where('moderation_status', 'PENDING')
            ->orderBy('created_at')
            ->get();

        $recent = FoodListing::with('donor:id,name,business_name')
            ->whereIn('moderation_status', ['APPROVED', 'REJECTED'])
            ->latest('moderated_at')
            ->take(15)
            ->get();

        return view('admin.listings.index', compact('pending', 'recent'));
    }

    // Approve or reject a listing before it goes live
    public function update(Request $request, $id)
    {
        $listing = FoodListing::with('donor')->findOrFail($id);

        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:255',
        ]);

        if ($data['action'] === 'approve') {
            $listing->update([
                'moderation_status' => 'APPROVED',
                'rejection_reason' => null,
                'moderated_by' => Auth::id(),
                'moderated_at' => now(),
            ]);

            $this->notifyNearbyRecipients($listing, app(NotificationService::class));

            if ($listing->donor) {
                app(NotificationService::class)->notify(
                    $listing->donor,
                    'Listing approved',
                    '"'.$listing->title.'" is now visible to NGOs in search.'
                );
            }
        } else {
            $listing->update([
                'moderation_status' => 'REJECTED',
                'rejection_reason' => $data['rejection_reason'],
                'moderated_by' => Auth::id(),
                'moderated_at' => now(),
            ]);

            if ($listing->donor) {
                app(NotificationService::class)->notify(
                    $listing->donor,
                    'Listing rejected',
                    '"'.$listing->title.'" was not approved. Reason: '.$data['rejection_reason']
                );
            }
        }

        return back()->with('success', 'Listing moderation updated.');
    }

    // Alert NGOs near a newly approved listing
    private function notifyNearbyRecipients(FoodListing $listing, NotificationService $notifier): void
    {
        User::query()
            ->where('role', 'recipient')
            ->where('is_active', true)
            ->where('verification_status', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->chunkById(50, function ($recipients) use ($listing, $notifier) {
                foreach ($recipients as $recipient) {
                    $km = FoodListing::distanceKm(
                        (float) $listing->latitude,
                        (float) $listing->longitude,
                        (float) $recipient->latitude,
                        (float) $recipient->longitude
                    );
                    if ($km <= 10) {
                        $notifier->notify(
                            $recipient,
                            'New food nearby',
                            $listing->title.' is available about '.round($km, 1).' km away.'
                        );
                    }
                }
            });
    }
}
