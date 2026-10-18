@extends('layouts.app')

@section('title', 'Manage Users')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Manage users</h1>
    <a href="{{ url('/admin/dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to dashboard</a>
</div>

<p class="small text-muted">Approve or reject donor and NGO accounts. Block users who should not use the portal.</p>

<div class="card page-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>City</th>
                        <th>Verified</th>
                        <th>Active</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $doc = $user->verificationDocument;
                        @endphp
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td class="small">{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->city }}</td>
                            <td>{{ $user->is_verified ? 'Yes' : 'No' }}</td>
                            <td>{{ $user->is_active ? 'Yes' : 'No' }}</td>
                            <td class="small">
                                @if ($doc)
                                    <a href="{{ url('/admin/users/'.$user->id.'/document') }}" target="_blank" rel="noopener">View</a>
                                    <span class="text-muted">({{ $doc->status }})</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    @if (! $user->is_verified)
                                        <form method="POST" action="{{ url('/admin/users/'.$user->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-brand">Approve</button>
                                        </form>
                                    @endif
                                    @if ($user->is_active)
                                        <form method="POST" action="{{ url('/admin/users/'.$user->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="block">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Block this user?">Block</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ url('/admin/users/'.$user->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="unblock">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Unblock</button>
                                        </form>
                                    @endif
                                </div>
                                <details class="ui-disclose">
                                    <summary>Reject with a note</summary>
                                    <div class="ui-disclose-body">
                                        <form method="POST" action="{{ url('/admin/users/'.$user->id) }}" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <label class="form-label" for="note-{{ $user->id }}">Note (optional)</label>
                                            <input type="text" id="note-{{ $user->id }}" name="admin_note" class="form-control form-control-sm mb-2" placeholder="Why this was rejected">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Reject</button>
                                        </form>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted p-3">No donor or NGO users yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
