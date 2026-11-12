@extends('layouts.app')

@section('title', 'NGO verification')

@section('nav')
@include('partials.admin_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">NGO document review</h1>
</div>

<p class="small text-muted">Only admins can download verification files. Approve before the NGO can claim food.</p>

<div class="card page-card">
    <div class="card-body p-0 table-responsive">
        <table class="table table-sm mb-0 align-middle">
            <thead>
                <tr>
                    <th>NGO</th>
                    <th>Email</th>
                    <th>Doc status</th>
                    <th>User status</th>
                    <th>Uploaded</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $doc)
                    <tr>
                        <td>{{ $doc->user->ngo_name ?? $doc->user->name }}</td>
                        <td class="small">{{ $doc->user->email }}</td>
                        <td>{{ $doc->status }}</td>
                        <td>{{ $doc->user->verification_status }}</td>
                        <td class="small">{{ $doc->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.verification.download', $doc->id) }}">Download</a>
                            @if ($doc->status === 'pending')
                                <form method="POST" action="{{ route('admin.verification.update', $doc->id) }}" class="d-inline-flex gap-1 flex-wrap justify-content-end mt-1">
                                    @csrf
                                    <input type="text" name="admin_note" class="form-control form-control-sm" placeholder="Note (optional)" style="max-width: 10rem;">
                                    <button type="submit" name="action" value="approve" class="btn btn-sm btn-brand">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger" data-confirm="Reject this NGO document?">Reject</button>
                                </form>
                            @else
                                <span class="small text-muted d-block">{{ $doc->admin_note ?: '—' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted p-3">No documents uploaded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($documents->hasPages())
        <div class="card-footer">{{ $documents->links() }}</div>
    @endif
</div>
@endsection
