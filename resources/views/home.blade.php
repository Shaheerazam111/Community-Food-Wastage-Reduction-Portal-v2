@extends('layouts.guest')

@section('title', 'Home')

@section('content')
<section class="home-hero text-center text-md-start">
    <div class="row align-items-center g-4">
        <div class="col-12 col-lg-7">
            <p class="brand-name mb-1">Food Portal</p>
            <h1 class="h3 mb-3">Community Food Wastage Reduction</h1>
            <p class="text-muted mb-4 mb-lg-0">
                Connect surplus food with NGOs and shelters nearby.
                Log in to open your dashboard — donors post food, recipients search and claim, admins verify accounts and view reports.
            </p>
        </div>
        <div class="col-12 col-lg-5">
            <div class="d-grid gap-2">
                <a href="{{ url('/login') }}" class="btn btn-brand btn-lg">Log in to dashboard</a>
                <a href="{{ url('/register') }}" class="btn btn-outline-brand btn-lg">Create account</a>
            </div>
            <p class="small text-muted mt-3 mb-0">
                New donor or NGO? Register, upload proof on your profile, then wait for admin approval before posting or claiming.
            </p>
        </div>
    </div>
</section>

<section class="mb-4">
    <h2 class="h5 mb-3">How it works</h2>
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="home-step">
                <span class="step-num">1</span>
                <h3 class="h6 mb-1">Register &amp; get verified</h3>
                <p class="small text-muted mb-0">Sign up as a donor or NGO, set your city and map pin, and upload a short verification document.</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="home-step">
                <span class="step-num">2</span>
                <h3 class="h6 mb-1">Post or find food</h3>
                <p class="small text-muted mb-0">Donors post from their dashboard. NGOs search by GPS, profile pin, or city and claim available listings.</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="home-step">
                <span class="step-num">3</span>
                <h3 class="h6 mb-1">Pickup &amp; confirm</h3>
                <p class="small text-muted mb-0">Either side marks the food delivered after pickup. The NGO can leave a quick rating for the donor.</p>
            </div>
        </div>
    </div>
</section>

@endsection
