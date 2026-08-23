<?php

namespace App\Http\Controllers;

use App\Services\ApiTestSnapshotService;
use App\Services\GeocodingService;
use App\Support\ApiTestCatalog;
use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    public function index()
    {
        return view('test-apis', [
            'apiGroups' => ApiTestCatalog::groups(),
        ]);
    }

    public function snapshot(ApiTestSnapshotService $snapshotService)
    {
        return response()->json($snapshotService->build());
    }

    public function reverseGeocode(Request $request, GeocodingService $geocodingService)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        try {
            return response()->json($geocodingService->reverse((float) $data['lat'], (float) $data['lng']));
        } catch (\RuntimeException) {
            return response()->json(['message' => 'Geocoding failed'], 502);
        }
    }
}
