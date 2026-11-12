<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\FoodListing;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FoodListingController extends Controller
{
    use HandlesPortalResponses;

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'donor') {
            return $this->portalBack(
                $request,
                ['message' => 'Only donors can create listings'],
                403,
                'Only donors can create listings.'
            );
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in(['veg', 'non_veg'])],
            'quantity' => 'required|string|max:100',
            'expiry_at' => 'required|date|after:now',
            'photo' => 'nullable|image|max:2048',
        ]);

        $donor = Auth::user();

        if ($donor->latitude === null || $donor->longitude === null) {
            return $this->portalBack(
                $request,
                ['message' => 'Donor profile must have latitude and longitude (set during registration).'],
                422,
                'Your profile needs a map location from registration.'
            );
        }

        $data['donor_id'] = $donor->id;
        $data['pickup_address'] = $donor->address ?? $donor->business_name ?? 'Donor location';
        $data['city'] = $this->guessCityFromAddress($data['pickup_address']);
        $data['latitude'] = $donor->latitude;
        $data['longitude'] = $donor->longitude;

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('food-photos', 'public');
        }
        unset($data['photo']);

        $autoApprove = app()->environment('local');
        $data['moderation_status'] = $autoApprove ? 'APPROVED' : 'PENDING';

        $listing = FoodListing::create($data);

        $flash = $autoApprove
            ? 'Listing posted. NGOs can find it in search now.'
            : 'Listing submitted. It will appear in search after admin approval.';

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            201,
            '/food/mine',
            $flash
        );
    }

    public function myListings(Request $request)
    {
        $listings = FoodListing::where('donor_id', Auth::id())
            ->with('claimedBy:id,name,ngo_name')
            ->latest()
            ->get();

        if ($this->wantsJson($request)) {
            return response()->json(['listings' => $listings]);
        }

        if (Auth::user()->role !== 'donor') {
            return redirect('/login')->with('error', 'Log in as a donor to see your listings.');
        }

        return view('donor.donations.index', compact('listings'));
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'city' => 'nullable|string|max:120',
            'latitude' => 'required_without:city|nullable|numeric|between:-90,90',
            'longitude' => 'required_without:city|nullable|numeric|between:-180,180',
            'radius_km' => 'nullable|numeric|min:0.1|max:100',
        ]);

        $radiusKm = $data['radius_km'] ?? 5;
        $city = isset($data['city']) ? trim($data['city']) : '';

        $searchLat = null;
        $searchLng = null;

        if ($city !== '') {
            $listings = FoodListing::searchAvailableByCity($city);
            $searchLat = Auth::user()?->latitude;
            $searchLng = Auth::user()?->longitude;
            $listings = FoodListing::attachDistanceKm($listings, $searchLat, $searchLng);
            $latitude = $searchLat;
            $longitude = $searchLng;
        } else {
            $searchLat = (float) $data['latitude'];
            $searchLng = (float) $data['longitude'];
            $listings = FoodListing::searchAvailableNearby(
                $searchLat,
                $searchLng,
                (float) $radiusKm
            );
            $latitude = $searchLat;
            $longitude = $searchLng;
        }

        if ($this->wantsJson($request)) {
            return response()->json(['listings' => $listings]);
        }

        if (! Auth::check() || Auth::user()->role !== 'recipient') {
            return redirect('/login')->with('error', 'Log in as a recipient (NGO) to search food.');
        }

        $pendingNearby = ($city === '' && $searchLat !== null && $searchLng !== null)
            ? FoodListing::countPendingModerationNearby($searchLat, $searchLng, (float) $radiusKm)
            : 0;

        return view('recipient.food.index', [
            'listings' => $listings,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius_km' => $radiusKm,
            'city' => $city,
            'searched' => true,
            'pendingNearby' => $pendingNearby,
        ]);
    }

    public function markDelivered(Request $request, $id)
    {
        $listing = FoodListing::findOrFail($id);

        if ($listing->donor_id !== Auth::id()) {
            return $this->portalBack(
                $request,
                ['message' => 'Only the donor can mark this delivered'],
                403,
                'Only the donor can mark this delivered.'
            );
        }

        if ($listing->status !== 'CLAIMED') {
            return $this->portalBack(
                $request,
                ['message' => 'Listing must be CLAIMED before it can be DELIVERED'],
                422,
                'Listing must be CLAIMED before it can be DELIVERED.'
            );
        }

        $listing->update([
            'status' => 'DELIVERED',
            'delivered_at' => now(),
        ]);

        if ($listing->claimedBy) {
            app(NotificationService::class)->notify(
                $listing->claimedBy,
                'Pickup marked delivered',
                'The donor marked "'.$listing->title.'" as delivered. You can leave feedback under My claims.'
            );
        }

        $back = url()->previous();
        if ($back === url()->current()) {
            $back = '/food/mine';
        }

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            200,
            $back,
            'Listing marked DELIVERED (POST /food/'.$id.'/deliver).'
        );
    }

    // Donor withdraws an AVAILABLE listing (marks EXPIRED)
    public function cancel(Request $request, $id)
    {
        $listing = FoodListing::findOrFail($id);

        if ($listing->donor_id !== Auth::id()) {
            return $this->portalBack(
                $request,
                ['message' => 'Only the donor can cancel this listing'],
                403,
                'Only the donor can cancel this listing.'
            );
        }

        if ($listing->status !== 'AVAILABLE') {
            return $this->portalBack(
                $request,
                ['message' => 'Only AVAILABLE listings can be cancelled'],
                422,
                'Only available listings can be cancelled.'
            );
        }

        $listing->update([
            'status' => 'EXPIRED',
            'updated_at' => now(),
        ]);

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            200,
            url()->previous() !== url()->current() ? url()->previous() : '/food/mine',
            'Listing cancelled and removed from search.'
        );
    }

    /** Food this recipient has claimed (AVAILABLE → CLAIMED → DELIVERED lifecycle). */
    public function myClaims(Request $request)
    {
        $listings = FoodListing::where('claimed_by', Auth::id())
            ->with('donor:id,name,business_name,phone,address,latitude,longitude')
            ->with('feedbacks')
            ->orderByDesc('claimed_at')
            ->get();

        if ($this->wantsJson($request)) {
            return response()->json(['claims' => $listings]);
        }

        if (Auth::user()->role !== 'recipient') {
            return redirect('/login')->with('error', 'Log in as a recipient (NGO) to see your claims.');
        }

        return view('recipient.claims.index', ['claims' => $listings]);
    }

    private function guessCityFromAddress(string $address): ?string
    {
        $parts = array_map('trim', explode(',', $address));
        if (count($parts) === 0) {
            return null;
        }

        return $parts[count($parts) - 1] ?: null;
    }

}
