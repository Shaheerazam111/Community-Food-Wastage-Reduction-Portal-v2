{{-- Rate a DELIVERED claim (one submission per listing). --}}
@php
    $existing = $claim->feedback ?? null;
@endphp

<div class="claim-feedback-panel">
    <h3 class="claim-block-label mb-2">Rate this pickup</h3>

    @if ($existing)
        <p class="mb-1">
            <span class="feedback-stars" aria-label="Rating {{ $existing->rating }} out of 5">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="feedback-star {{ $i <= $existing->rating ? 'is-on' : '' }}">★</span>
                @endfor
            </span>
            <span class="small text-muted ms-1">({{ $existing->rating }}/5)</span>
        </p>
        @if ($existing->comment)
            <p class="small mb-0 text-muted">“{{ $existing->comment }}”</p>
        @else
            <p class="small text-muted mb-0">No comment left.</p>
        @endif
    @else
        <p class="small text-muted">How was the food quality and hygiene? (1 = poor, 5 = excellent)</p>
        <form method="POST" action="{{ url('/food/'.$claim->id.'/feedback') }}" class="feedback-form">
            @csrf
            <div class="mb-3">
                <label for="rating-{{ $claim->id }}" class="form-label">Rating</label>
                <select class="form-select @error('rating') is-invalid @enderror" id="rating-{{ $claim->id }}" name="rating" required>
                    <option value="" disabled @selected(old('rating') === null)>Choose 1–5</option>
                    @for ($score = 5; $score >= 1; $score--)
                        <option value="{{ $score }}" @selected((string) old('rating') === (string) $score)>
                            {{ $score }} — @if ($score >= 4) Good @elseif ($score === 3) OK @else Poor @endif
                        </option>
                    @endfor
                </select>
                @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="comment-{{ $claim->id }}" class="form-label">Comment (optional)</label>
                <textarea class="form-control @error('comment') is-invalid @enderror" id="comment-{{ $claim->id }}" name="comment" rows="2" maxlength="500" placeholder="Short note about quality or hygiene">{{ old('comment') }}</textarea>
                @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-brand btn-sm">Submit feedback</button>
        </form>
    @endif
</div>
