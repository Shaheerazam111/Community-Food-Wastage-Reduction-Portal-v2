<?php

namespace App\Support;

use App\Models\User;

class RoleDashboard
{
    /** Dashboard path for donor, recipient, or admin. */
    public static function path(string $role): string
    {
        return match ($role) {
            'donor' => '/donor/dashboard',
            'admin' => '/admin/dashboard',
            default => '/recipient/dashboard',
        };
    }

    /** Full URL for the user’s role dashboard, or home when logged out. */
    public static function urlFor(?User $user): string
    {
        if ($user === null) {
            return url('/');
        }

        return url(self::path($user->role));
    }
}
