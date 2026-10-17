@extends('layouts.app')

@section('title', 'Donor Dashboard')

@section('nav')
@include('partials.donor_nav')
@endsection

@section('content')
@php
    use App\Support\ListingImpact;
@endphp

<div class="page-header">
    <h1 class="page-title h4 mb-0">Donor dashboard</h1>
    <a href="{{ url('/donor/donations/create') }}" class="btn btn-brand">Post food</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-6">
        <div class="stat-card">
            <div class="text-muted small">Meals saved (delivered)</div>
            <div class="fs-4">{{ ListingImpact::formatAmount($stats['meals_saved']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-6">
        <div class="stat-card">
            <div class="text-muted small">Kg saved (delivered)</div>
            <div class="fs-4">{{ ListingImpact::formatAmount($stats['kg_saved']) }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Available</div>
            <div class="fs-4">{{ $stats['available'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Claimed</div>
            <div class="fs-4">{{ $stats['claimed'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Delivered</div>
            <div class="fs-4">{{ $stats['delivered'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Average rating</div>
            <div class="fs-4">
                @if ($stats['rating_avg'] !== null)
                    {{ $stats['rating_avg'] }}/5
                    <span class="fs-6 text-muted">({{ $stats['rating_count'] }})</span>
                @else
                    —
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card page-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Recent listings</strong>
        <a href="{{ url('/food/mine') }}" class="small">View all</a>
    </div>
    <div class="card-body p-0">
        @include('partials.listings_table', [
            'listings' => $listings,
            'showDeliverButton' => true,
            'showClaimInfo' => true,
            'showManageActions' => true,
        ])
    </div>
</div>
@endsection
