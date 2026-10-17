@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="auth-box-wide">
    <div class="card page-card">
        <div class="card-body">
            <h1 class="page-title h4">Register</h1>
            <p class="small text-muted">Create a donor or NGO account. Admin accounts are created by the system.</p>
            <p class="form-hint"><span class="text-danger">*</span> Required fields</p>

            <form method="POST" action="{{ url('/auth/register') }}" id="registerForm">
                @csrf

                <p class="form-section-title">Account</p>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label required">Full name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label required">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="6">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">At least 6 characters.</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label required">Confirm password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="6">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                    </div>
                </div>

                <p class="form-section-title">I am a…</p>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="role" id="role_donor" value="donor" {{ old('role', 'donor') === 'donor' ? 'checked' : '' }}>
                        <label class="form-check-label" for="role_donor">Donor (business)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="role" id="role_recipient" value="recipient" {{ old('role') === 'recipient' ? 'checked' : '' }}>
                        <label class="form-check-label" for="role_recipient">Recipient (NGO)</label>
                    </div>
                    @error('role')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div id="donorFields" class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="business_name" class="form-label required">Business name</label>
                        <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name') }}">
                        @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div id="recipientFields" class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="ngo_name" class="form-label required">NGO name</label>
                        <input type="text" class="form-control @error('ngo_name') is-invalid @enderror" id="ngo_name" name="ngo_name" value="{{ old('ngo_name') }}">
                        @error('ngo_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="contact_person" class="form-label">Contact person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                    </div>
                </div>

                <p class="form-section-title">Your location (used for pickup / search)</p>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="city" class="form-label required">City</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required placeholder="e.g. Lahore">
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="address" class="form-label required">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required placeholder="Street, area">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        @include('partials.map_picker', ['label' => 'Pin on map', 'required' => true])
                        @error('latitude')<div class="text-danger small">{{ $message }}</div>@enderror
                        @error('longitude')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-brand">Create account</button>
            </form>
            <p class="mt-3 mb-0 small">Already registered? <a href="{{ url('/login') }}">Login</a></p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function () {
    var donorFields = document.getElementById('donorFields');
    var recipientFields = document.getElementById('recipientFields');
    var businessInput = document.getElementById('business_name');
    var ngoInput = document.getElementById('ngo_name');

    function syncRoleSections() {
        var isDonor = document.getElementById('role_donor').checked;
        donorFields.hidden = !isDonor;
        recipientFields.hidden = isDonor;
        if (businessInput) {
            businessInput.required = isDonor;
        }
        if (ngoInput) {
            ngoInput.required = !isDonor;
        }
    }

    document.querySelectorAll('input[name="role"]').forEach(function (radio) {
        radio.addEventListener('change', syncRoleSections);
    });
    syncRoleSections();
})();
</script>
@endpush
