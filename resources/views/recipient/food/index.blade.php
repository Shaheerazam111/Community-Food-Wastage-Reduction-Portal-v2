@extends('layouts.app')

@section('title', 'Find Food')

@section('nav')
@include('partials.recipient_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Find food nearby</h1>
</div>

@auth
    @if (auth()->user()->role === 'recipient' && auth()->user()->verification_status !== 'approved')
        <div class="alert alert-warning">
            NGO verification is <strong>{{ auth()->user()->verification_status }}</strong>.
            You can search food but cannot claim until an admin approves your document.
            <a href="{{ url('/recipient/verification') }}">View verification status</a>
        </div>
    @endif
@endauth

<div class="card page-card mb-4">
    <div class="card-body">
        <p class="small text-muted mb-3">
            Search uses <strong>GET /food/search</strong> with your location and radius (km).
        </p>

        <form method="GET" action="{{ url('/food/search') }}" id="food-search-form" class="row g-3 align-items-end">
            <input type="hidden" id="latitude" name="latitude" value="{{ $latitude }}">
            <input type="hidden" id="longitude" name="longitude" value="{{ $longitude }}">

            <div class="col-12 col-md-4">
                <label for="city" class="form-label">Or search by city</label>
                <input type="text" class="form-control" id="city" name="city" value="{{ $city ?? '' }}" placeholder="e.g. Lahore">
                <div class="form-text">City search ignores radius; leave blank to use map/GPS.</div>
            </div>
            <div class="col-12 col-md-4">
                <label for="radius_km" class="form-label">Radius (km)</label>
                <input type="number" step="0.1" min="0.1" max="100" class="form-control" id="radius_km" name="radius_km" value="{{ $radius_km }}">
            </div>
            <div class="col-12 col-md-8 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-brand" id="btn-current-location">
                    Use my current location &amp; search
                </button>
                @if ($latitude !== null && $longitude !== null)
                    <button type="submit" class="btn btn-outline-secondary" id="btn-profile-location">
                        Search using profile pin
                    </button>
                @endif
                <button type="submit" class="btn btn-outline-secondary" id="btn-city-search">
                    Search by city
                </button>
            </div>
        </form>

        <p class="small text-muted mt-3 mb-0" id="location-status">
            @if (! empty($searched) && $latitude !== null && $longitude !== null)
                Last search: {{ $latitude }}, {{ $longitude }}
            @else
                Allow location access when the browser asks, then we search automatically.
            @endif
        </p>

        <details class="mt-3">
            <summary class="small text-muted">Advanced: enter latitude / longitude manually</summary>
            <div class="row g-3 mt-2">
                <div class="col-12 col-md-4">
                    <label for="latitude_manual" class="form-label">Latitude</label>
                    <input type="number" step="any" class="form-control" id="latitude_manual" value="{{ $latitude }}">
                </div>
                <div class="col-12 col-md-4">
                    <label for="longitude_manual" class="form-label">Longitude</label>
                    <input type="number" step="any" class="form-control" id="longitude_manual" value="{{ $longitude }}">
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary w-100" id="btn-manual-search">Search with these coordinates</button>
                </div>
            </div>
        </details>
    </div>
</div>

@if (empty($searched))
    <div class="alert alert-info">Tap <strong>Use my current location &amp; search</strong>, use your <strong>profile pin</strong>, or type a city and press <strong>Search by city</strong>.</div>
@endif

@if (! empty($searched) && $listings->isEmpty() && ! empty($pendingNearby))
    <div class="alert alert-warning">
        No approved food in this area, but <strong>{{ $pendingNearby }}</strong> listing(s) are waiting for <strong>admin approval</strong> and are hidden from search until approved.
    </div>
@elseif (! empty($searched) && $listings->isEmpty())
    <div class="alert alert-secondary mb-0">
        Nothing matched. Try a larger radius, another city, or ask the donor to post again. New posts need admin approval in production.
    </div>
@endif

<div class="card page-card">
    <div class="card-header"><strong>Available food</strong></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Qty</th>
                        <th>Pickup</th>
                        <th>Expires</th>
                        <th>Distance (km)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listings as $listing)
                        <tr>
                            <td>{{ $listing->title }}</td>
                            <td>{{ $listing->quantity }}</td>
                            <td class="small">{{ $listing->pickup_address }}</td>
                            <td class="small">{{ $listing->expiry_at }}</td>
                            <td>
                                @if (isset($listing->distance_km))
                                    {{ number_format((float) $listing->distance_km, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if (auth()->user()->verification_status === 'approved')
                                    <form method="POST" action="{{ url('/food/'.$listing->id.'/claim') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-brand" data-confirm="Claim this food?">Claim</button>
                                    </form>
                                @else
                                    <span class="small text-muted">Verify NGO first</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted p-3">
                                @if (! empty($searched))
                                    No available food in this area.
                                @else
                                    Run a search to see results.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('food-search-form');
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var statusEl = document.getElementById('location-status');
    var btnCurrent = document.getElementById('btn-current-location');
    var btnManual = document.getElementById('btn-manual-search');
    var latManual = document.getElementById('latitude_manual');
    var lngManual = document.getElementById('longitude_manual');

    function setStatus(text) {
        if (statusEl) {
            statusEl.textContent = text;
        }
    }

    function submitWithCoords(lat, lng) {
        latInput.value = lat;
        lngInput.value = lng;
        if (latManual) {
            latManual.value = lat;
        }
        if (lngManual) {
            lngManual.value = lng;
        }
        setStatus('Searching near ' + lat + ', ' + lng + '…');
        form.submit();
    }

    if (btnCurrent) {
        btnCurrent.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Your browser does not support location. Use the manual coordinates below.');
                return;
            }

            btnCurrent.disabled = true;
            setStatus('Waiting for GPS / location permission…');

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    submitWithCoords(
                        pos.coords.latitude.toFixed(6),
                        pos.coords.longitude.toFixed(6)
                    );
                },
                function (err) {
                    btnCurrent.disabled = false;
                    setStatus('Location failed. Try again or use manual coordinates.');
                    alert('Could not get your location: ' + err.message);
                },
                { enableHighAccuracy: true, timeout: 20000, maximumAge: 60000 }
            );
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
            submitWithCoords(lat, lng);
        });
    }

    form.addEventListener('submit', function (e) {
        var cityInput = document.getElementById('city');
        var city = cityInput ? cityInput.value.trim() : '';
        if (city !== '') {
            latInput.value = '';
            lngInput.value = '';
            return;
        }
        if (latInput.value === '' || lngInput.value === '') {
            e.preventDefault();
            alert('Set a location first (GPS, profile pin, manual coordinates, or search by city name).');
        }
    });
})();
</script>
@endpush
