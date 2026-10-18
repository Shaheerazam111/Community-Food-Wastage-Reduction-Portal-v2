@extends('layouts.app')

@section('title', 'My Listings')

@section('nav')
@include('partials.donor_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">My food listings</h1>
    <a href="{{ url('/donor/donations/create') }}" class="btn btn-brand">Post food</a>
</div>

@if ($listings->isEmpty())
    <div class="claim-empty page-card">
        <div class="claim-empty-icon" aria-hidden="true"></div>
        <h2 class="h5 mb-2">No listings posted yet</h2>
        <p class="text-muted small mb-3">
            Post surplus food with a photo, quantity, and pickup pin. NGOs in your city get a dashboard alert.
        </p>
        <a href="{{ url('/donor/donations/create') }}" class="btn btn-brand">Post food</a>
    </div>
@else
    <div class="card page-card">
        <div class="card-body p-0">
            @include('partials.listings_table', [
                'listings' => $listings,
                'showDeliverButton' => true,
                'showClaimInfo' => true,
                'showManageActions' => true,
            ])
        </div>
    </div>
@endif
@endsection
