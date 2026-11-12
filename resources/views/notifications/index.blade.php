@extends('layouts.app')

@section('title', 'Notifications')

@section('nav')
@include('partials.role_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Notifications</h1>
</div>

<div class="card page-card">
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @forelse ($notifications as $note)
                <li class="list-group-item {{ $note->read_at ? '' : 'bg-light' }}">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <strong>{{ $note->title }}</strong>
                            <p class="small mb-1">{{ $note->body }}</p>
                            <span class="text-muted small">{{ $note->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @if (! $note->read_at)
                            <form method="POST" action="{{ url('/notifications/'.$note->id.'/read') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-brand">Mark read</button>
                            </form>
                        @endif
                    </div>
                </li>
            @empty
                <li class="list-group-item text-muted">No notifications yet.</li>
            @endforelse
        </ul>
    </div>
    @if ($notifications->hasPages())
        <div class="card-footer">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
