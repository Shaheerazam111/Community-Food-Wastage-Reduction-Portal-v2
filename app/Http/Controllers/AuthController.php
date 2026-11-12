<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\User;
use App\Models\VerificationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    use HandlesPortalResponses;

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['donor', 'recipient'])],
            'business_name' => 'required_if:role,donor|nullable|string|max:255',
            'phone' => 'required_if:role,donor|nullable|string|max:30',
            'ngo_name' => 'required_if:role,recipient|nullable|string|max:255',
            'contact_person' => 'required_if:role,recipient|nullable|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'verification_document' => 'required_if:role,recipient|nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['verification_status'] = $data['role'] === 'recipient' ? 'pending' : 'approved';
        unset($data['verification_document']);

        $user = User::create($data);

        if ($request->hasFile('verification_document')) {
            $file = $request->file('verification_document');
            $path = $file->store('ngo-documents', 'local');
            VerificationDocument::create([
                'user_id' => $user->id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'status' => 'pending',
            ]);
        }

        Auth::login($user);

        $flash = $user->role === 'recipient'
            ? 'Account created. Your NGO document is pending admin review.'
            : 'Account created. You are now logged in.';

        return $this->portalRedirect(
            $request,
            ['user' => $user],
            201,
            User::homePathForRole($user->role),
            $flash
        );
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials)) {
            return $this->portalBack(
                $request,
                ['message' => 'Invalid credentials'],
                401,
                'Wrong email or password.'
            );
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->is_active === false) {
            Auth::logout();
            $request->session()->invalidate();

            return $this->portalBack(
                $request,
                ['message' => 'Account blocked'],
                403,
                'Your account is blocked. Contact the admin.'
            );
        }

        return $this->portalRedirect(
            $request,
            ['user' => $user],
            200,
            User::homePathForRole($user->role),
            'Welcome back, '.$user->name.'.'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->portalRedirect(
            $request,
            ['message' => 'Logged out'],
            200,
            '/',
            'Logged out.'
        );
    }

}
