@extends('layouts.app')

@section('title', 'Notifications')

@section('nav')
@if (auth()->user()->role === 'donor')
    @include('partials.donor_nav')
@elseif (auth()->user()->role === 'recipient')
    @include('partials.recipient_nav')
@elseif (auth()->user()->role === 'admin')
    @include('partials.admin_nav')
@endif
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Notifications</h1>
    @if ($notifications->total() > 0 && ($unreadNotificationCount ?? 0) > 0)
        <form method="POST" action="{{ url('/notifications/read-all') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">Mark all as read</button>
        </form>
    @endif
</div>

@if ($notifications->isEmpty())
    <div class="alert alert-info mb-0">
        No notifications yet. Alerts appear here when food is posted in your city, claimed, or marked delivered.
    </div>
@else
    <div class="card page-card">
        <div class="list-group list-group-flush">
            @foreach ($notifications as $note)
                <div class="list-group-item {{ $note->is_read ? '' : 'notification-unread' }}">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-2">
                        <div>
                            <p class="mb-1 {{ $note->is_read ? 'text-muted' : 'mb-1' }}">{{ $note->message }}</p>
                            <span class="small text-muted">{{ $note->created_at->format('M j, Y · g:i A') }}</span>
                        </div>
                        @if (! $note->is_read)
                            <form method="POST" action="{{ url('/notifications/'.$note->id.'/read') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-brand">Mark read</button>
                            </form>
                        @else
                            <span class="badge badge-status badge-delivered">Read</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
@endif
@endsection
