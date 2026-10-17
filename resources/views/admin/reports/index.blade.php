@extends('layouts.app')

@section('title', 'Admin Reports')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
@php
    use App\Support\ListingImpact;
@endphp

<div class="page-header">
    <h1 class="page-title h4 mb-0">Impact reports</h1>
    <a href="{{ url('/admin/listings') }}" class="btn btn-outline-secondary">All listings</a>
</div>

<p class="small text-muted">Totals come from live listings in the database (delivered = food that reached an NGO).</p>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">All listings</div>
            <div class="fs-4">{{ $totals['listings_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Delivered</div>
            <div class="fs-4">{{ $totals['delivered_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Meals saved</div>
            <div class="fs-4">{{ ListingImpact::formatAmount($totals['meals_saved']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="text-muted small">Kg saved</div>
            <div class="fs-4">{{ ListingImpact::formatAmount($totals['kg_saved']) }}</div>
        </div>
    </div>
</div>

<div class="card page-card">
    <div class="card-header"><strong>By city</strong></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Listings</th>
                        <th>Delivered</th>
                        <th>Meals saved</th>
                        <th>Kg saved</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cityRows as $row)
                        <tr>
                            <td>{{ $row->city_name }}</td>
                            <td>{{ $row->listings_count }}</td>
                            <td>{{ $row->delivered_count }}</td>
                            <td>{{ ListingImpact::formatAmount((float) $row->meals_saved) }}</td>
                            <td>{{ ListingImpact::formatAmount((float) $row->kg_saved) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted p-3">No listings yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
