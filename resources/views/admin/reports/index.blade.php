@extends('layouts.app')

@section('title', 'Admin Reports')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title h4 mb-1">Reports &amp; analytics</h1>
        <p class="small text-muted mb-0">Food listings created between {{ $from }} and {{ $to }}.</p>
    </div>
    <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}" class="btn btn-outline-brand">Download CSV</a>
</div>

<div class="card page-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label for="from" class="form-label">From</label>
                <input type="date" class="form-control" id="from" name="from" value="{{ $from }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label for="to" class="form-label">To</label>
                <input type="date" class="form-control" id="to" name="to" value="{{ $to }}" required>
            </div>
            <div class="col-12 col-md-4 d-grid d-md-block">
                <button type="submit" class="btn btn-brand">Update report</button>
            </div>
        </form>
        <p class="form-text mb-0 mt-2">If the range is too long, the start date is trimmed to {{ \App\Services\AdminReportService::MAX_RANGE_DAYS }} days.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">Total posted</div>
            <div class="fs-4">{{ $summary['total_listings'] }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">Delivered</div>
            <div class="fs-4">{{ $summary['delivered'] }}</div>
            <div class="small text-muted">Delivery rate (of claimed+delivered): {{ $summary['delivery_rate_pct'] }}%</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">Claim rate</div>
            <div class="fs-4">{{ $summary['claim_rate_pct'] }}%</div>
            <div class="small text-muted">{{ $summary['claimed'] }} still claimed · {{ $summary['available'] }} available</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="text-muted small">New users in range</div>
            <div class="fs-5">{{ $summary['donors_new'] }} donors · {{ $summary['recipients_new'] }} NGOs</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-xl-8">
        <div class="card page-card">
            <div class="card-header"><strong>Listings per day</strong></div>
            <div class="card-body">
                @if ($dailyTrend->isEmpty())
                    <p class="text-muted small mb-0">No listings in this date range.</p>
                @else
                    <div class="report-trend" aria-hidden="true">
                        @foreach ($dailyTrend as $day)
                            @php
                                $h = max(4, round(($day->total / $maxTrend) * 100));
                            @endphp
                            <div class="report-trend-col" title="{{ $day->day }}: {{ $day->total }}">
                                <div class="report-trend-bar" style="height: {{ $h }}%"></div>
                                <span class="report-trend-label">{{ \Illuminate\Support\Str::substr($day->day, 5) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card page-card mb-3">
            <div class="card-header"><strong>By category</strong></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($byCategory as $row)
                            <tr>
                                <td>{{ $row->category === 'veg' ? 'Vegetarian' : 'Non-veg' }}</td>
                                <td class="text-end">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted small p-3">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card page-card">
            <div class="card-header"><strong>By status</strong></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($byStatus as $row)
                            <tr>
                                @php
                                    $badge = match ($row->status) {
                                        'AVAILABLE' => 'badge-available',
                                        'CLAIMED' => 'badge-claimed',
                                        'DELIVERED' => 'badge-delivered',
                                        'EXPIRED' => 'badge-expired',
                                        default => 'badge-closed',
                                    };
                                @endphp
                                <td><span class="badge {{ $badge }}">{{ $row->status }}</span></td>
                                <td class="text-end">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted small p-3">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card page-card">
            <div class="card-header"><strong>Top pickup areas</strong></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Address</th>
                            <th class="text-end">Listings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byArea as $row)
                            <tr>
                                <td class="small">{{ $row->pickup_address }}</td>
                                <td class="text-end">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted small p-3">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card page-card">
            <div class="card-header"><strong>Top donors</strong></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th class="text-end">Posted</th>
                            <th class="text-end">Delivered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topDonors as $row)
                            <tr>
                                <td class="small">{{ $row->business_name ?: $row->name }}</td>
                                <td class="text-end">{{ $row->listings }}</td>
                                <td class="text-end">{{ $row->delivered }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted small p-3">No donors in range</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card page-card">
            <div class="card-header"><strong>Top NGOs (by claims)</strong></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>NGO</th>
                            <th class="text-end">Claims</th>
                            <th class="text-end">Delivered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topRecipients as $row)
                            <tr>
                                <td class="small">{{ $row->ngo_name ?: $row->name }}</td>
                                <td class="text-end">{{ $row->claims }}</td>
                                <td class="text-end">{{ $row->delivered }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted small p-3">No claims in range</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card page-card">
            <div class="card-header"><strong>Recent listings</strong></div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Donor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentListings as $row)
                            <tr>
                                <td class="small">{{ $row->title }}</td>
                                @php
                                    $badge = match ($row->status) {
                                        'AVAILABLE' => 'badge-available',
                                        'CLAIMED' => 'badge-claimed',
                                        'DELIVERED' => 'badge-delivered',
                                        'EXPIRED' => 'badge-expired',
                                        default => 'badge-closed',
                                    };
                                @endphp
                                <td><span class="badge {{ $badge }}">{{ $row->status }}</span></td>
                                <td class="small">{{ $row->donor_name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted small p-3">No listings</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
