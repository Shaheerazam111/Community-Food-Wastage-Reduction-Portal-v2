<?php

namespace App\Http\Controllers;

use App\Models\VerificationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load('verificationDocument');

        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];

        if ($user->role === 'donor') {
            $rules['business_name'] = 'required|string|max:255';
        } else {
            $rules['ngo_name'] = 'required|string|max:255';
            $rules['contact_person'] = 'nullable|string|max:255';
        }

        $data = $request->validate($rules);

        $user->update($data);

        return redirect('/profile')->with('success', 'Profile updated.');
    }

    public function uploadDocument(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('document');
        $existing = $user->verificationDocument;

        if ($existing && Storage::disk('public')->exists($existing->file_path)) {
            Storage::disk('public')->delete($existing->file_path);
        }

        $path = $file->store('verification-documents/'.$user->id, 'public');

        VerificationDocument::updateOrCreate(
            ['user_id' => $user->id],
            [
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'status' => 'pending',
                'admin_note' => null,
            ]
        );

        $user->update(['is_verified' => false]);

        return redirect('/profile')->with(
            'success',
            'Document uploaded. Status is pending until an admin reviews it.'
        );
    }
}
