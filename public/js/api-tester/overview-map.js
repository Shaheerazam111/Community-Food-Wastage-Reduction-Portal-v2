import { state } from './state.js';
import { createTileLayer } from './utils.js';

/** Main dashboard map at the top of the page. */

export function initOverviewMap() {
    state.overviewMap = L.map('map').setView([24.8607, 67.0011], 11);
    createTileLayer(state.overviewMap);
    state.overviewMarkers = L.layerGroup().addTo(state.overviewMap);
}

function clearOverviewMap() {
    state.overviewMarkers.clearLayers();
    if (state.overviewSearchCircle) {
        state.overviewMap.removeLayer(state.overviewSearchCircle);
        state.overviewSearchCircle = null;
    }
}

export function drawOverviewMap(listings, searchLat, searchLng, radiusKm) {
    clearOverviewMap();
    const bounds = [];

    if (searchLat && searchLng) {
        L.marker([searchLat, searchLng], {
            icon: L.divIcon({
                className: '',
                html: '<div style="background:#2563eb;width:14px;height:14px;border-radius:50%;border:2px solid #fff"></div>',
            }),
        }).addTo(state.overviewMarkers).bindPopup('Search center');
        bounds.push([searchLat, searchLng]);

        if (radiusKm) {
            state.overviewSearchCircle = L.circle([searchLat, searchLng], {
                radius: radiusKm * 1000,
                color: '#2563eb',
                fillOpacity: 0.08,
            }).addTo(state.overviewMap);
        }
    }

    (listings || []).forEach((listing) => {
        if (!listing.latitude || !listing.longitude) return;

        const color = listing.status === 'AVAILABLE' ? '#22c55e'
            : listing.status === 'CLAIMED' ? '#f59e0b' : '#64748b';

        L.circleMarker([listing.latitude, listing.longitude], {
            radius: 8, color, fillColor: color, fillOpacity: 0.8,
        }).addTo(state.overviewMarkers)
            .bindPopup(`<b>#${listing.id} ${listing.title}</b><br>${listing.status}<br>${listing.pickup_address || ''}`);

        bounds.push([listing.latitude, listing.longitude]);
    });

    if (bounds.length) {
        state.overviewMap.fitBounds(bounds, { padding: [30, 30], maxZoom: 14 });
    }
}

export function drawOverviewFromSnapshot(snapshot) {
    const visible = snapshot.listings.filter((l) => l.status !== 'EXPIRED');
    drawOverviewMap(visible);
}
