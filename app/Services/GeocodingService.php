<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public function reverse(float $lat, float $lng): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'CFWRP-v2-ApiTester/1.0 (local dev)',
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat' => $lat,
            'lon' => $lng,
            'accept-language' => 'en',
            'zoom' => 18,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Geocoding failed');
        }

        $geo = $response->json();

        return [
            'latitude' => $lat,
            'longitude' => $lng,
            'area_name' => $this->areaName($geo),
            'display_name' => $geo['display_name'] ?? null,
        ];
    }

    private function areaName(array $geo): string
    {
        $addr = $geo['address'] ?? [];
        $parts = array_filter([
            $addr['neighbourhood'] ?? null,
            $addr['suburb'] ?? null,
            $addr['quarter'] ?? null,
            $addr['city_district'] ?? null,
            $addr['district'] ?? null,
            $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? null,
            $addr['state'] ?? null,
            $addr['country'] ?? null,
        ]);

        if ($parts) {
            return implode(', ', array_unique($parts));
        }

        return $geo['display_name'] ?? 'Unknown area';
    }
}
