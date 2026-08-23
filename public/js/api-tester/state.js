/** Shared app state — one place for data every module reads. */
export const state = {
    baseUrl: window.location.origin,
    lastSnapshot: { listings: [], users: [] },
    pickerMaps: {},
    searchMaps: {},
    overviewMap: null,
    overviewMarkers: null,
    overviewSearchCircle: null,
};

export function getSnapshot() {
    return state.lastSnapshot;
}

export function setSnapshot(data) {
    state.lastSnapshot = data;
}
