import { state } from './state.js';

/** Calls our Laravel reverse-geocode endpoint. */
export async function fetchAreaName(lat, lng) {
    const url = `${state.baseUrl}/test-apis/reverse-geocode?lat=${lat}&lng=${lng}`;
    const response = await fetch(url);
    if (!response.ok) throw new Error('Geocoding failed');
    return response.json();
}
