// Ask before important actions (claim, mark delivered, etc.)
document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
        var message = el.getAttribute('data-confirm') || 'Are you sure?';
        if (!window.confirm(message)) {
            e.preventDefault();
        }
    });
});

// Click map to set hidden latitude / longitude (register page)
(function () {
    var boxes = document.querySelectorAll('[data-map-picker]');
    if (!boxes.length || typeof L === 'undefined') {
        return;
    }

    boxes.forEach(function (box) {
        var wrap = box.closest('form') || box.parentElement;
        var latInput = wrap.querySelector('[data-map-lat]');
        var lngInput = wrap.querySelector('[data-map-lng]');
        var hint = wrap.querySelector('[data-map-hint]');
        var startLat = parseFloat(box.getAttribute('data-lat'));
        var startLng = parseFloat(box.getAttribute('data-lng'));
        var defaultLat = parseFloat(box.getAttribute('data-default-lat')) || 31.5204;
        var defaultLng = parseFloat(box.getAttribute('data-default-lng')) || 74.3587;
        var hasStart = !isNaN(startLat) && !isNaN(startLng);
        var centerLat = hasStart ? startLat : defaultLat;
        var centerLng = hasStart ? startLng : defaultLng;
        var marker = null;

        var map = L.map(box).setView([centerLat, centerLng], hasStart ? 15 : 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        function setPin(lat, lng) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
            if (latInput) {
                latInput.value = String(lat.toFixed(6));
            }
            if (lngInput) {
                lngInput.value = String(lng.toFixed(6));
            }
            if (hint) {
                hint.textContent = 'Pin set — click again to move it.';
            }
        }

        if (hasStart) {
            setPin(startLat, startLng);
        }

        map.on('click', function (e) {
            setPin(e.latlng.lat, e.latlng.lng);
        });

        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    });
})();
