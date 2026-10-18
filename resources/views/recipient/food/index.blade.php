@extends('layouts.app')

@section('title', 'Find Food')

@section('nav')
@include('partials.recipient_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Find food</h1>
</div>

<div class="card page-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ url('/food/search') }}" id="food-search-form" class="row g-3 align-items-end">
            <input type="hidden" name="search_mode" id="search_mode" value="{{ $search_mode ?? 'nearby' }}">
            <input type="hidden" id="latitude" name="latitude" value="{{ $latitude }}">
            <input type="hidden" id="longitude" name="longitude" value="{{ $longitude }}">

            <div class="col-12 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-brand" id="btn-current-location">Use GPS &amp; search</button>
                @if ($latitude !== null && $longitude !== null)
                    <button type="button" class="btn btn-outline-secondary" id="btn-profile-location">Use profile pin</button>
                @endif
                <button type="button" class="btn btn-outline-secondary" id="btn-city-search">Search by city</button>
            </div>
        </form>

        <details class="ui-disclose mt-3" id="search-options" @if (! empty($searched)) open @endif>
            <summary>Search options</summary>
            <div class="ui-disclose-body">
                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6">
                        <label for="radius_km" class="form-label">Radius (km)</label>
                        <input type="number" step="0.1" min="0.1" max="100" class="form-control" id="radius_km" name="radius_km" form="food-search-form" value="{{ $radius_km ?? 10 }}">
                        <p class="form-text mb-0">Used with GPS or your profile pin. Default is 10 km.</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="city" class="form-label">City</label>
                        <select class="form-select" id="city" name="city" form="food-search-form">
                            <option value="">Choose a city</option>
                            @foreach ($listing_cities ?? [] as $cityName)
                                <option value="{{ $cityName }}" @selected(($city ?? '') === $cityName)>{{ $cityName }}</option>
                            @endforeach
                        </select>
                        <p class="form-text mb-0">Then press Search by city.</p>
                    </div>
                </div>
            </div>
        </details>

        <p class="small text-muted mt-3 mb-0" id="location-status">
            @if (! empty($searched))
                @if (($search_mode ?? 'nearby') === 'city')
                    Showing available food in <strong>{{ $city }}</strong>.
                @elseif ($latitude !== null && $longitude !== null)
                    Nearby search from {{ $latitude }}, {{ $longitude }} ({{ $radius_km ?? 10 }} km).
                @endif
            @else
                Search by GPS, your profile pin, or pick a city with available listings.
            @endif
        </p>

        <details class="ui-disclose mt-3">
            <summary>Enter coordinates yourself</summary>
            <div class="ui-disclose-body">
            <div class="row g-3 mt-1">
                <div class="col-12 col-md-4">
                    <label for="latitude_manual" class="form-label">Latitude</label>
                    <input type="number" step="any" class="form-control" id="latitude_manual" value="{{ $latitude }}">
                </div>
                <div class="col-12 col-md-4">
                    <label for="longitude_manual" class="form-label">Longitude</label>
                    <input type="number" step="any" class="form-control" id="longitude_manual" value="{{ $longitude }}">
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100" id="btn-manual-search">Search nearby with these coordinates</button>
                </div>
            </div>
            </div>
        </details>
    </div>
</div>

@if (empty($searched))
    <div class="alert alert-info">Use <strong>Use GPS &amp; search</strong>, your profile pin, or <strong>Search by city</strong>. Radius and city are under Search options.</div>
@endif

@if (! empty($searched))
@if (count($listings))
    <div class="card page-card mb-3">
        <div class="card-header"><strong>Map view</strong></div>
        <div class="card-body">
            @include('partials.search_results_map', [
                'listings' => $listings,
                'centerLat' => $latitude,
                'centerLng' => $longitude,
            ])
        </div>
    </div>
@endif

<div class="card page-card">
    <div class="card-header"><strong>Available food</strong></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Qty</th>
                        <th>City</th>
                        <th>Pickup</th>
                        <th>Expires</th>
                        <th>Distance</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listings as $listing)
                        @php
                            $qty = '—';
                            if (isset($listing->quantity_value)) {
                                $unit = ($listing->quantity_unit ?? '') === 'kg' ? 'kg' : 'servings';
                                $qty = rtrim(rtrim(number_format((float) $listing->quantity_value, 2, '.', ''), '0'), '.').' '.$unit;
                            }
                        @endphp
                        <tr>
                            <td>@include('partials.listing_photo', ['path' => $listing->photo_path ?? null, 'size' => 40])</td>
                            <td>{{ $listing->title }}</td>
                            <td>{{ $qty }}</td>
                            <td>{{ $listing->city ?? '—' }}</td>
                            <td class="small">{{ $listing->pickup_address }}</td>
                            <td class="small">{{ $listing->expiry_at }}</td>
                            <td>
                                @if (isset($listing->distance_km))
                                    {{ number_format((float) $listing->distance_km, 2) }} km
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('/recipient/food/'.$listing->id) }}" class="btn btn-sm btn-brand">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted p-3">
                                @if (($search_mode ?? 'nearby') === 'city')
                                    No available food in this city right now.
                                @else
                                    No food nearby yet. Try a wider radius or search by city.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function () {
    var form = document.getElementById('food-search-form');
    var modeInput = document.getElementById('search_mode');
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var citySelect = document.getElementById('city');
    var statusEl = document.getElementById('location-status');
    var btnCurrent = document.getElementById('btn-current-location');
    var btnProfile = document.getElementById('btn-profile-location');
    var btnCity = document.getElementById('btn-city-search');
    var btnManual = document.getElementById('btn-manual-search');
    var latManual = document.getElementById('latitude_manual');
    var lngManual = document.getElementById('longitude_manual');

    function setStatus(text) {
        if (statusEl) {
            statusEl.textContent = text;
        }
    }

    function submitNearby(lat, lng) {
        modeInput.value = 'nearby';
        latInput.value = lat;
        lngInput.value = lng;
        form.submit();
    }

    if (btnCurrent) {
        btnCurrent.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Your browser does not support location. Use manual coordinates or search by city.');
                return;
            }
            btnCurrent.disabled = true;
            setStatus('Waiting for GPS…');
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    submitNearby(pos.coords.latitude.toFixed(6), pos.coords.longitude.toFixed(6));
                },
                function (err) {
                    btnCurrent.disabled = false;
                    alert('Could not get your location: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 20000, maximumAge: 60000 }
            );
        });
    }

    if (btnProfile) {
        btnProfile.addEventListener('click', function () {
            modeInput.value = 'nearby';
            form.submit();
        });
    }

    if (btnCity) {
        btnCity.addEventListener('click', function () {
            if (!citySelect.value) {
                var options = document.getElementById('search-options');
                if (options) {
                    options.open = true;
                }
                alert('Open Search options and choose a city first.');
                return;
            }
            modeInput.value = 'city';
            form.submit();
        });
    }

    if (btnManual) {
        btnManual.addEventListener('click', function () {
            var lat = latManual.value.trim();
            var lng = lngManual.value.trim();
            if (lat === '' || lng === '') {
                alert('Enter both latitude and longitude.');
                return;
            }
            submitNearby(lat, lng);
        });
    }

    form.addEventListener('submit', function (e) {
        if (modeInput.value === 'city') {
            if (!citySelect.value) {
                e.preventDefault();
                alert('Choose a city for city search.');
            }
            return;
        }
        if (latInput.value === '' || lngInput.value === '') {
            e.preventDefault();
            alert('Set a location for nearby search (GPS, profile pin, or manual coordinates).');
        }
    });

    var mapBox = document.querySelector('[data-search-results-map]');
    if (mapBox && typeof L !== 'undefined') {
        var pins = JSON.parse(mapBox.getAttribute('data-pins') || '[]');
        var lat = parseFloat(mapBox.getAttribute('data-lat')) || 31.5204;
        var lng = parseFloat(mapBox.getAttribute('data-lng')) || 74.3587;
        var map = L.map(mapBox).setView([lat, lng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);
        pins.forEach(function (pin) {
            L.marker([pin.lat, pin.lng]).addTo(map).bindPopup('<a href="/recipient/food/' + pin.id + '">' + pin.title + '</a>');
        });
        setTimeout(function () { map.invalidateSize(); }, 150);
    }
})();
</script>
@endpush
