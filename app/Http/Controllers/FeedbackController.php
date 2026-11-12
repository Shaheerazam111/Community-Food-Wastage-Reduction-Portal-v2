<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\FoodListing;
use App\Models\ListingFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    use HandlesPortalResponses;

    // Rate food after delivery
    public function store(Request $request, $listingId)
    {
        if (Auth::user()->role !== 'recipient') {
            return $this->portalBack($request, ['message' => 'Only recipients can leave feedback'], 403, 'Only NGOs can leave feedback.');
        }

        $listing = FoodListing::findOrFail($listingId);

        if ((int) $listing->claimed_by !== Auth::id() || $listing->status !== 'DELIVERED') {
            return $this->portalBack($request, ['message' => 'Feedback only after you claimed and food was delivered'], 422, 'You can only rate delivered food you claimed.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        ListingFeedback::updateOrCreate(
            [
                'food_listing_id' => $listing->id,
                'recipient_id' => Auth::id(),
            ],
            $data
        );

        return $this->portalRedirect(
            $request,
            ['message' => 'Feedback saved'],
            200,
            '/food/claims',
            'Thanks — your rating was saved.'
        );
    }
}
