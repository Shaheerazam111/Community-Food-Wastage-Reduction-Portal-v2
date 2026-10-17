@extends('layouts.app')

@section('title', 'Edit Listing')

@section('nav')
@include('partials.donor_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Edit listing</h1>
    <a href="{{ url('/food/mine') }}" class="btn btn-outline-secondary btn-sm">Back to my listings</a>
</div>

<div class="card page-card">
    <div class="card-body">
        <form method="POST" action="{{ url('/food/'.$listing->id) }}" enctype="multipart/form-data">
            @csrf
            @include('donor.donations._form', [
                'categories' => $categories,
                'defaults' => $defaults,
                'listing' => $listing,
            ])
            <button type="submit" class="btn btn-brand">Update listing</button>
        </form>

        <form method="POST" action="{{ url('/food/'.$listing->id.'/cancel') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm" data-confirm="Cancel this listing? It will be removed from search.">Cancel listing</button>
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
