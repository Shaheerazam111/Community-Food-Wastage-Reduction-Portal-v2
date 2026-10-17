<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\FoodListing;
use App\Support\PortalNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    use HandlesPortalResponses;

    public function claim(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'recipient') {
            return $this->portalBack(
                $request,
                ['message' => 'Only recipients can claim food'],
                403,
                'Only recipients can claim food.'
            );
        }

        if (! $user->is_verified || ! $user->is_active) {
            return $this->portalBack(
                $request,
                ['message' => 'Only verified active recipients can claim food'],
                403,
                'Only verified active recipients can claim food.'
            );
        }

        $updated = DB::table('food_listings')
            ->where('id', $id)
            ->where('status', 'AVAILABLE')
            ->where('expiry_at', '>', now())
            ->update([
                'status' => 'CLAIMED',
                'claimed_by' => Auth::id(),
                'claimed_at' => now(),
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            return $this->portalBack(
                $request,
                ['message' => 'This item was already claimed or has expired'],
                409,
                'This food was already claimed or has expired.'
            );
        }

        $listing = FoodListing::find($id);
        if ($listing) {
            $ngoName = $user->ngo_name ?? $user->name;
            PortalNotifier::notifyUser(
                (int) $listing->donor_id,
                $ngoName.' claimed '.$listing->title.'.',
                (int) $listing->id
            );
        }

        $redirectUrl = $this->wantsJson($request) ? url()->previous() : '/food/claims';

        return $this->portalRedirect(
            $request,
            ['message' => 'Claim successful'],
            200,
            $redirectUrl,
            'Claim successful (POST /food/'.$id.'/claim). Pickup details are under My claims.'
        );
    }
}
