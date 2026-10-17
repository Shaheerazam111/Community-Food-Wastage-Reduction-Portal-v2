<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\Feedback;
use App\Models\FoodListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    use HandlesPortalResponses;

    public function store(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'recipient' || ! $user->is_verified || ! $user->is_active) {
            return $this->portalBack(
                $request,
                ['message' => 'Only verified recipients can leave feedback'],
                403,
                'Only verified recipients can leave feedback.'
            );
        }

        $listing = FoodListing::where('claimed_by', $user->id)
            ->where('status', 'DELIVERED')
            ->findOrFail($id);

        if (Feedback::where('food_listing_id', $listing->id)->exists()) {
            return $this->portalBack(
                $request,
                ['message' => 'Feedback already submitted for this listing'],
                422,
                'You already rated this pickup.'
            );
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Feedback::create([
            'food_listing_id' => $listing->id,
            'recipient_id' => $user->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return $this->portalRedirect(
            $request,
            ['message' => 'Feedback saved'],
            201,
            '/food/claims',
            'Thanks — your rating was saved.'
        );
    }
}
