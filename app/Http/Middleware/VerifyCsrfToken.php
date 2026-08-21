<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    // This is a JSON API tested from Postman, not a browser form, so there is
    // no CSRF-carrying HTML page to steal a token from - CSRF checks are
    // skipped for these routes and only the session cookie is used.
    protected $except = [
        'auth/*',
        'food',
        'food/*',
    ];
}
