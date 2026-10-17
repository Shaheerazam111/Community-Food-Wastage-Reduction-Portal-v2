<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\FoodListing;
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
        $data['latitude'] = $donor->latitude;
        $data['longitude'] = $donor->longitude;

        $listing = FoodListing::create($data);

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            201,
            '/food/mine',
            'Food listing posted (POST /food).'
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
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'nullable|numeric|min:0.1|max:100',
        ]);

        $radiusKm = $data['radius_km'] ?? 5;

        $listings = FoodListing::searchAvailableNearby(
            (float) $data['latitude'],
            (float) $data['longitude'],
            (float) $radiusKm
        );

        if ($this->wantsJson($request)) {
            return response()->json(['listings' => $listings]);
        }

        if (! Auth::check() || Auth::user()->role !== 'recipient') {
            return redirect('/login')->with('error', 'Log in as a recipient (NGO) to search food.');
        }

        return view('recipient.food.index', [
            'listings' => $listings,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'radius_km' => $radiusKm,
            'searched' => true,
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

    /** Food this recipient has claimed (AVAILABLE → CLAIMED → DELIVERED lifecycle). */
    public function myClaims(Request $request)
    {
        $listings = FoodListing::where('claimed_by', Auth::id())
            ->with('donor:id,name,business_name,phone,address,latitude,longitude')
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
}
