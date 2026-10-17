@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Admin dashboard</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ url('/admin/reports') }}" class="btn btn-brand">Reports</a>
        <a href="{{ url('/admin/users') }}" class="btn btn-outline-secondary">Manage users</a>
    </div>
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
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">All listings</div>
            <div class="fs-4">{{ $stats['listings'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Available</div>
            <div class="fs-4">{{ $stats['listings_available'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Claimed</div>
            <div class="fs-4">{{ $stats['listings_claimed'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Delivered</div>
            <div class="fs-4">{{ $stats['listings_delivered'] }}</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="stat-card">
            <div class="text-muted small">Waiting for verification</div>
            <div class="fs-4">{{ $stats['pending_verification'] }}</div>
        </div>
    </div>
</div>

<div class="card page-card">
    <div class="card-header"><strong>Recent feedback</strong></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Listing</th>
                        <th>NGO</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentFeedback as $row)
                        <tr>
                            <td>{{ $row->foodListing->title ?? '—' }}</td>
                            <td class="small">{{ $row->recipient->ngo_name ?? $row->recipient->name ?? '—' }}</td>
                            <td>{{ $row->rating }}/5</td>
                            <td class="small text-muted">{{ $row->comment ? \Illuminate\Support\Str::limit($row->comment, 80) : '—' }}</td>
                            <td class="small text-muted">{{ $row->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted p-3">No feedback submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
