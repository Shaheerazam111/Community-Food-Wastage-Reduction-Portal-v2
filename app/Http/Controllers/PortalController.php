<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use App\Models\FoodListing;
use Illuminate\Support\Facades\Auth;

/**
 * HTML pages only (GET). All writes go through /auth/* and /food/* API routes.
 */
class PortalController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function donorDashboard()
    {
        $donorId = Auth::id();
        $listings = FoodListing::where('donor_id', $donorId)
            ->with(['claimedBy:id,name,ngo_name', 'foodCategory'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'available' => FoodListing::where('donor_id', $donorId)->where('status', 'AVAILABLE')->count(),
            'claimed' => FoodListing::where('donor_id', $donorId)->where('status', 'CLAIMED')->count(),
            'delivered' => FoodListing::where('donor_id', $donorId)->where('status', 'DELIVERED')->count(),
        ];

        return view('donor.dashboard', compact('listings', 'stats'));
    }

    public function donorCreateForm()
    {
        return view('donor.donations.create', $this->listingFormData());
    }

    public function donorEditForm($id)
    {
        $listing = FoodListing::where('donor_id', Auth::id())
            ->where('status', 'AVAILABLE')
            ->findOrFail($id);

        return view('donor.donations.edit', array_merge($this->listingFormData(), [
            'listing' => $listing,
        ]));
    }

    private function listingFormData(): array
    {
        return [
            'categories' => FoodCategory::where('is_active', true)->orderBy('name')->get(),
            'defaults' => Auth::user(),
        ];
    }

    public function recipientDashboard()
    {
        $user = Auth::user();

        $nearbyFood = collect();
        if ($user->latitude !== null && $user->longitude !== null) {
            $nearbyFood = FoodListing::searchAvailableNearby(
                (float) $user->latitude,
                (float) $user->longitude,
                10
            );
        }

        return view('recipient.dashboard', [
            'user' => $user,
            'nearbyFood' => $nearbyFood,
            'recentClaims' => FoodListing::where('claimed_by', $user->id)
                ->latest('claimed_at')
                ->take(5)
                ->get(),
        ]);
    }

    /** Search form defaults; results come from GET /food/search. */
    public function recipientFoodForm()
    {
        $user = Auth::user();

        return view('recipient.food.index', [
            'listings' => collect(),
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'radius_km' => 10,
            'search_mode' => 'nearby',
            'city' => $user->city,
            'listing_cities' => FoodListing::citiesWithAvailableFood(),
            'searched' => false,
        ]);
    }

    public function adminDashboard()
    {
        $stats = [
            'users' => \App\Models\User::count(),
            'donors' => \App\Models\User::where('role', 'donor')->count(),
            'recipients' => \App\Models\User::where('role', 'recipient')->count(),
            'listings' => FoodListing::count(),
            'pending_verification' => \App\Models\User::whereIn('role', ['donor', 'recipient'])
                ->where('is_verified', false)
                ->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
