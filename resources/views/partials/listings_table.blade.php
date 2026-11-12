{{-- Donor listing table. $listings = FoodListing models (use showClaimInfo on donor pages). --}}
<div class="table-responsive">
    <table class="table table-striped mb-0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Quantity</th>
                @if (! empty($showClaimInfo))
                    <th>Pickup</th>
                @endif
                <th>Expires</th>
                <th>Status</th>
                @if (! empty($showDeliverButton))
                    <th>Admin</th>
                @endif
                @if (! empty($showClaimInfo))
                    <th>Claimed by</th>
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
                        default => 'badge-closed',
                    };
                    $expiry = $listing->expiry_at;
                    $expiryText = $expiry instanceof \DateTimeInterface ? $expiry->format('Y-m-d H:i') : (string) $expiry;
                    $claimer = $listing->relationLoaded('claimedBy') ? $listing->claimedBy : null;
                @endphp
                <tr>
                    <td>{{ $listing->title }}</td>
                    <td>{{ $listing->category === 'non_veg' ? 'Non-veg' : 'Veg' }}</td>
                    <td>{{ $listing->quantity }}</td>
                    @if (! empty($showClaimInfo))
                        <td class="small">{{ $listing->pickup_address }}</td>
                    @endif
                    <td>{{ $expiryText }}</td>
                    <td><span class="badge badge-status {{ $badge }}">{{ $status }}</span></td>
                    @if (! empty($showDeliverButton))
                        <td class="small">{{ $listing->moderation_status ?? '—' }}</td>
                    @endif
                    @if (! empty($showClaimInfo))
                        <td class="small">
                            @if ($claimer)
                                {{ $claimer->ngo_name ?? $claimer->name }}
                            @elseif ($status === 'AVAILABLE')
                                <span class="text-muted">—</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    @endif
                    @if (! empty($showDeliverButton))
                        <td class="col-actions">
                            @if ($status === 'CLAIMED')
                                <form method="POST" action="{{ url('/food/'.$listing->id.'/deliver') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-brand" data-confirm="Mark this listing as DELIVERED?">Mark delivered</button>
                                </form>
                            @elseif ($status === 'AVAILABLE')
                                <form method="POST" action="{{ url('/food/'.$listing->id.'/cancel') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" data-confirm="Cancel this listing? It will no longer appear in search.">Cancel</button>
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
                        $cols = 5;
                        if (! empty($showClaimInfo)) {
                            $cols += 2;
                        }
                        if (! empty($showDeliverButton)) {
                            $cols += 1;
                        }
                    @endphp
                    <td colspan="{{ $cols }}" class="text-muted p-3">No listings yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
