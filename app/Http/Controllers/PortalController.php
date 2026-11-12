<?php

namespace App\Http\Controllers;

use App\Models\FoodListing;
use App\Support\MealsEstimate;
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
        if (! $this->loggedInDonor()) {
            return redirect('/login')->with('error', 'Log in as a donor to continue.');
        }

        $donorId = Auth::id();
        $listings = FoodListing::where('donor_id', $donorId)
            ->with('claimedBy:id,name,ngo_name')
            ->latest()
            ->take(10)
            ->get();

        $deliveredListings = FoodListing::where('donor_id', $donorId)->where('status', 'DELIVERED')->get();

        $stats = [
            'available' => FoodListing::where('donor_id', $donorId)->where('status', 'AVAILABLE')->count(),
            'claimed' => FoodListing::where('donor_id', $donorId)->where('status', 'CLAIMED')->count(),
            'delivered' => $deliveredListings->count(),
            'meals_saved' => $deliveredListings->sum(fn ($row) => MealsEstimate::fromQuantityString($row->quantity)),
        ];

        return view('donor.dashboard', compact('listings', 'stats'));
    }

    public function donorCreateForm()
    {
        if (! $this->loggedInDonor()) {
            return redirect('/login')->with('error', 'Log in as a donor to continue.');
        }

        return view('donor.donations.create');
    }

    /** Search form defaults; results come from GET /food/search (same API as Postman). */
    public function recipientFoodForm()
    {
        if (! $this->loggedInRecipient()) {
            return redirect('/login')->with('error', 'Log in as a recipient (NGO) to continue.');
        }

        $user = Auth::user();

        return view('recipient.food.index', [
            'listings' => collect(),
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
            'radius_km' => 5,
            'city' => '',
            'searched' => false,
            'pendingNearby' => 0,
        ]);
    }

    private function loggedInDonor(): bool
    {
        return Auth::check() && Auth::user()->role === 'donor';
    }

    private function loggedInRecipient(): bool
    {
        return Auth::check() && Auth::user()->role === 'recipient';
    }
}
