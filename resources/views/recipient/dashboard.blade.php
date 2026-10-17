@extends('layouts.app')

@section('title', 'Recipient Dashboard')

@section('nav')
@include('partials.recipient_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">NGO dashboard</h1>
    <a href="{{ url('/recipient/food') }}" class="btn btn-brand">Find food</a>
</div>

@if (($unreadNotificationCount ?? 0) > 0)
    <div class="alert alert-info d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <span>You have <strong>{{ $unreadNotificationCount }}</strong> unread alert{{ ($unreadNotificationCount ?? 0) === 1 ? '' : 's' }}.</span>
        <a href="{{ url('/notifications') }}" class="btn btn-sm btn-outline-brand">View notifications</a>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Your city</div>
            <div class="fs-5">{{ $user->city ?? '—' }}</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Organization</div>
            <div class="fs-5">{{ $user->ngo_name ?? $user->name }}</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="text-muted small">Food nearby (10 km)</div>
            <div class="fs-5">{{ $nearbyFood->count() }}</div>
        </div>
    </div>
</div>

<div class="card page-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Available food near you</strong>
        <a href="{{ url('/recipient/food') }}" class="small">Search again</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>City</th>
                        <th>Distance (km)</th>
                        <th>Expires</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nearbyFood as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->city }}</td>
                            <td>{{ isset($item->distance_km) ? number_format((float) $item->distance_km, 2) : '—' }}</td>
                            <td class="small">{{ $item->expiry_at }}</td>
                            <td><a href="{{ url('/recipient/food/'.$item->id) }}" class="btn btn-sm btn-outline-secondary">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted p-3">
                                @if ($user->latitude === null || $user->longitude === null)
                                    Set your map pin on <a href="{{ url('/profile') }}">My profile</a> to see nearby food.
                                @else
                                    No available food within 10 km right now.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card page-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Recent claims</strong>
        <a href="{{ url('/food/claims') }}" class="small">View all</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Claimed at</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentClaims as $claim)
                        @php
                            $badge = match ($claim->status) {
                                'CLAIMED' => 'badge-claimed',
                                'DELIVERED' => 'badge-delivered',
                                default => 'badge-closed',
                            };
                        @endphp
                        <tr>
                            <td>{{ $claim->title }}</td>
                            <td><span class="badge badge-status {{ $badge }}">{{ $claim->status }}</span></td>
                            <td class="small">{{ $claim->claimed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-muted p-3">No claims yet. Use Find food to claim surplus near you.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
