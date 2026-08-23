import { state } from './state.js';
import { roundCoord, createTileLayer } from './utils.js';
import { fetchAreaName } from './geocode.js';

/** Map picker for register forms — click to set lat/lng + address. */

export function initRegisterMapPicker(form) {
    const apiId = form.dataset.api;

    if (state.pickerMaps[apiId]) {
        setTimeout(() => state.pickerMaps[apiId].map.invalidateSize(), 100);
        return;
    }

    const mapElement = document.getElementById(`picker-map-${apiId}`);
    const latInput = form.querySelector('[name="latitude"]');
    const lngInput = form.querySelector('[name="longitude"]');
    const addressInput = form.querySelector('[name="address"]') || form.querySelector('[name="pickup_address"]');
    const resultBox = document.getElementById(`picker-result-${apiId}`);

    const startLat = parseFloat(latInput?.value) || 24.8607;
    const startLng = parseFloat(lngInput?.value) || 67.0011;

    const map = L.map(mapElement).setView([startLat, startLng], 13);
    createTileLayer(map);

    const marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

    async function applyPickedLocation(lat, lng) {
        const latRounded = roundCoord(lat);
        const lngRounded = roundCoord(lng);

        marker.setLatLng([latRounded, lngRounded]);

        if (latInput) { latInput.value = latRounded; latInput.classList.add('map-filled'); }
        if (lngInput) { lngInput.value = lngRounded; lngInput.classList.add('map-filled'); }

        resultBox.querySelector('.picker-coords').textContent = `Lat: ${latRounded} · Lng: ${lngRounded}`;
        resultBox.querySelector('.picker-area').textContent = 'Area: looking up…';

        try {
            const geo = await fetchAreaName(latRounded, lngRounded);
            resultBox.querySelector('.picker-area').textContent = `Area: ${geo.area_name}`;
            if (addressInput && geo.area_name) addressInput.value = geo.area_name;
        } catch {
            resultBox.querySelector('.picker-area').textContent = 'Area: (could not resolve name)';
        }
    }

    map.on('click', (event) => applyPickedLocation(event.latlng.lat, event.latlng.lng));
    marker.on('dragend', (event) => {
        const point = event.target.getLatLng();
        applyPickedLocation(point.lat, point.lng);
    });

    if (latInput?.value && lngInput?.value) {
        applyPickedLocation(startLat, startLng);
    }

    state.pickerMaps[apiId] = { map, marker };
}
