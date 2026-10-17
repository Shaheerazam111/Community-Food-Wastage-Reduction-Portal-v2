@extends('layouts.guest')

@section('title', 'Home')

@section('content')
<section class="home-hero text-center text-md-start">
    <div class="row align-items-center g-4">
        <div class="col-12 col-lg-7">
            <p class="brand-name mb-1">Food Portal</p>
            <h1 class="h3 mb-3">Community Food Wastage Reduction</h1>
            <p class="text-muted mb-4 mb-lg-0">
                Donors post surplus food. NGOs search nearby and claim it.
                Donors mark food as delivered when pickup is done.
            </p>
        </div>
        <div class="col-12 col-lg-5">
            <div class="d-grid gap-2">
                <a href="{{ url('/login') }}" class="btn btn-brand btn-lg">Login</a>
                <a href="{{ url('/register') }}" class="btn btn-outline-brand btn-lg">Create account</a>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <h2 class="h5 mb-3">Assignment flow (demo for teacher)</h2>
    <div class="card page-card">
        <div class="card-body small">
            <ol class="mb-0 ps-3">
                <li class="mb-2"><strong>Task 1 — Auth:</strong> Register donor + recipient (<code>POST /auth/register</code>), login (<code>POST /auth/login</code>).</li>
                <li class="mb-2"><strong>Task 2 — Donor:</strong> Post food (<code>POST /food</code>) → status <code>AVAILABLE</code>. View <code>GET /food/mine</code>. After NGO claims, mark <code>POST /food/{id}/deliver</code> → <code>DELIVERED</code>.</li>
                <li class="mb-2"><strong>Task 3 — Search:</strong> Recipient uses current location → <code>GET /food/search</code> (radius km, closest first, hides claimed/expired).</li>
                <li class="mb-2"><strong>Task 4 — Claim:</strong> <code>POST /food/{id}/claim</code> → then <code>GET /food/claims</code> for pickup details. Second claim returns <strong>409</strong> (Challenge 2).</li>
                <li><strong>Challenge 3 — Expiry:</strong> Search/claim check <code>expiry_at</code>; hourly job <code>php artisan food:expire</code> marks old rows <code>EXPIRED</code>.</li>
            </ol>
        </div>
    </div>
</section>

<section class="mb-4">
    <h2 class="h5 mb-3">How it works</h2>
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="home-step">
                <span class="step-num">1</span>
                <h3 class="h6 mb-1">Donor registers &amp; posts food</h3>
                <p class="small text-muted mb-0">AVAILABLE — pickup from donor profile.</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="home-step">
                <span class="step-num">2</span>
                <h3 class="h6 mb-1">NGO searches &amp; claims</h3>
                <p class="small text-muted mb-0">CLAIMED — see My claims for donor contact.</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="home-step">
                <span class="step-num">3</span>
                <h3 class="h6 mb-1">Donor marks delivered</h3>
                <p class="small text-muted mb-0">DELIVERED — full lifecycle complete.</p>
            </div>
        </div>
    </div>
</section>

<p class="small text-muted text-center mb-0">
    API playground: <a href="{{ url('/test-apis') }}">/test-apis</a>
</p>
@endsection
