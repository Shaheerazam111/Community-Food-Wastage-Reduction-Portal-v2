import { defaultExpiryInputs } from './utils.js';
import { initOverviewMap } from './overview-map.js';
import { refreshSnapshot } from './snapshot.js';
import { toggleGroup, toggleApi, initOpenPanels } from './ui-toggle.js';
import { bindEvents } from './events.js';

/** App entry point — wires modules together on page load. */
function boot() {
    defaultExpiryInputs();
    initOverviewMap();
    bindEvents();
    refreshSnapshot();
    initOpenPanels();
    document.querySelector('.group')?.classList.add('open');
}

window.toggleGroup = toggleGroup;
window.toggleApi = toggleApi;

boot();
