import { state, setSnapshot } from './state.js';
import { escapeHtml } from './utils.js';
import { updateSessionUI } from './session.js';
import { refreshListingPickers } from './listings.js';
import { drawOverviewFromSnapshot } from './overview-map.js';
import { redrawSearchPreview } from './search-map.js';

function updateDonorLocationHints(sessionUser) {
    document.querySelectorAll('[id^="donor-location-"]').forEach((box) => {
        if (!sessionUser || sessionUser.role !== 'donor') {
            box.className = 'donor-location-box warn';
            box.textContent = 'Login as a donor first — pickup location comes from the donor profile.';
            return;
        }
        if (sessionUser.latitude == null || sessionUser.longitude == null) {
            box.className = 'donor-location-box warn';
            box.textContent = 'Your donor profile has no location. Register again with map location or use Register Donor.';
            return;
        }
        const address = sessionUser.address || sessionUser.business_name || 'Donor address';
        box.className = 'donor-location-box';
        box.innerHTML = `<strong>Pickup location (auto from donor profile):</strong> ${address} · Lat ${sessionUser.latitude}, Lng ${sessionUser.longitude}`;
    });
}

function renderUsersTable(users) {
    const tbody = document.querySelector('#users-table tbody');
    if (!users.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="empty">No users yet — register via Task 1</td></tr>';
        return;
    }
    tbody.innerHTML = users.map((u) => `<tr>
        <td>${u.id}</td><td>${escapeHtml(u.name)}</td><td>${escapeHtml(u.email)}</td>
        <td class="role-${u.role}">${u.role}</td>
        <td>${u.latitude ?? '—'}, ${u.longitude ?? '—'}</td>
    </tr>`).join('');
}

function renderListingsTable(listings) {
    const tbody = document.querySelector('#listings-table tbody');
    if (!listings.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty">No listings yet — login as donor and create one</td></tr>';
        return;
    }
    tbody.innerHTML = listings.map((l) => `<tr>
        <td class="listing-id-cell" data-id="${l.id}" title="Click to use for claim/deliver">${l.id}</td>
        <td>${escapeHtml(l.title)}</td>
        <td><span class="status status-${l.status}">${l.status}</span></td>
        <td>${l.donor ? escapeHtml(l.donor.name) : '—'}</td>
        <td>${typeof l.claimed_by === 'object' && l.claimed_by ? escapeHtml(l.claimed_by.name) : (l.claimed_by ? '#' + l.claimed_by : '—')}</td>
        <td>${l.expiry_at ? l.expiry_at.replace('T', ' ').slice(0, 16) : '—'}</td>
    </tr>`).join('');
}

function refreshOpenSearchMaps() {
    document.querySelectorAll('[data-show-map]').forEach((form) => {
        const apiId = form.dataset.api;
        if (state.searchMaps[apiId]) {
            redrawSearchPreview(form, state.searchMaps[apiId].lastResults);
        }
    });
}

export async function refreshSnapshot() {
    const response = await fetch(`${state.baseUrl}/test-apis/snapshot`, { credentials: 'include' });
    const data = await response.json();

    setSnapshot(data);
    updateSessionUI(data.session_user);
    updateDonorLocationHints(data.session_user);
    renderUsersTable(data.users);
    renderListingsTable(data.listings);
    drawOverviewFromSnapshot(data);
    refreshListingPickers();
    refreshOpenSearchMaps();
}
