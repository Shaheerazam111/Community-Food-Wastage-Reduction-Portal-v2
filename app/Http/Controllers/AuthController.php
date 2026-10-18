<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\User;
use App\Support\RoleDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use HandlesPortalResponses;

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => ['required', Rule::in(['donor', 'recipient'])],
            'business_name' => ['nullable', Rule::requiredIf($request->input('role') === 'donor'), 'string', 'max:255'],
            'phone' => 'nullable|string|max:30',
            'ngo_name' => ['nullable', Rule::requiredIf($request->input('role') === 'recipient'), 'string', 'max:255'],
            'contact_person' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_verified'] = false;
        $data['is_active'] = true;

        $user = User::create($data);

        Auth::login($user);

        return $this->portalRedirect(
            $request,
            ['user' => $user],
            201,
            RoleDashboard::path($user->role),
            'Account created. You are logged in. An admin will verify your account before you can post or claim food.'
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

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->portalBack(
                $request,
                ['message' => 'Account blocked'],
                403,
                'This account is blocked.'
            );
        }

        $request->session()->regenerate();

        return $this->portalRedirect(
            $request,
            ['user' => $user],
            200,
            RoleDashboard::path($user->role),
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
