@if (!empty($api['mapPicker']))
    <div class="map-picker-wrap">
        <label>Pick location on map</label>
        <p class="picker-hint">Click the map — latitude, longitude, and area name fill in automatically.</p>
        <div class="picker-map" id="picker-map-{{ $api['id'] }}"></div>
        <div class="picker-result" id="picker-result-{{ $api['id'] }}">
            <span class="picker-area">Area: — click the map —</span>
            <span class="picker-coords">Lat: — · Lng: —</span>
        </div>
    </div>
@endif

@if (!empty($api['showMap']))
    <div class="search-preview-wrap">
        <label>Search area preview</label>
        <p class="picker-hint">Blue = you · dashed circle = radius · green = in range · gray = outside radius.</p>
        <div class="search-loc-label" id="search-loc-{{ $api['id'] }}">Your location: —</div>
        <div class="search-preview-map" id="search-map-{{ $api['id'] }}"></div>
        <div class="search-summary" id="search-summary-{{ $api['id'] }}">Adjust lat/lng/radius or click the map, then Send request.</div>
        <div class="search-legend">
            <span><i style="background:#2563eb"></i> Your location</span>
            <span><i style="background:#22c55e"></i> In range</span>
            <span><i style="background:#64748b"></i> Outside radius</span>
        </div>
    </div>
@endif
