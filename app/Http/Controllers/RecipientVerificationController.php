<?php

namespace App\Http\Controllers;

use App\Models\VerificationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecipientVerificationController extends Controller
{
    // NGO verification status page
    public function show()
    {
        if (Auth::user()->role !== 'recipient') {
            return redirect('/login');
        }

        $document = VerificationDocument::where('user_id', Auth::id())->latest()->first();

        return view('recipient.verification.show', [
            'user' => Auth::user(),
            'document' => $document,
        ]);
    }

    // Re-upload document after rejection
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'recipient') {
            return redirect('/login');
        }

        $data = $request->validate([
            'verification_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $file = $data['verification_document'];
        $path = $file->store('ngo-documents', 'local');

        VerificationDocument::create([
            'user_id' => Auth::id(),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'status' => 'pending',
        ]);

        Auth::user()->update(['verification_status' => 'pending']);

        return redirect('/recipient/verification')->with('success', 'Document uploaded. Wait for admin approval.');
    }
}
