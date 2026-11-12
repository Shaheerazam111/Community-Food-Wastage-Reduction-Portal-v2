@extends('layouts.app')

@section('title', 'Moderate listings')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Moderate food listings</h1>
</div>

<p class="small text-muted">New donor posts stay <strong>PENDING</strong> until approved. Only approved listings appear in NGO search.</p>

<div class="card page-card mb-4">
    <div class="card-header"><strong>Pending approval</strong></div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Donor</th>
                    <th>Qty</th>
                    <th>Expires</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pending as $listing)
                    <tr>
                        <td>{{ $listing->title }}</td>
                        <td class="small">{{ $listing->donor->business_name ?? $listing->donor->name }}</td>
                        <td>{{ $listing->quantity }}</td>
                        <td class="small">{{ $listing->expiry_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.listings.update', $listing->id) }}" class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                @csrf
                                <input type="text" name="rejection_reason" class="form-control form-control-sm" placeholder="Reject reason" style="max-width: 12rem;">
                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-brand">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger" data-confirm="Reject this listing?">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted p-3">No listings waiting for review.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card page-card">
    <div class="card-header"><strong>Recently moderated</strong></div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recent as $listing)
                    <tr>
                        <td>{{ $listing->title }}</td>
                        <td>{{ $listing->moderation_status }}</td>
                        <td class="small">{{ $listing->rejection_reason ?? '—' }}</td>
                        <td class="small">{{ $listing->moderated_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted p-3">No history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
