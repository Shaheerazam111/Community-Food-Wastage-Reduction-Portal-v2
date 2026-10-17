@extends('layouts.app')

@section('title', 'All Listings')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">All food listings</h1>
    <a href="{{ url('/admin/reports') }}" class="btn btn-outline-secondary">Reports</a>
</div>

<p class="small text-muted mb-3">Read-only view. Filter by status or city.</p>

<div class="card page-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ url('/admin/listings') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach (['AVAILABLE', 'CLAIMED', 'DELIVERED', 'EXPIRED', 'CANCELLED'] as $statusOption)
                        <option value="{{ $statusOption }}" @selected(($filters['status'] ?? '') === $statusOption)>{{ $statusOption }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label for="city" class="form-label">City</label>
                <select class="form-select" id="city" name="city">
                    <option value="">All cities</option>
                    @foreach ($cities as $cityName)
                        <option value="{{ $cityName }}" @selected(($filters['city'] ?? '') === $cityName)>{{ $cityName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-brand">Filter</button>
                <a href="{{ url('/admin/listings') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card page-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Donor</th>
                        <th>City</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Claimed by</th>
                        <th>Posted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listings as $listing)
                        @php
                            $badge = match ($listing->status) {
                                'AVAILABLE' => 'badge-available',
                                'CLAIMED' => 'badge-claimed',
                                'DELIVERED' => 'badge-delivered',
                                'EXPIRED' => 'badge-expired',
                                default => 'badge-closed',
                            };
                            $donor = $listing->donor;
                            $claimer = $listing->claimedBy;
                        @endphp
                        <tr>
                            <td>{{ $listing->title }}</td>
                            <td class="small">{{ $donor->business_name ?? $donor->name ?? '—' }}</td>
                            <td>{{ $listing->city ?? '—' }}</td>
                            <td class="small">{{ $listing->quantity_display }}</td>
                            <td><span class="badge badge-status {{ $badge }}">{{ $listing->status }}</span></td>
                            <td class="small">{{ $claimer->ngo_name ?? $claimer->name ?? '—' }}</td>
                            <td class="small text-muted">{{ $listing->created_at?->format('Y-m-d') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted p-3">No listings match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $listings->links() }}</div>
@endsection
