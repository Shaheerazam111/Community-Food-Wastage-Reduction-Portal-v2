{{-- Leaflet map with pins for search results --}}
@php
    $pins = collect($listings ?? [])->map(function ($listing) {
        return [
            'id' => $listing->id,
            'title' => $listing->title,
            'lat' => (float) $listing->latitude,
            'lng' => (float) $listing->longitude,
        ];
    })->filter(fn ($p) => $p['lat'] && $p['lng'])->values();
    $centerLat = $centerLat ?? ($pins->first()['lat'] ?? 31.5204);
    $centerLng = $centerLng ?? ($pins->first()['lng'] ?? 74.3587);
@endphp

@if ($pins->isNotEmpty())
    <div
        class="map-embed mb-2"
        data-search-results-map
        data-lat="{{ $centerLat }}"
        data-lng="{{ $centerLng }}"
        data-pins='@json($pins)'
        role="application"
        aria-label="Food listings on map"
    ></div>
    <p class="small text-muted mb-0">Tap a pin, then open the listing from the table below.</p>
@endif
