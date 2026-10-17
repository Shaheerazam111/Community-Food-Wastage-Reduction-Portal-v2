@extends('layouts.app')

@section('title', 'My Listings')

@section('nav')
@include('partials.donor_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">My food listings <span class="text-muted fs-6">(GET /food/mine)</span></h1>
    <a href="{{ url('/donor/donations/create') }}" class="btn btn-brand">Post food</a>
</div>

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
@endsection
