@extends('layouts.app')

@section('title', 'Donor Dashboard')

@section('nav')
@include('partials.donor_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Donor dashboard</h1>
    <a href="{{ url('/donor/donations/create') }}" class="btn btn-brand">Post food</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Available</div>
            <div class="fs-4">{{ $stats['available'] }}</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Claimed</div>
            <div class="fs-4">{{ $stats['claimed'] }}</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Delivered</div>
            <div class="fs-4">{{ $stats['delivered'] }}</div>
        </div>
    </div>
</div>

<div class="card page-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Recent listings</strong>
        <a href="{{ url('/food/mine') }}" class="small">View all (GET /food/mine)</a>
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
