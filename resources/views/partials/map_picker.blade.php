{{-- Click map to set pickup pin (lat/lng stay hidden) --}}
@php
    $startLat = $latitude ?? 31.5204;
    $startLng = $longitude ?? 74.3587;
    $hasPin = isset($latitude) && isset($longitude) && $latitude !== '' && $longitude !== null;
@endphp
<div class="mb-1">
    <label class="form-label {{ ! empty($required) ? 'required' : '' }}">{{ $label ?? 'Pickup location on map' }}</label>
    <p class="small text-muted mb-2">Click the map to drop a pin for this pickup spot.</p>
</div>
<div
    class="map-embed map-picker mb-2"
    data-map-picker
    data-lat="{{ $hasPin ? $startLat : '' }}"
    data-lng="{{ $hasPin ? $startLng : '' }}"
    data-default-lat="{{ $startLat }}"
    data-default-lng="{{ $startLng }}"
    role="application"
    aria-label="{{ $label ?? 'Pickup location map' }}"
></div>
<input type="hidden" name="latitude" data-map-lat value="{{ $hasPin ? $startLat : '' }}">
<input type="hidden" name="longitude" data-map-lng value="{{ $hasPin ? $startLng : '' }}">
<p class="small text-muted mb-0" data-map-hint>
    @if ($hasPin)
        Pin set — click again to move it.
    @else
        No pin yet — click the map to choose a location.
    @endif
</p>
