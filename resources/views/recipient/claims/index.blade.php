@extends('layouts.app')

@section('title', 'My Claims')

@section('nav')
@include('partials.recipient_nav')
@endsection

@section('content')
@php
    $activeCount = $claims->where('status', 'CLAIMED')->count();
    $deliveredCount = $claims->where('status', 'DELIVERED')->count();
@endphp

<div class="page-header">
    <h1 class="page-title h4 mb-0">My claims</h1>
    <a href="{{ url('/recipient/food') }}" class="btn btn-brand">Find more food</a>
</div>

@if ($claims->isEmpty())
    <div class="claim-empty page-card">
        <div class="claim-empty-icon" aria-hidden="true"></div>
        <h2 class="h5 mb-2">No claims yet</h2>
        <p class="text-muted small mb-3">
            Search nearby listings, open one you like, and press <strong>Claim</strong>.
            Pickup details and the donor phone number will show up here.
        </p>
        <a href="{{ url('/recipient/food') }}" class="btn btn-brand">Find food</a>
    </div>
@else
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="text-muted small">Awaiting pickup</div>
                <div class="fs-4 fw-semibold">{{ $activeCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card">
                <div class="text-muted small">Delivered</div>
                <div class="fs-4 fw-semibold">{{ $deliveredCount }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card claim-hint">
                <div class="text-muted small">How it works</div>
                <p class="small mb-0">
                    Status stays <strong>CLAIMED</strong> until you or the donor marks <strong>DELIVERED</strong>.
                    Claimed food is hidden from search.
                </p>
            </div>
        </div>
    </div>

    <div class="claim-list">
        @foreach ($claims as $claim)
            @php
                $badge = match ($claim->status) {
                    'CLAIMED' => 'badge-claimed',
                    'DELIVERED' => 'badge-delivered',
                    'EXPIRED' => 'badge-expired',
                    default => 'badge-closed',
                };
                $donor = $claim->donor;
                $donorLabel = $donor ? ($donor->business_name ?? $donor->name) : '—';
                $expiryText = $claim->expiry_at instanceof \DateTimeInterface
                    ? $claim->expiry_at->format('M j, Y · g:i A')
                    : ($claim->expiry_at ?? '—');
                $claimedText = $claim->claimed_at?->format('M j, Y · g:i A') ?? '—';
            @endphp
            <article class="claim-card page-card">
                <div class="claim-card-head">
                    <div class="claim-card-thumb">
                        @include('partials.listing_photo', ['path' => $claim->photo_path ?? null, 'size' => 72])
                    </div>
                    <div class="claim-card-title-wrap">
                        <h2 class="claim-card-title h6 mb-1">{{ $claim->title }}</h2>
                        <div class="claim-card-meta text-muted small">
                            <span>{{ $claim->quantity_display }}</span>
                            @if ($claim->city)
                                <span class="claim-meta-dot" aria-hidden="true">·</span>
                                <span>{{ $claim->city }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="badge badge-status {{ $badge }} claim-card-badge">{{ $claim->status }}</span>
                </div>

                <div class="row g-3 claim-card-body">
                    <div class="col-12 col-lg-5">
                        <div class="claim-detail-block">
                            <h3 class="claim-block-label">Pickup</h3>
                            <p class="claim-block-value mb-0">{{ $claim->pickup_address }}</p>
                            <p class="small text-muted mb-0 mt-1">Claimed {{ $claimedText }}</p>
                            @if ($claim->status === 'CLAIMED')
                                <p class="small text-muted mb-0">Best before {{ $expiryText }}</p>
                            @endif
                        </div>

                        <div class="claim-contact-panel">
                            <h3 class="claim-block-label mb-2">Donor contact</h3>
                            <p class="mb-2 fw-medium">{{ $donorLabel }}</p>
                            @if ($donor?->phone)
                                <a href="tel:{{ $donor->phone }}" class="btn btn-outline-brand btn-sm w-100">
                                    Call {{ $donor->phone }}
                                </a>
                            @else
                                <p class="small text-muted mb-0">No phone on file.</p>
                            @endif
                            @if ($donor?->address)
                                <p class="small text-muted mb-0 mt-2">{{ $donor->address }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-lg-7">
                        <details class="ui-disclose">
                            <summary>Show pickup map</summary>
                            <div class="ui-disclose-body pt-3">
                                @include('partials.map_embed', [
                                    'latitude' => $claim->latitude,
                                    'longitude' => $claim->longitude,
                                    'label' => 'Pickup map for '.$claim->title,
                                ])
                            </div>
                        </details>
                    </div>
                </div>

                @if ($claim->status === 'CLAIMED')
                    <div class="claim-card-foot">
                        <p class="small text-muted mb-2 mb-md-0">
                            After pickup, confirm so the donor knows the food was collected.
                        </p>
                        <form method="POST" action="{{ url('/food/'.$claim->id.'/deliver') }}" class="claim-deliver-form">
                            @csrf
                            <button type="submit" class="btn btn-brand" data-confirm="Mark this food as picked up (DELIVERED)?">
                                Mark as delivered
                            </button>
                        </form>
                    </div>
                @elseif ($claim->status === 'DELIVERED')
                    <div class="claim-card-foot claim-card-foot-stack">
                        @if ($claim->delivered_at)
                            <span class="small text-muted d-block mb-3">
                                Delivered {{ $claim->delivered_at->format('M j, Y · g:i A') }}
                            </span>
                        @endif
                        @include('partials.listing_feedback_form', ['claim' => $claim])
                    </div>
                @endif
            </article>
        @endforeach
    </div>
@endif
@endsection
