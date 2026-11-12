@extends('layouts.app')

@section('title', 'Manage users')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">Manage users</h1>
</div>

<p class="small text-muted">Block inactive NGOs or donors. Verification documents are Phase 2 — use active flag for now.</p>

<div class="card page-card">
    <div class="card-body p-0 table-responsive">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            @if ($user->is_active)
                                <span class="badge badge-available">Active</span>
                            @else
                                <span class="badge badge-blocked">Blocked</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="is_active" value="{{ $user->is_active ? '0' : '1' }}">
                                <button type="submit" class="btn btn-sm btn-outline-secondary" data-confirm="Change this user’s active status?">
                                    {{ $user->is_active ? 'Block' : 'Unblock' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
        <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection
