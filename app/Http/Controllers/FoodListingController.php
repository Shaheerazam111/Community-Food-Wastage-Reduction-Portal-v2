<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\FoodCategory;
use App\Models\FoodListing;
use App\Support\PortalNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FoodListingController extends Controller
{
    use HandlesPortalResponses;

    public function store(Request $request)
    {
        $donor = Auth::user();
        $data = $this->validateListing($request);
        $data['donor_id'] = $donor->id;
        $data['status'] = 'AVAILABLE';

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request, $donor->id);
        }

        $listing = FoodListing::create($data);

        PortalNotifier::notifyRecipientsNewListing($listing);

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            201,
            '/food/mine',
            'Food listing posted.'
        );
    }

    public function update(Request $request, $id)
    {
        $listing = $this->ownedAvailableListing($id);
        $data = $this->validateListing($request, $listing);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($listing->photo_path);
            $data['photo_path'] = $this->storePhoto($request, Auth::id());
        }

        $listing->update($data);

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            200,
            '/food/mine',
            'Listing updated.'
        );
    }

    public function cancel(Request $request, $id)
    {
        $listing = $this->ownedAvailableListing($id);
        $listing->update(['status' => 'CANCELLED']);

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            200,
            '/food/mine',
            'Listing cancelled.'
        );
    }

    public function myListings(Request $request)
    {
        $listings = FoodListing::where('donor_id', Auth::id())
            ->with(['claimedBy:id,name,ngo_name', 'foodCategory'])
            ->latest()
            ->get();

        if ($this->wantsJson($request)) {
            return response()->json(['listings' => $listings]);
        }

        return view('donor.donations.index', compact('listings'));
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $mode = $request->input('search_mode', 'nearby');
        $listingCities = FoodListing::citiesWithAvailableFood();

        if ($mode === 'city') {
            $data = $request->validate([
                'city' => 'required|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            $lat = $data['latitude'] ?? $user->latitude;
            $lng = $data['longitude'] ?? $user->longitude;

            $listings = FoodListing::searchAvailableByCity(
                $data['city'],
                $lat !== null ? (float) $lat : null,
                $lng !== null ? (float) $lng : null
            );

            return $this->searchView($listings, [
                'search_mode' => 'city',
                'city' => $data['city'],
                'latitude' => $lat,
                'longitude' => $lng,
                'radius_km' => 10,
                'listing_cities' => $listingCities,
                'searched' => true,
            ]);
        }

        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'nullable|numeric|min:0.1|max:100',
        ]);

        $radiusKm = (float) ($data['radius_km'] ?? 5);

        $listings = FoodListing::searchAvailableNearby(
            (float) $data['latitude'],
            (float) $data['longitude'],
            $radiusKm
        );

        return $this->searchView($listings, [
            'search_mode' => 'nearby',
            'city' => $request->input('city'),
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'radius_km' => $radiusKm,
            'listing_cities' => $listingCities,
            'searched' => true,
        ]);
    }

    public function showForRecipient($id)
    {
        $listing = FoodListing::with(['donor:id,name,business_name,phone', 'foodCategory'])
            ->where('status', 'AVAILABLE')
            ->where('expiry_at', '>', now())
            ->findOrFail($id);

        return view('recipient.food.show', compact('listing'));
    }

    public function markDelivered(Request $request, $id)
    {
        $listing = FoodListing::findOrFail($id);
        $user = Auth::user();

        $isDonor = $listing->donor_id === $user->id;
        $isClaimer = $user->role === 'recipient'
            && (int) $listing->claimed_by === (int) $user->id;

        if (! $isDonor && ! $isClaimer) {
            return $this->portalBack(
                $request,
                ['message' => 'Only the donor or the recipient who claimed this can mark it delivered'],
                403,
                'Only the donor or the recipient who claimed this can mark it delivered.'
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

        if ($isDonor && $listing->claimed_by) {
            PortalNotifier::notifyUser(
                (int) $listing->claimed_by,
                'Pickup confirmed: '.$listing->title.' was marked delivered.',
                $listing->id
            );
        } elseif ($isClaimer) {
            PortalNotifier::notifyUser(
                (int) $listing->donor_id,
                'Pickup confirmed: '.$listing->title.' was marked delivered by the recipient.',
                $listing->id
            );
        }

        $defaultBack = $isClaimer ? '/food/claims' : '/food/mine';
        $back = url()->previous();
        if ($back === url()->current() || $back === url('/')) {
            $back = $defaultBack;
        }

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            200,
            $back,
            'Listing marked as delivered.'
        );
    }

    public function myClaims(Request $request)
    {
        $listings = FoodListing::where('claimed_by', Auth::id())
            ->with('donor:id,name,business_name,phone,address,latitude,longitude')
            ->orderByDesc('claimed_at')
            ->get();

        if ($this->wantsJson($request)) {
            return response()->json(['claims' => $listings]);
        }

        return view('recipient.claims.index', ['claims' => $listings]);
    }

    private function searchView($listings, array $extra)
    {
        if (request()->expectsJson()) {
            return response()->json(['listings' => $listings]);
        }

        return view('recipient.food.index', array_merge($extra, [
            'listings' => $listings,
        ]));
    }

    private function validateListing(Request $request, ?FoodListing $listing = null): array
    {
        $donor = Auth::user();

        if (! $donor->is_verified || ! $donor->is_active) {
            abort(403, 'Only verified active donors can manage listings.');
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|integer|exists:food_categories,id',
            'quantity_value' => 'required|numeric|min:0.01|max:99999',
            'quantity_unit' => ['required', Rule::in(['kg', 'servings'])],
            'pickup_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'expiry_at' => 'required|date|after:now',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        $data = $request->validate($rules);

        $categoryOk = FoodCategory::where('id', $data['category_id'])
            ->where('is_active', true)
            ->exists();

        if (! $categoryOk) {
            throw ValidationException::withMessages([
                'category_id' => 'Choose an active category.',
            ]);
        }

        return $data;
    }

    private function ownedAvailableListing($id): FoodListing
    {
        return FoodListing::where('donor_id', Auth::id())
            ->where('status', 'AVAILABLE')
            ->findOrFail($id);
    }

    private function storePhoto(Request $request, int $donorId): string
    {
        return $request->file('photo')->store('food-listings/'.$donorId, 'public');
    }

    private function deletePhoto(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
