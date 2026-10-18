@extends('layouts.app')

@section('title', 'My Profile')

@section('nav')
@if (auth()->user()->role === 'donor')
    @include('partials.donor_nav')
@else
    @include('partials.recipient_nav')
@endif
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">My profile</h1>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card page-card">
            <div class="card-body">
                <h2 class="h6 mb-2">Your details</h2>
                <dl class="row small mb-3">
                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">{{ $user->name }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $user->phone ?: '—' }}</dd>
                    <dt class="col-sm-4">City</dt>
                    <dd class="col-sm-8">{{ $user->city ?: '—' }}</dd>
                    <dt class="col-sm-4">Address</dt>
                    <dd class="col-sm-8">{{ $user->address ?: '—' }}</dd>
                </dl>

                <details class="ui-disclose" @if ($errors->hasAny(['name', 'phone', 'business_name', 'ngo_name', 'contact_person', 'city', 'address', 'latitude', 'longitude'])) open @endif>
                    <summary>Edit profile</summary>
                    <div class="ui-disclose-body">
                <p class="small text-muted mt-3">
                    Email and role stay the same. New listings use your city and map pin as defaults.
                </p>

                <form method="POST" action="{{ url('/profile') }}">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label required">Full name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Verified</label>
                            <input type="text" class="form-control" value="{{ $user->is_verified ? 'Yes' : 'No' }}" disabled>
                        </div>

                        @if ($user->role === 'donor')
                            <div class="col-12 col-md-6">
                                <label for="business_name" class="form-label required">Business name</label>
                                <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', $user->business_name) }}" required>
                                @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @else
                            <div class="col-12 col-md-6">
                                <label for="ngo_name" class="form-label required">NGO name</label>
                                <input type="text" class="form-control @error('ngo_name') is-invalid @enderror" id="ngo_name" name="ngo_name" value="{{ old('ngo_name', $user->ngo_name) }}" required>
                                @error('ngo_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="contact_person" class="form-label">Contact person</label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person', $user->contact_person) }}">
                                @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endif

                        <div class="col-12 col-md-6">
                            <label for="city" class="form-label required">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $user->city) }}" required>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label required">Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}" required>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            @include('partials.map_picker', [
                                'label' => 'Map pin',
                                'required' => true,
                                'latitude' => old('latitude', $user->latitude),
                                'longitude' => old('longitude', $user->longitude),
                            ])
                            @error('latitude')<div class="text-danger small">{{ $message }}</div>@enderror
                            @error('longitude')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand">Save profile</button>
                </form>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card page-card h-100">
            <div class="card-body">
                <h2 class="h6">Verification document</h2>
                <p class="small text-muted">
                    Upload one file (PDF, JPG, or PNG, max 2 MB). Admin will review it before you can post or claim food.
                </p>

                @php $doc = $user->verificationDocument; @endphp
                @if ($doc)
                    <p class="small mb-2">
                        <strong>Current file:</strong> {{ $doc->original_name ?? 'Uploaded document' }}<br>
                        <strong>Status:</strong>
                        <span class="badge badge-status {{ $doc->status === 'approved' ? 'badge-delivered' : ($doc->status === 'rejected' ? 'badge-expired' : 'badge-claimed') }}">
                            {{ ucfirst($doc->status) }}
                        </span>
                    </p>
                    @if ($doc->admin_note)
                        <p class="small text-muted">Admin note: {{ $doc->admin_note }}</p>
                    @endif
                @else
                    <p class="small text-muted mb-2">No document uploaded yet.</p>
                @endif

                <details class="ui-disclose" @if ($errors->has('document')) open @endif>
                    <summary>{{ $doc ? 'Replace document' : 'Upload document' }}</summary>
                    <div class="ui-disclose-body">
                        <form method="POST" action="{{ url('/profile/document') }}" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <div class="mb-3">
                                <label for="document" class="form-label required">Choose file</label>
                                <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-outline-secondary">Upload document</button>
                        </form>
                    </div>
                </details>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
@endpush
