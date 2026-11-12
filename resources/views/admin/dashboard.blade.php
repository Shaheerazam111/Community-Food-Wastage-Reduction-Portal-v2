@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title h4 mb-1">Admin dashboard</h1>
        <p class="small text-muted mb-0">Snapshot for {{ $from }} to {{ $to }} (listings created in this range).</p>
    </div>
    <a href="{{ route('admin.reports', ['from' => $from, 'to' => $to]) }}" class="btn btn-brand">Open full reports</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="text-muted small">Listings posted</div>
            <div class="fs-4">{{ $summary['total_listings'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="text-muted small">Delivered</div>
            <div class="fs-4 text-success">{{ $summary['delivered'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="text-muted small">Claim rate</div>
            <div class="fs-4">{{ $summary['claim_rate_pct'] }}%</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="text-muted small">Donors (all)</div>
            <div class="fs-4">{{ $summary['donors_total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="text-muted small">NGOs (all)</div>
            <div class="fs-4">{{ $summary['recipients_total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <div class="text-muted small">Expired</div>
            <div class="fs-4 text-muted">{{ $summary['expired'] }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card page-card h-100">
            <div class="card-header"><strong>By status</strong></div>
            <div class="card-body">
                @forelse ($byStatus as $row)
                    @php
                        $pct = $summary['total_listings'] > 0
                            ? round(($row->total / $summary['total_listings']) * 100, 1)
                            : 0;
                    @endphp
                    <div class="report-bar-row mb-2">
                        <div class="d-flex justify-content-between small mb-1">
                            @php
                                $badge = match ($row->status) {
                                    'AVAILABLE' => 'badge-available',
                                    'CLAIMED' => 'badge-claimed',
                                    'DELIVERED' => 'badge-delivered',
                                    'EXPIRED' => 'badge-expired',
                                    default => 'badge-closed',
                                };
                            @endphp
                            <span><span class="badge {{ $badge }}">{{ $row->status }}</span></span>
                            <span>{{ $row->total }} ({{ $pct }}%)</span>
                        </div>
                        <div class="report-bar-track">
                            <div class="report-bar-fill" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No listings in this period yet.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="card page-card h-100">
            <div class="card-header"><strong>What you can do here</strong></div>
            <div class="card-body small">
                <p>Reports read live data from <code>food_listings</code> and <code>users</code> — same records donors and NGOs create through the portal API.</p>
                <ul class="mb-0">
                    <li>Filter by date range on the <a href="{{ route('admin.reports') }}">Reports</a> page (max {{ \App\Services\AdminReportService::MAX_RANGE_DAYS }} days).</li>
                    <li>See trends, top donors/NGOs, pickup areas, and export CSV for your assignment write-up.</li>
                    <li>Demo accounts (admin, donor, NGO, volunteer) come from <code>php artisan db:seed --class=UserSeeder</code> — not the public register form.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
