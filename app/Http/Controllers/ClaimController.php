<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    use HandlesPortalResponses;

    public function claim(Request $request, $id)
    {
        if (Auth::user()->role !== 'recipient') {
            return $this->portalBack(
                $request,
                ['message' => 'Only recipients can claim food'],
                403,
                'Only recipients can claim food.'
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
