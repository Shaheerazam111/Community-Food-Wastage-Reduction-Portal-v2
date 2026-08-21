<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FoodListingController extends Controller
{
    // TASK 2: Donor creates a food listing
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'donor') {
            return response()->json(['message' => 'Only donors can create listings'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in(['veg', 'non_veg'])],
            'quantity' => 'required|string|max:100',
            'pickup_address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'expiry_at' => 'required|date|after:now',
        ]);

        $data['donor_id'] = Auth::id();

        $listing = FoodListing::create($data);

        return response()->json(['listing' => $listing], 201);
    }

    // Donor's own listings
    public function myListings()
    {
        $listings = FoodListing::where('donor_id', Auth::id())
            ->latest()
            ->get();

        return response()->json(['listings' => $listings]);
    }

    // TASK 3 + Challenge 1 + Challenge 3: nearby search, sorted by distance
    public function search(Request $request)
    {
        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'nullable|numeric|min:0.1|max:100',
        ]);

        $radiusKm = $data['radius_km'] ?? 5;

        $distanceExpr = '6371 * ACOS(
            COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?))
            + SIN(RADIANS(?)) * SIN(RADIANS(latitude))
        )';

        $listings = DB::table('food_listings')
            ->selectRaw('*, ' . $distanceExpr . ' AS distance_km', [
                $data['latitude'], $data['longitude'], $data['latitude'],
            ])
            ->where('status', 'AVAILABLE')
            ->where('expiry_at', '>', now())
            ->havingRaw('distance_km <= ?', [$radiusKm])
            ->orderBy('distance_km')
            ->get();

        return response()->json(['listings' => $listings]);
    }

    // TASK 2: status transition CLAIMED -> DELIVERED
    public function markDelivered($id)
    {
        $listing = FoodListing::findOrFail($id);

        if ($listing->donor_id !== Auth::id()) {
            return response()->json(['message' => 'Only the donor can mark this delivered'], 403);
        }

        if ($listing->status !== 'CLAIMED') {
            return response()->json(['message' => 'Listing must be CLAIMED before it can be DELIVERED'], 422);
        }

        $listing->update([
            'status' => 'DELIVERED',
            'delivered_at' => now(),
        ]);

        return response()->json(['listing' => $listing]);
    }
}
