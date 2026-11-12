@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="auth-box-wide">
    <div class="card page-card">
        <div class="card-body">
            <h1 class="page-title h4">Register</h1>
            <p class="small text-muted">Form posts to <strong>POST /auth/register</strong> (same as Postman).</p>
            <p class="form-hint"><span class="text-danger">*</span> Required fields</p>

            <form method="POST" action="{{ url('/auth/register') }}" id="registerForm" enctype="multipart/form-data">
                @csrf

                <p class="form-section-title">Account</p>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label required">Full name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label required">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <div class="form-text">At least 6 characters.</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label required">Phone</label>
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
                </div>

                <div id="donorFields" class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="business_name" class="form-label required">Business name</label>
                        <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name') }}">
                    </div>
                </div>

                <div id="recipientFields" class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="ngo_name" class="form-label required">NGO name</label>
                        <input type="text" class="form-control" id="ngo_name" name="ngo_name" value="{{ old('ngo_name') }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="contact_person" class="form-label required">Contact person</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                    </div>
                    <div class="col-12">
                        <label for="verification_document" class="form-label required">Verification document</label>
                        <input type="file" class="form-control" id="verification_document" name="verification_document" accept=".pdf,image/*">
                        <div class="form-text">PDF or photo of NGO registration (admin reviews before you can claim).</div>
                    </div>
                </div>

                <p class="form-section-title">Your location (used for pickup / search)</p>
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label for="address" class="form-label required">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" placeholder="Street, area, city" required>
                    </div>
                    <div class="col-12">
                        @include('partials.map_picker', ['label' => 'Pin on map', 'required' => true])
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

    function syncRoleSections() {
        var isDonor = document.getElementById('role_donor').checked;
        donorFields.hidden = !isDonor;
        recipientFields.hidden = isDonor;
        document.getElementById('business_name').required = isDonor;
        document.getElementById('phone').required = isDonor;
        document.getElementById('ngo_name').required = !isDonor;
        document.getElementById('contact_person').required = !isDonor;
        var docInput = document.getElementById('verification_document');
        if (docInput) {
            docInput.required = !isDonor;
        }
    }

    document.getElementById('registerForm').addEventListener('submit', function (e) {
        var lat = document.querySelector('[data-map-lat]');
        var lng = document.querySelector('[data-map-lng]');
        if (!lat || !lng || !lat.value || !lng.value) {
            e.preventDefault();
            window.alert('Please click the map to set your pickup / search location.');
        }
    });

    document.querySelectorAll('input[name="role"]').forEach(function (radio) {
        radio.addEventListener('change', syncRoleSections);
    });
    syncRoleSections();
})();
</script>
@endpush
