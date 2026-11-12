@extends('layouts.app')

@section('title', 'My Claims')

@section('nav')
@include('partials.recipient_nav')
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title h4 mb-0">My claimed food <span class="text-muted fs-6">(GET /food/claims)</span></h1>
    <a href="{{ url('/recipient/food') }}" class="btn btn-outline-secondary">Find more food</a>
</div>

<p class="small text-muted">
    After you claim, status is <strong>CLAIMED</strong> until the donor marks <strong>DELIVERED</strong>.
    Claimed items disappear from search (Task 3 — not shown when already claimed).
</p>

<div class="card page-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Claimed at</th>
                        <th>Pickup</th>
                        <th>Donor</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($claims as $claim)
                        @php
                            $badge = match ($claim->status) {
                                'CLAIMED' => 'badge-claimed',
                                'DELIVERED' => 'badge-delivered',
                                'EXPIRED' => 'badge-expired',
                                default => 'badge-closed',
                            };
                            $donor = $claim->donor;
                        @endphp
                        <tr>
                            <td>{{ $claim->title }}</td>
                            <td>{{ $claim->quantity }}</td>
                            <td><span class="badge badge-status {{ $badge }}">{{ $claim->status }}</span></td>
                            <td class="small">{{ $claim->claimed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="small">
                                {{ $claim->pickup_address }}<br>
                                <span class="text-muted">{{ $claim->latitude }}, {{ $claim->longitude }}</span>
                            </td>
                            <td class="small">
                                @if ($donor)
                                    {{ $donor->business_name ?? $donor->name }}<br>
                                    @if ($donor->phone)
                                        {{ $donor->phone }}<br>
                                    @endif
                                    {{ $donor->address }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">
                                @php
                                    $myFeedback = $claim->feedbacks->where('recipient_id', auth()->id())->first();
                                @endphp
                                @if ($claim->status === 'DELIVERED' && ! $myFeedback)
                                    <form method="POST" action="{{ url('/food/'.$claim->id.'/feedback') }}" class="d-flex flex-column gap-1">
                                        @csrf
                                        <select name="rating" class="form-select form-select-sm" required>
                                            <option value="">Rating</option>
                                            @for ($r = 5; $r >= 1; $r--)
                                                <option value="{{ $r }}">{{ $r }} stars</option>
                                            @endfor
                                        </select>
                                        <input type="text" name="comment" class="form-control form-control-sm" placeholder="Short comment">
                                        <button type="submit" class="btn btn-sm btn-outline-brand">Submit</button>
                                    </form>
                                @elseif ($myFeedback)
                                    {{ $myFeedback->rating }}/5 — {{ $myFeedback->comment ?: 'No comment' }}
                                @else
                                    <span class="text-muted">After delivery</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted p-3">
                                No claims yet. Use <a href="{{ url('/recipient/food') }}">Find food</a> and press Claim.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
