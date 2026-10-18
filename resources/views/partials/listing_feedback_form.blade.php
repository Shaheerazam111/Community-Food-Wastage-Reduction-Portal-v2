{{-- Rate a DELIVERED claim. Form stays closed until the user opens it. --}}
@php
    $existing = $claim->feedback ?? null;
    $openFeedback = (string) old('feedback_listing_id') === (string) $claim->id;
@endphp

<div class="claim-feedback-panel">
    @if ($existing)
        <h3 class="claim-block-label mb-2">Your rating</h3>
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
        <details class="ui-disclose" @if ($openFeedback) open @endif>
            <summary>Rate this pickup</summary>
            <div class="ui-disclose-body">
                <p class="small text-muted mt-3">How was the food quality and hygiene? 1 is poor, 5 is excellent.</p>
                <form method="POST" action="{{ url('/food/'.$claim->id.'/feedback') }}" class="feedback-form">
                    @csrf
                    <input type="hidden" name="feedback_listing_id" value="{{ $claim->id }}">
                    <div class="mb-3">
                        <label for="rating-{{ $claim->id }}" class="form-label">Rating</label>
                        <select class="form-select @error('rating') is-invalid @enderror" id="rating-{{ $claim->id }}" name="rating" required>
                            <option value="" disabled @selected(old('rating') === null || ! $openFeedback)>Choose 1–5</option>
                            @for ($score = 5; $score >= 1; $score--)
                                <option value="{{ $score }}" @selected($openFeedback && (string) old('rating') === (string) $score)>
                                    {{ $score }} — @if ($score >= 4) Good @elseif ($score === 3) OK @else Poor @endif
                                </option>
                            @endfor
                        </select>
                        @if ($openFeedback)
                            @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="comment-{{ $claim->id }}" class="form-label">Comment (optional)</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" id="comment-{{ $claim->id }}" name="comment" rows="2" maxlength="500" placeholder="Short note about quality or hygiene">{{ $openFeedback ? old('comment') : '' }}</textarea>
                        @if ($openFeedback)
                            @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>
                    <button type="submit" class="btn btn-brand btn-sm">Submit feedback</button>
                </form>
            </div>
        </details>
    @endif
</div>
