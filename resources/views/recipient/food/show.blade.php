@extends('layouts.app')

@section('title', $listing->title)

@section('nav')
@include('partials.recipient_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">{{ $listing->title }}</h1>
    <a href="{{ url('/recipient/food') }}" class="btn btn-outline-secondary btn-sm">Back to search</a>
</div>

<div class="row g-3">
    <div class="col-12 col-md-5">
        @include('partials.listing_photo', ['path' => $listing->photo_path, 'size' => 200])
        @if (! $listing->photo_path)
            <p class="small text-muted">No photo for this listing.</p>
        @endif
    </div>
    <div class="col-12 col-md-7">
        <div class="card page-card">
            <div class="card-body">
                <dl class="row small mb-0">
                    <dt class="col-sm-4">Category</dt>
                    <dd class="col-sm-8">{{ $listing->foodCategory->name ?? '—' }}</dd>
                    <dt class="col-sm-4">Quantity</dt>
                    <dd class="col-sm-8">{{ $listing->quantity_display }}</dd>
                    <dt class="col-sm-4">Best before</dt>
                    <dd class="col-sm-8">{{ $listing->expiry_at->format('Y-m-d H:i') }}</dd>
                    <dt class="col-sm-4">City</dt>
                    <dd class="col-sm-8">{{ $listing->city }}</dd>
                    <dt class="col-sm-4">Pickup</dt>
                    <dd class="col-sm-8">{{ $listing->pickup_address }}</dd>
                    <dt class="col-sm-4">Donor</dt>
                    <dd class="col-sm-8">{{ $listing->donor->business_name ?? $listing->donor->name }}</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $listing->donor->phone ?? '—' }}</dd>
                </dl>
                @if ($listing->description)
                    <p class="mt-3 mb-0"><strong>Description:</strong> {{ $listing->description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12">
        <details class="ui-disclose" open>
            <summary>Pickup map</summary>
            <div class="ui-disclose-body pt-3">
                @include('partials.map_embed', [
                    'latitude' => $listing->latitude,
                    'longitude' => $listing->longitude,
                    'label' => 'Pickup map',
                ])
            </div>
        </details>
    </div>
    <div class="col-12">
        @if (auth()->user()->is_verified && auth()->user()->is_active)
            <form method="POST" action="{{ url('/food/'.$listing->id.'/claim') }}">
                @csrf
                <button type="submit" class="btn btn-brand" data-confirm="Claim this food? You can see pickup details under My claims.">Claim this food</button>
            </form>
        @else
            <p class="text-warning small mb-0">Verify your account before you can claim food.</p>
        @endif
    </div>
</div>
@endsection
