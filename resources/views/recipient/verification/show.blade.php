@extends('layouts.app')

@section('title', 'NGO verification')

@section('nav')
@include('partials.recipient_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">NGO verification</h1>
</div>

<div class="card page-card mb-4">
    <div class="card-body">
        <p class="mb-2">Your account status: <strong>{{ $user->verification_status }}</strong></p>
        @if ($user->verification_status === 'pending')
            <p class="small text-muted mb-0">An admin is reviewing your document. You can browse food but cannot claim until approved.</p>
        @elseif ($user->verification_status === 'rejected')
            <p class="small text-danger mb-0">Upload a clearer registration certificate or NGO letter below.</p>
        @else
            <p class="small text-muted mb-0">You are verified and can claim food.</p>
        @endif
    </div>
</div>

@if ($document)
<div class="card page-card mb-4">
    <div class="card-header"><strong>Latest upload</strong></div>
    <div class="card-body small">
        <div>File: {{ $document->original_name ?? 'document' }}</div>
        <div>Review status: {{ $document->status }}</div>
        @if ($document->admin_note)
            <div>Admin note: {{ $document->admin_note }}</div>
        @endif
    </div>
</div>
@endif

@if ($user->verification_status !== 'approved')
<div class="card page-card">
    <div class="card-body">
        <h2 class="h6">Upload verification document</h2>
        <p class="small text-muted">PDF or image, max 5 MB. Only admins can view this file.</p>
        <form method="POST" action="{{ url('/recipient/verification') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <input type="file" class="form-control" name="verification_document" accept=".pdf,image/*" required>
            </div>
            <button type="submit" class="btn btn-brand">Upload</button>
        </form>
    </div>
</div>
@endif
@endsection
