<?php

namespace App\Http\Controllers;

use App\Models\PortalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // List my dashboard alerts
    public function index()
    {
        $notifications = PortalNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    // Mark one notification read
    public function markRead(Request $request, $id)
    {
        $note = PortalNotification::where('user_id', Auth::id())->findOrFail($id);
        $note->update(['read_at' => now()]);

        return back()->with('success', 'Marked as read.');
    }
}
