import { state } from './state.js';
import { roundCoord, haversineKm, availableListings, createTileLayer, escapeHtml } from './utils.js';
import { fetchAreaName } from './geocode.js';

export function initSearchPreviewMap(form) {
    const apiId = form.dataset.api;
    const mapElement = document.getElementById(`search-map-${apiId}`);
    if (!mapElement) return;

    if (state.searchMaps[apiId]) {
        setTimeout(() => {
            state.searchMaps[apiId].map.invalidateSize();
            redrawSearchPreview(form, state.searchMaps[apiId].lastResults);
        }, 100);
        return;
    }

    const latInput = form.querySelector('[name="latitude"]');
    const lngInput = form.querySelector('[name="longitude"]');
    const startLat = parseFloat(latInput?.value) || 24.8615;
    const startLng = parseFloat(lngInput?.value) || 67.002;

    const map = L.map(mapElement).setView([startLat, startLng], 12);
    createTileLayer(map);
    const layers = L.layerGroup().addTo(map);
    const mapState = { map, layers, lastResults: null, radiusCircle: null };

    async function applySearchLocation(lat, lng, fromClick) {
        const latRounded = roundCoord(lat);
        const lngRounded = roundCoord(lng);
        if (latInput) latInput.value = latRounded;
        if (lngInput) lngInput.value = lngRounded;
        if (fromClick) {
            latInput?.classList.add('map-filled');
            lngInput?.classList.add('map-filled');
        }
        const label = document.getElementById(`search-loc-${apiId}`);
        label.textContent = 'Your location: looking up…';
        try {
            const geo = await fetchAreaName(latRounded, lngRounded);
            label.textContent = `Your location: ${geo.area_name} (${latRounded}, ${lngRounded})`;
        } catch {
            label.textContent = `Your location: ${latRounded}, ${lngRounded}`;
        }
        redrawSearchPreview(form, mapState.lastResults);
    }

    map.on('click', (e) => applySearchLocation(e.latlng.lat, e.latlng.lng, true));
    ['latitude', 'longitude', 'radius_km'].forEach((name) => {
        form.querySelector(`[name="${name}"]`)?.addEventListener('input', () => redrawSearchPreview(form, mapState.lastResults));
    });

    state.searchMaps[apiId] = mapState;
    applySearchLocation(startLat, startLng, false);
}

export function redrawSearchPreview(form, apiResults) {
    const apiId = form.dataset.api;
    const mapState = state.searchMaps[apiId];
    if (!mapState) return;

    const lat = parseFloat(form.querySelector('[name="latitude"]')?.value);
    const lng = parseFloat(form.querySelector('[name="longitude"]')?.value);
    const radius = parseFloat(form.querySelector('[name="radius_km"]')?.value) || 5;
    const summary = document.getElementById(`search-summary-${apiId}`);

    mapState.layers.clearLayers();
    if (mapState.radiusCircle) {
        mapState.map.removeLayer(mapState.radiusCircle);
        mapState.radiusCircle = null;
    }

    if (!lat || !lng || Number.isNaN(lat) || Number.isNaN(lng)) {
        summary.innerHTML = 'Enter valid latitude and longitude.';
        return;
    }

    const userIcon = L.divIcon({
        className: '',
        html: '<div style="background:#2563eb;width:16px;height:16px;border-radius:50%;border:3px solid #fff;box-shadow:0 0 0 2px #2563eb"></div>',
        iconSize: [16, 16], iconAnchor: [8, 8],
    });
    L.marker([lat, lng], { icon: userIcon, zIndexOffset: 1000 }).addTo(mapState.layers)
        .bindPopup('<b>Your location</b><br>From search form inputs');

    mapState.radiusCircle = L.circle([lat, lng], {
        radius: radius * 1000, color: '#2563eb', weight: 2, dashArray: '6 4', fillColor: '#2563eb', fillOpacity: 0.1,
    }).addTo(mapState.map);

    const listings = availableListings(state.lastSnapshot);
    const bounds = [[lat, lng]];

    listings.forEach((listing) => {
        const dist = haversineKm(lat, lng, parseFloat(listing.latitude), parseFloat(listing.longitude));
        const inRadius = dist <= radius;
        const returnedByApi = apiResults !== null && apiResults.some((r) => r.id === listing.id);
        let color, label;

        if (returnedByApi) {
            const apiItem = apiResults.find((r) => r.id === listing.id);
            color = '#22c55e';
            label = `<b>#${listing.id} ${escapeHtml(listing.title)}</b><br><span class="in-range">✓ In range · ${parseFloat(apiItem?.distance_km ?? dist).toFixed(2)} km</span>`;
        } else if (apiResults !== null) {
            color = '#64748b';
            label = `<b>#${listing.id} ${escapeHtml(listing.title)}</b><br><span class="out-range">Not in results · ${dist.toFixed(2)} km away</span>`;
        } else if (inRadius) {
            color = '#22c55e';
            label = `<b>#${listing.id} ${escapeHtml(listing.title)}</b><br><span class="in-range">Within ${radius} km · ${dist.toFixed(2)} km</span>`;
        } else {
            color = '#64748b';
            label = `<b>#${listing.id} ${escapeHtml(listing.title)}</b><br><span class="out-range">Outside radius · ${dist.toFixed(2)} km away</span>`;
        }

        L.circleMarker([listing.latitude, listing.longitude], {
            radius: 9, color, fillColor: color,
            fillOpacity: returnedByApi || (apiResults === null && inRadius) ? 1 : 0.65, weight: 2,
        }).addTo(mapState.layers).bindPopup(label);
        bounds.push([parseFloat(listing.latitude), parseFloat(listing.longitude)]);
    });

    const inRangeCount = apiResults !== null ? apiResults.length
        : listings.filter((l) => haversineKm(lat, lng, parseFloat(l.latitude), parseFloat(l.longitude)) <= radius).length;

    if (apiResults !== null) {
        summary.innerHTML = inRangeCount > 0
            ? `<strong>${inRangeCount} listing(s)</strong> returned by API within <strong>${radius} km</strong>. <span class="out-range">${listings.length - inRangeCount} other available listing(s) shown in gray.</span>`
            : `<strong>0 listings</strong> within <strong>${radius} km</strong> — ${listings.length > 0 ? `<span class="out-range">${listings.length} available listing(s) exist outside this area.</span>` : 'no available listings yet.'}`;
    } else {
        summary.innerHTML = `Preview: <strong>${inRangeCount}</strong> listing(s) would match within <strong>${radius} km</strong>. Click <em>Send request</em> for live results.`;
    }

    mapState.map.fitBounds(bounds, { padding: [40, 40], maxZoom: apiResults !== null && inRangeCount === 0 && listings.length > 0 ? 10 : 14 });
}

export function storeSearchResults(apiId, results) {
    if (state.searchMaps[apiId]) state.searchMaps[apiId].lastResults = results;
}
