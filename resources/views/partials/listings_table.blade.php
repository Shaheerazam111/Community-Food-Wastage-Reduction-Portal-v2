{{-- Donor listing table. $listings = FoodListing models. --}}
<div class="table-responsive">
    <table class="table table-striped mb-0 align-middle">
        <thead>
            <tr>
                <th></th>
                <th>Title</th>
                <th>Category</th>
                <th>Quantity</th>
                @if (! empty($showClaimInfo))
                    <th>Pickup</th>
                @endif
                <th>Expires</th>
                <th>Status</th>
                @if (! empty($showClaimInfo))
                    <th>Claimed by</th>
                @endif
                @if (! empty($showManageActions))
                    <th></th>
                @endif
                @if (! empty($showDeliverButton))
                    <th></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($listings as $listing)
                @php
                    $status = $listing->status;
                    $badge = match ($status) {
                        'AVAILABLE' => 'badge-available',
                        'CLAIMED' => 'badge-claimed',
                        'DELIVERED' => 'badge-delivered',
                        'EXPIRED' => 'badge-expired',
                        'CANCELLED' => 'badge-closed',
                        default => 'badge-closed',
                    };
                    $expiry = $listing->expiry_at;
                    $expiryText = $expiry instanceof \DateTimeInterface ? $expiry->format('Y-m-d H:i') : (string) $expiry;
                    $claimer = $listing->relationLoaded('claimedBy') ? $listing->claimedBy : null;
                    $categoryName = $listing->relationLoaded('foodCategory') && $listing->foodCategory
                        ? $listing->foodCategory->name
                        : '—';
                    $qty = $listing instanceof \App\Models\FoodListing
                        ? $listing->quantity_display
                        : '—';
                @endphp
                <tr>
                    <td>@include('partials.listing_photo', ['path' => $listing->photo_path ?? null, 'size' => 40])</td>
                    <td>{{ $listing->title }}</td>
                    <td>{{ $categoryName }}</td>
                    <td>{{ $qty }}</td>
                    @if (! empty($showClaimInfo))
                        <td class="small">{{ $listing->pickup_address }}</td>
                    @endif
                    <td>{{ $expiryText }}</td>
                    <td><span class="badge badge-status {{ $badge }}">{{ $status }}</span></td>
                    @if (! empty($showClaimInfo))
                        <td class="small">
                            @if ($claimer)
                                {{ $claimer->ngo_name ?? $claimer->name }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    @endif
                    @if (! empty($showManageActions))
                        <td>
                            @if ($status === 'AVAILABLE')
                                <a href="{{ url('/donor/donations/'.$listing->id.'/edit') }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    @endif
                    @if (! empty($showDeliverButton))
                        <td>
                            @if ($status === 'CLAIMED')
                                <form method="POST" action="{{ url('/food/'.$listing->id.'/deliver') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-brand" data-confirm="Mark this listing as DELIVERED?">Mark delivered</button>
                                </form>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    @php
                        $cols = 6;
                        if (! empty($showClaimInfo)) {
                            $cols += 1;
                        }
                        if (! empty($showManageActions)) {
                            $cols += 1;
                        }
                        if (! empty($showDeliverButton)) {
                            $cols += 1;
                        }
                    @endphp
                    <td colspan="{{ $cols }}" class="text-muted p-3">No listings posted yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
