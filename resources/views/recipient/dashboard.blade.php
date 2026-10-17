@extends('layouts.app')

@section('title', 'Recipient Dashboard')

@section('nav')
@include('partials.recipient_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">NGO dashboard</h1>
    <a href="{{ url('/recipient/food') }}" class="btn btn-brand">Find food nearby</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="stat-card">
            <div class="text-muted small">Your city</div>
            <div class="fs-5">{{ $user->city ?? '—' }}</div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="stat-card">
            <div class="text-muted small">Organization</div>
            <div class="fs-5">{{ $user->ngo_name ?? $user->name }}</div>
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
                        <tr>
                            <td>{{ $claim->title }}</td>
                            @php
                                $badge = match ($claim->status) {
                                    'CLAIMED' => 'badge-claimed',
                                    'DELIVERED' => 'badge-delivered',
                                    default => 'badge-closed',
                                };
                            @endphp
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
