@extends('layouts.app')

@section('title', 'Post Food')

@section('nav')
@include('partials.donor_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Post food</h1>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ url('/food') }}" enctype="multipart/form-data" id="donor-listing-form">
            @csrf
            @include('donor.donations._form', ['categories' => $categories, 'defaults' => $defaults])

            @if (! auth()->user()->is_verified)
                <p class="small text-warning mb-2">You must be verified before you can post food.</p>
                <button type="submit" class="btn btn-brand" disabled>Save listing</button>
            @else
                <button type="submit" class="btn btn-brand">Save listing</button>
            @endif
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
@endpush
