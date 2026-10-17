<?php

namespace App\Http\Controllers;

use App\Models\PortalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = PortalNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(25);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $notification = PortalNotification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Notification marked as read']);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request)
    {
        PortalNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'All notifications marked as read']);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
