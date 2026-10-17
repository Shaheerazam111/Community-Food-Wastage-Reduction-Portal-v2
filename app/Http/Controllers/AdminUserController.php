<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->whereIn('role', ['donor', 'recipient'])
            ->with('verificationDocument')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        if (! in_array($user->role, ['donor', 'recipient'], true)) {
            return back()->with('error', 'You can only manage donor and NGO accounts.');
        }

        $data = $request->validate([
            'action' => ['required', Rule::in(['approve', 'reject', 'block', 'unblock'])],
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($data['action'] === 'block' && $user->id === Auth::id()) {
            return back()->with('error', 'You cannot block your own account.');
        }

        $document = $user->verificationDocument;

        match ($data['action']) {
            'approve' => $this->approveUser($user, $document, $data['admin_note'] ?? null),
            'reject' => $this->rejectUser($user, $document, $data['admin_note'] ?? null),
            'block' => $user->update(['is_active' => false]),
            'unblock' => $user->update(['is_active' => true]),
        };

        $labels = [
            'approve' => 'User approved.',
            'reject' => 'User rejected.',
            'block' => 'User blocked.',
            'unblock' => 'User unblocked.',
        ];

        return back()->with('success', $labels[$data['action']]);
    }

    public function document(User $user)
    {
        if (! in_array($user->role, ['donor', 'recipient'], true)) {
            abort(404);
        }

        $document = $user->verificationDocument;

        if (! $document || ! Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->response(
            $document->file_path,
            $document->original_name ?? 'verification-document'
        );
    }

    private function approveUser(User $user, ?VerificationDocument $document, ?string $note): void
    {
        $user->update(['is_verified' => true, 'is_active' => true]);

        if ($document) {
            $document->update([
                'status' => 'approved',
                'admin_note' => $note,
            ]);
        }
    }

    private function rejectUser(User $user, ?VerificationDocument $document, ?string $note): void
    {
        $user->update(['is_verified' => false]);

        if ($document) {
            $document->update([
                'status' => 'rejected',
                'admin_note' => $note,
            ]);
        }
    }
}
