import { initRegisterMapPicker } from './picker-map.js';
import { initSearchPreviewMap } from './search-map.js';

export function toggleGroup(element) {
    element.classList.toggle('open');
}

export function toggleApi(panel) {
    panel.classList.toggle('open');
    if (!panel.classList.contains('open')) return;

    const pickerForm = panel.querySelector('[data-map-picker]');
    if (pickerForm) initRegisterMapPicker(pickerForm);

    const searchForm = panel.querySelector('[data-show-map]');
    if (searchForm) initSearchPreviewMap(searchForm);
}

export function initOpenPanels() {
    document.querySelectorAll('[data-map-picker]').forEach((form) => {
        if (form.closest('.api-panel')?.classList.contains('open')) initRegisterMapPicker(form);
    });
    document.querySelectorAll('[data-show-map]').forEach((form) => {
        if (form.closest('.api-panel')?.classList.contains('open')) initSearchPreviewMap(form);
    });
}
