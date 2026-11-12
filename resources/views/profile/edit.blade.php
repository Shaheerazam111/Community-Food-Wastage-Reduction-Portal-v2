@extends('layouts.app')

@section('title', 'My profile')

@section('nav')
@include('partials.role_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">My profile</h1>
</div>

<div class="card page-card">
    <div class="card-body">
        <p class="small text-muted">Updates your account and pickup/search location. Role: <strong>{{ $user->role }}</strong> (cannot change here).</p>

        <form method="POST" action="{{ url('/profile') }}" id="profileForm">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label required">Full name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            @if ($user->role === 'donor')
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="business_name" class="form-label required">Business name</label>
                        <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $user->business_name) }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label required">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                    </div>
                </div>
            @endif

            @if ($user->role === 'recipient')
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="ngo_name" class="form-label required">NGO name</label>
                        <input type="text" class="form-control" id="ngo_name" name="ngo_name" value="{{ old('ngo_name', $user->ngo_name) }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="contact_person" class="form-label required">Contact person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $user->contact_person) }}" required>
                    </div>
                </div>
                <p class="small text-muted">NGO verification status: <strong>{{ $user->verification_status }}</strong>.
                    @if ($user->verification_status !== 'approved')
                        <a href="{{ url('/recipient/verification') }}">Upload or check documents</a>
                    @endif
                </p>
            @endif

            @if (in_array($user->role, ['donor', 'recipient', 'volunteer'], true))
                <p class="form-section-title">Location</p>
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label for="address" class="form-label required">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address) }}" required>
                    </div>
                    <div class="col-12">
                        @include('partials.map_picker', [
                            'label' => 'Pin on map',
                            'required' => true,
                            'latitude' => old('latitude', $user->latitude),
                            'longitude' => old('longitude', $user->longitude),
                        ])
                    </div>
                </div>
            @endif

            <p class="form-section-title">Change password (optional)</p>
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label for="password" class="form-label">New password</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="6" autocomplete="new-password">
                </div>
                <div class="col-12 col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm new password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="6" autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn btn-brand">Save profile</button>
        </form>
    </div>
</div>
@endsection

@if (in_array($user->role, ['donor', 'recipient', 'volunteer'], true))
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.getElementById('profileForm').addEventListener('submit', function (e) {
    var lat = document.querySelector('[data-map-lat]');
    var lng = document.querySelector('[data-map-lng]');
    if (lat && lng && (!lat.value || !lng.value)) {
        e.preventDefault();
        window.alert('Please click the map to set your location.');
    }
});
</script>
@endpush
@endif
