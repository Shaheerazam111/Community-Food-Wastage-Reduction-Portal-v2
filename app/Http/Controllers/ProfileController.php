<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use HandlesPortalResponses;

    // Show profile edit form
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    // Save profile changes
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
        ];

        if (in_array($user->role, ['donor', 'recipient', 'volunteer'], true)) {
            $rules['address'] = 'required|string|max:255';
            $rules['latitude'] = 'required|numeric|between:-90,90';
            $rules['longitude'] = 'required|numeric|between:-180,180';
        }

        if ($user->role === 'donor') {
            $rules['business_name'] = 'required|string|max:255';
            $rules['phone'] = 'required|string|max:30';
        }

        if ($user->role === 'recipient') {
            $rules['ngo_name'] = 'required|string|max:255';
            $rules['contact_person'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if (isset($data['address'])) {
            $user->address = $data['address'];
            $user->latitude = $data['latitude'];
            $user->longitude = $data['longitude'];
        }

        if ($user->role === 'donor') {
            $user->business_name = $data['business_name'];
            $user->phone = $data['phone'];
        }

        if ($user->role === 'recipient') {
            $user->ngo_name = $data['ngo_name'];
            $user->contact_person = $data['contact_person'];
        }

        $user->save();

        return $this->portalRedirect(
            $request,
            ['user' => $user->fresh()],
            200,
            '/profile',
            'Profile updated.'
        );
    }
}
