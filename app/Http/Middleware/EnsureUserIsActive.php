<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->is_active === false) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect('/login')->with('error', 'Your account is blocked. Contact the admin.');
        }

        return $next($request);
    }
}
