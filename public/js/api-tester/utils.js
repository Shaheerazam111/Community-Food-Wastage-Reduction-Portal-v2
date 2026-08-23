/** Small helpers used across the API tester UI. */

export function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

export function roundCoord(value) {
    return Math.round(value * 1e6) / 1e6;
}

export function haversineKm(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) ** 2
        + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

export function buildApiPath(path, listingId) {
    return path.replace('{listing_id}', listingId || '');
}

export function availableListings(snapshot) {
    const now = Date.now();
    return (snapshot.listings || []).filter((listing) => {
        if (listing.status !== 'AVAILABLE') return false;
        if (listing.expiry_at && new Date(listing.expiry_at).getTime() <= now) return false;
        return listing.latitude && listing.longitude;
    });
}

export function defaultExpiryInputs() {
    document.querySelectorAll('[data-default-offset]').forEach((input) => {
        const date = new Date(Date.now() + parseInt(input.dataset.defaultOffset, 10));
        const pad = (n) => String(n).padStart(2, '0');
        input.value = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    });
}

export function createTileLayer(map) {
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);
}
