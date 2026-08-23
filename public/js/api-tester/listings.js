import { getSnapshot } from './state.js';
import { escapeHtml } from './utils.js';

/** Keeps listing_id in sync across dropdowns, inputs, and claim buttons. */

export function setListingId(id) {
    const value = id ? String(id) : '';

    document.getElementById('listing-id').value = value;
    document.getElementById('listing-id-select').value = value;

    document.querySelectorAll('.listing-id-select').forEach((select) => {
        select.value = value;
    });
    document.querySelectorAll('.listing-id-local').forEach((input) => {
        input.value = value;
    });
    document.querySelectorAll('.btn-listing').forEach((button) => {
        button.classList.toggle('active', button.dataset.id === value);
    });
}

export function getListingId(apiId) {
    const local = document.querySelector(`.listing-id-local[data-api="${apiId}"]`);
    if (local?.value) return local.value;

    const panelSelect = document.querySelector(`.listing-id-select[data-api="${apiId}"]`);
    if (panelSelect?.value) return panelSelect.value;

    const globalSelect = document.getElementById('listing-id-select');
    if (globalSelect?.value) return globalSelect.value;

    return document.getElementById('listing-id').value;
}

function buildListingOptions(listings, selectedId) {
    let html = '<option value="">Select listing…</option>';
    listings.forEach((listing) => {
        const selected = String(listing.id) === String(selectedId) ? ' selected' : '';
        html += `<option value="${listing.id}"${selected}>#${listing.id} — ${escapeHtml(listing.title)} (${listing.status})</option>`;
    });
    return html;
}

function renderClaimableButtons(listings, currentId) {
    const box = document.getElementById('claimable-listings');
    if (!box) return;

    const available = listings.filter((l) => l.status === 'AVAILABLE');

    if (!listings.length) {
        box.innerHTML = '<span class="empty">No listings yet — login as donor and create one first.</span>';
        return;
    }

    if (!available.length) {
        box.innerHTML = '<span class="empty">No AVAILABLE listings — all are claimed or delivered.</span>'
            + listings.map((l) => `<button type="button" class="btn-listing claimed" disabled>#${l.id} ${escapeHtml(l.title)} (${l.status})</button>`).join('');
        return;
    }

    box.innerHTML = available.map((l) => {
        const active = String(l.id) === String(currentId) ? ' active' : '';
        return `<button type="button" class="btn-listing${active}" data-id="${l.id}">#${l.id} ${escapeHtml(l.title)}</button>`;
    }).join('');

    box.querySelectorAll('.btn-listing:not(.claimed)').forEach((button) => {
        button.addEventListener('click', () => setListingId(button.dataset.id));
    });
}

export function refreshListingPickers() {
    const listings = getSnapshot().listings || [];
    const currentId = document.getElementById('listing-id').value;

    document.getElementById('listing-id-select').innerHTML = buildListingOptions(listings, currentId);

    document.querySelectorAll('.listing-id-select').forEach((select) => {
        select.innerHTML = buildListingOptions(listings, getListingId(select.dataset.api) || currentId);
    });

    renderClaimableButtons(listings, currentId);
}
