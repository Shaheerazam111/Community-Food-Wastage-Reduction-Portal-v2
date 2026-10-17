<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Same controller serves JSON (Postman / test-apis) and HTML (portal forms).
 * JSON when the client sends Accept: application/json; otherwise redirects + flash.
 */
trait HandlesPortalResponses
{
    protected function wantsJson(Request $request): bool
    {
        return $request->expectsJson();
    }

    protected function portalRedirect(Request $request, array $json, int $status, string $url, string $flashMessage)
    {
        if ($this->wantsJson($request)) {
            return response()->json($json, $status);
        }

        return redirect($url)->with('success', $flashMessage);
    }

    protected function portalBack(Request $request, array $json, int $status, string $errorMessage)
    {
        if ($this->wantsJson($request)) {
            return response()->json($json, $status);
        }

        return back()->with('error', $errorMessage);
    }
}
