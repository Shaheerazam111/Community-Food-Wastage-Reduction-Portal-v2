import { setListingId } from './listings.js';
import { sendFormApi, sendButtonApi, quickLogin, logout } from './api-client.js';
import { refreshSnapshot } from './snapshot.js';

export function bindEvents() {
    document.querySelectorAll('form.api-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            sendFormApi(form);
        });
    });

    document.querySelectorAll('.btn-send-no-form').forEach((button) => {
        button.addEventListener('click', () => sendButtonApi(button));
    });

    document.getElementById('listing-id').addEventListener('input', (e) => setListingId(e.target.value));
    document.getElementById('listing-id-select').addEventListener('change', (e) => setListingId(e.target.value));

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('listing-id-select')) setListingId(e.target.value);
    });
    document.addEventListener('input', (e) => {
        if (e.target.classList.contains('listing-id-local')) setListingId(e.target.value);
    });
    document.addEventListener('click', (e) => {
        const cell = e.target.closest('.listing-id-cell');
        if (cell) setListingId(cell.dataset.id);
    });

    document.getElementById('widget-logout').addEventListener('click', logout);
    document.getElementById('btn-refresh').addEventListener('click', refreshSnapshot);

    document.querySelectorAll('[data-quick-login]').forEach((button) => {
        button.addEventListener('click', () => quickLogin(button.dataset.quickLogin));
    });
}
