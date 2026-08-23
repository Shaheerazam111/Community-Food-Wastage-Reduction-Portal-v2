import { state } from './state.js';
import { buildApiPath } from './utils.js';
import { getListingId, setListingId } from './listings.js';
import { updateSessionUI } from './session.js';
import { refreshSnapshot } from './snapshot.js';
import { drawOverviewMap } from './overview-map.js';
import { redrawSearchPreview, storeSearchResults } from './search-map.js';

function collectFormData(form) {
    const formData = new FormData(form);
    const body = {};
    const params = {};

    formData.forEach((value, key) => {
        if (value === '') return;
        if (['latitude', 'longitude', 'radius_km'].includes(key) && form.dataset.method === 'GET') {
            params[key] = value;
        } else if (key === 'latitude' || key === 'longitude') {
            body[key] = parseFloat(value);
        } else {
            body[key] = value;
        }
    });

    if (body.expiry_at) body.expiry_at = body.expiry_at.replace('T', ' ') + ':00';
    return { body, params };
}

function showResponse(box, status, json, ok) {
    if (!box) return;
    box.textContent = `HTTP ${status}\n\n` + (typeof json === 'string' ? json : JSON.stringify(json, null, 2));
    box.className = 'response-box ' + (ok ? 'ok' : 'err');
}

function handleAuthSideEffects(path, json, ok) {
    if (json?.user) updateSessionUI(json.user);
    if (path === '/auth/logout' && ok) updateSessionUI(null);
}

function handleListingCapture(capture, json) {
    if (capture === 'listing_id' && json?.listing?.id) setListingId(json.listing.id);
}

function handleSearchSideEffects(showMap, params, json, apiId) {
    if (!showMap || !params?.latitude || !params?.longitude) return;

    const results = json?.listings || [];
    drawOverviewMap(results, parseFloat(params.latitude), parseFloat(params.longitude), parseFloat(params.radius_km || 5));

    const form = document.querySelector(`form[data-api="${apiId}"]`);
    storeSearchResults(apiId, results);
    if (form) redrawSearchPreview(form, results);
}

export async function sendApi({ method, path, body, params, capture, showMap, apiId }) {
    const listingId = getListingId(apiId);
    let url = state.baseUrl + buildApiPath(path, listingId);

    if (method === 'GET' && params && Object.keys(params).length) {
        url += '?' + new URLSearchParams(params).toString();
    }

    const responseBox = apiId ? document.getElementById(`response-${apiId}`) : null;
    if (responseBox) {
        responseBox.textContent = 'Sending…';
        responseBox.className = 'response-box';
    }

    const options = { method, credentials: 'include', headers: { Accept: 'application/json' } };
    if (method !== 'GET' && Object.keys(body || {}).length) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }

    try {
        const response = await fetch(url, options);
        const text = await response.text();
        let json;
        try { json = JSON.parse(text); } catch { json = text; }

        showResponse(responseBox, response.status, json, response.ok);
        handleListingCapture(capture, json);
        handleAuthSideEffects(path, json, response.ok);
        handleSearchSideEffects(showMap, params, json, apiId);
        await refreshSnapshot();
    } catch (error) {
        showResponse(responseBox, 'Error', error.message, false);
    }
}

export function sendFormApi(form) {
    const { body, params } = collectFormData(form);
    return sendApi({
        method: form.dataset.method,
        path: form.dataset.path,
        body,
        params,
        capture: form.dataset.capture,
        showMap: form.dataset.showMap,
        apiId: form.dataset.api,
    });
}

export function sendButtonApi(button) {
    const listingId = getListingId(button.dataset.api);
    if (button.dataset.usesListingId && !listingId) {
        alert('Select or enter a listing_id first (use the dropdown, claim panel, or click an ID in the table).');
        return;
    }
    return sendApi({
        method: button.dataset.method,
        path: button.dataset.path,
        body: {},
        apiId: button.dataset.api,
    });
}

export function quickLogin(email) {
    return sendApi({
        method: 'POST',
        path: '/auth/login',
        body: { email, password: 'secret123' },
        apiId: null,
    });
}

export function logout() {
    return sendApi({ method: 'POST', path: '/auth/logout', body: {}, apiId: null });
}
