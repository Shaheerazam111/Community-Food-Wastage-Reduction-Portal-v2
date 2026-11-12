<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\FoodListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerController extends Controller
{
    use HandlesPortalResponses;

    // Pickup tasks = claimed listings awaiting delivery
    public function index()
    {
        if (Auth::user()->role !== 'volunteer') {
            return redirect('/login')->with('error', 'Log in as a volunteer.');
        }

        $tasks = FoodListing::where('status', 'CLAIMED')
            ->with(['donor:id,name,business_name,phone,address', 'claimedBy:id,ngo_name,name'])
            ->orderBy('claimed_at')
            ->get();

        return view('volunteer.tasks.index', compact('tasks'));
    }

    // Volunteer confirms pickup completed
    public function complete(Request $request, $id)
    {
        if (Auth::user()->role !== 'volunteer') {
            return $this->portalBack($request, ['message' => 'Volunteers only'], 403, 'Volunteers only.');
        }

        $listing = FoodListing::findOrFail($id);

        if ($listing->status !== 'CLAIMED') {
            return $this->portalBack($request, ['message' => 'Task not active'], 422, 'This task is not active.');
        }

        $listing->update([
            'status' => 'DELIVERED',
            'delivered_at' => now(),
        ]);

        return $this->portalRedirect(
            $request,
            ['listing' => $listing],
            200,
            '/volunteer/tasks',
            'Pickup marked complete (DELIVERED).'
        );
    }
}
