<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPortalResponses;
use App\Models\User;
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
            'business_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'ngo_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        Auth::login($user);

        return $this->portalRedirect(
            $request,
            ['user' => $user],
            201,
            $this->homeForRole($user->role),
            'Account created. You are now logged in.'
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

        return $this->portalRedirect(
            $request,
            ['user' => $user],
            200,
            $this->homeForRole($user->role),
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

    private function homeForRole(string $role): string
    {
        if ($role === 'admin') {
            return '/admin/dashboard';
        }

        if ($role === 'donor') {
            return '/donor/dashboard';
        }

        return '/recipient/food';
    }
}
