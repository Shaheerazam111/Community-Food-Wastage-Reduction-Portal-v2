@if (!empty($api['usesListingId']))
    <div class="listing-picker-wrap {{ str_contains($api['path'], 'claim') ? 'claim' : '' }}">
        <label>Listing ID for this request</label>
        <div class="listing-id-row">
            <div>
                <select class="listing-id-select" data-api="{{ $api['id'] }}">
                    <option value="">Select listing…</option>
                </select>
            </div>
            <div class="id-manual">
                <input type="number" class="listing-id-local" data-api="{{ $api['id'] }}" placeholder="ID">
            </div>
        </div>
        <p class="picker-hint">Uses this ID in the URL: {{ $api['path'] }}</p>
    </div>
@endif
