<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationDocument;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminVerificationController extends Controller
{
    // NGO documents waiting for review
    public function index()
    {
        $documents = VerificationDocument::with('user:id,name,email,ngo_name,verification_status')
            ->latest()
            ->paginate(20);

        return view('admin.verification.index', compact('documents'));
    }

    // Download document (admin only)
    public function download($id)
    {
        $document = VerificationDocument::findOrFail($id);

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download(
            $document->file_path,
            $document->original_name ?? 'ngo-document'
        );
    }

    // Approve or reject NGO verification
    public function update(Request $request, $id)
    {
        $document = VerificationDocument::with('user')->findOrFail($id);

        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $status = $data['action'] === 'approve' ? 'approved' : 'rejected';
        $docStatus = $data['action'] === 'approve' ? 'approved' : 'rejected';

        $document->update([
            'status' => $docStatus,
            'admin_note' => $data['admin_note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($document->user) {
            $document->user->update(['verification_status' => $status]);

            app(NotificationService::class)->notify(
                $document->user,
                $status === 'approved' ? 'NGO verified' : 'Verification rejected',
                $status === 'approved'
                    ? 'Your NGO document was approved. You can search and claim food.'
                    : ('Your document was rejected. '.($data['admin_note'] ?? 'Upload a new document from your profile page.'))
            );
        }

        return back()->with('success', 'Verification updated.');
    }
}
