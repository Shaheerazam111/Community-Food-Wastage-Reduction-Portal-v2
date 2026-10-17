@php
    $size = $size ?? 48;
@endphp
@if (! empty($path))
    <img src="{{ asset('storage/'.$path) }}" alt="Food photo" class="listing-photo" width="{{ $size }}" height="{{ $size }}" style="object-fit:cover;border-radius:6px;">
@else
    <span class="text-muted small">No photo</span>
@endif
