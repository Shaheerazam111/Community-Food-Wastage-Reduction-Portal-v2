@extends('layouts.app')

@section('title', 'Post Food')

@section('nav')
<li class="nav-item"><a class="nav-link" href="{{ url('/donor/dashboard') }}">Dashboard</a></li>
<li class="nav-item"><a class="nav-link active" href="{{ url('/donor/donations/create') }}">Post food</a></li>
<li class="nav-item"><a class="nav-link" href="{{ url('/food/mine') }}">My listings</a></li>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Post food</h1>
</div>

<div class="card page-card">
    <div class="card-body">
        <p class="small text-muted mb-3">
            Submits to <strong>POST /food</strong>. Pickup address and coordinates are taken from your donor profile (Task 2).
        </p>
        @auth
        <div class="alert alert-light border small mb-3">
            <strong>Pickup sent with this listing:</strong><br>
            Address: {{ auth()->user()->address ?? '—' }}<br>
            Latitude: {{ auth()->user()->latitude ?? '—' }},
            Longitude: {{ auth()->user()->longitude ?? '—' }}
        </div>
        @endauth
        <form method="POST" action="{{ url('/food') }}">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label required">Food title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. 20 lunch boxes">
            </div>
            <div class="mb-3">
                <label class="form-label required">Category</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="category" id="cat_veg" value="veg" {{ old('category', 'veg') === 'veg' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="cat_veg">Vegetarian</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="category" id="cat_non_veg" value="non_veg" {{ old('category') === 'non_veg' ? 'checked' : '' }}>
                        <label class="form-check-label" for="cat_non_veg">Non-vegetarian</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label required">Quantity</label>
                <input type="text" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}" required placeholder="e.g. 20 boxes">
            </div>
            <div class="mb-3">
                <label for="expiry_at" class="form-label required">Expiry date &amp; time</label>
                <input type="datetime-local" class="form-control" id="expiry_at" name="expiry_at" value="{{ old('expiry_at') }}" required>
            </div>
            <button type="submit" class="btn btn-brand">Save listing</button>
        </form>
    </div>
</div>
@endsection
