@extends('layouts.app')

@section('title', 'My Listings')

@section('nav')
<li class="nav-item"><a class="nav-link" href="{{ url('/donor/dashboard') }}">Dashboard</a></li>
<li class="nav-item"><a class="nav-link" href="{{ url('/donor/donations/create') }}">Post food</a></li>
<li class="nav-item"><a class="nav-link active" href="{{ url('/food/mine') }}">My listings</a></li>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">My food listings <span class="text-muted fs-6">(GET /food/mine)</span></h1>
    <a href="{{ url('/donor/donations/create') }}" class="btn btn-brand">Post food</a>
</div>

<div class="card page-card">
    <div class="card-body p-0">
        @include('partials.listings_table', ['listings' => $listings, 'showDeliverButton' => true, 'showClaimInfo' => true])
    </div>
</div>
@endsection
