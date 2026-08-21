<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    // TASK 4 + Challenge 2: atomic claim, safe against simultaneous clicks
    public function claim(Request $request, $id)
    {
        if (Auth::user()->role !== 'recipient') {
            return response()->json(['message' => 'Only recipients can claim food'], 403);
        }

        // Single atomic UPDATE: only succeeds if the row is still AVAILABLE.
        // If two requests race, only one UPDATE can flip the row - the other gets 0 rows affected.
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
            return response()->json(['message' => 'This item was already claimed or has expired'], 409);
        }

        return response()->json(['message' => 'Claim successful']);
    }
}
