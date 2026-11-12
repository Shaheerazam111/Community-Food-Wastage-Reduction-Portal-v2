<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class UseRequestRootUrl
{
    // Keep generated links on same host:port as the browser (local dev)
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('local')) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        return $next($request);
    }
}
