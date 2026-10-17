@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Admin dashboard</h1>
    <a href="{{ url('/admin/users') }}" class="btn btn-brand">Manage users</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Total users</div>
            <div class="fs-4">{{ $stats['users'] }}</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Donors</div>
            <div class="fs-4">{{ $stats['donors'] }}</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Recipients (NGOs)</div>
            <div class="fs-4">{{ $stats['recipients'] }}</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="stat-card">
            <div class="text-muted small">Food listings</div>
            <div class="fs-4">{{ $stats['listings'] }}</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="stat-card">
            <div class="text-muted small">Waiting for verification</div>
            <div class="fs-4">{{ $stats['pending_verification'] }}</div>
        </div>
    </div>
</div>
@endsection
