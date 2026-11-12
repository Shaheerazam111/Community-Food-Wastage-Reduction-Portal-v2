<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    // List users for verify / block
    public function index()
    {
        $users = User::orderByDesc('created_at')->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    // Toggle active flag (block / unblock)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ((int) $user->id === Auth::id()) {
            return back()->with('error', 'You cannot block your own admin account.');
        }

        $data = $request->validate([
            'is_active' => 'required|in:0,1',
        ]);

        $user->update(['is_active' => $data['is_active'] === '1']);

        return back()->with('success', 'User status updated.');
    }
}
