{{-- Read-only OpenStreetMap embed (no API key) --}}
@php
    $lat = (float) ($latitude ?? 0);
    $lng = (float) ($longitude ?? 0);
    $delta = 0.02;
    $bbox = ($lng - $delta).','.($lat - $delta).','.($lng + $delta).','.($lat + $delta);
    $embedUrl = 'https://www.openstreetmap.org/export/embed.html?bbox='.urlencode($bbox).'&layer=mapnik&marker='.urlencode($lat.','.$lng);
    $openUrl = 'https://www.openstreetmap.org/?mlat='.$lat.'&mlon='.$lng.'#map=16/'.$lat.'/'.$lng;
@endphp
@if ($lat && $lng)
    <div class="map-embed mb-2">
        <iframe title="{{ $label ?? 'Map' }}" src="{{ $embedUrl }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <p class="small mb-0">
        <a href="{{ $openUrl }}" target="_blank" rel="noopener noreferrer">Open in Maps</a>
    </p>
@else
    <p class="small text-muted mb-0">No map pin for this location yet.</p>
@endif
