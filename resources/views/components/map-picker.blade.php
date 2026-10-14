@props([
    'latName' => 'latitude',
    'lngName' => 'longitude',
    'lat' => null,
    'lng' => null,
    'centerLat' => -8.2,
    'centerLng' => 114.0,
    'zoom' => 10,
    'height' => 'h-64',
])

@php
    $uid = uniqid('mappicker_');
@endphp

@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endonce

<div>
    <div class="flex items-center justify-between gap-2 mb-1.5">
        <p class="text-xs text-gray-500">Klik pada peta untuk mengisi koordinat, atau pakai lokasi GPS perangkat Anda.</p>
        <button type="button" id="{{ $uid }}_gps" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 shrink-0 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span id="{{ $uid }}_gps_label">Gunakan Lokasi GPS Saya</span>
        </button>
    </div>
    <div id="{{ $uid }}_map" class="w-full {{ $height }} rounded-xl border border-gray-200 z-0"></div>
    <p id="{{ $uid }}_err" class="hidden mt-1.5 text-xs text-red-600"></p>

    <div class="grid grid-cols-2 gap-3 mt-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
            <input type="number" id="{{ $uid }}_lat" name="{{ $latName }}" value="{{ old($latName, $lat) }}" step="0.00000001" placeholder="-8.123456"
                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
            <input type="number" id="{{ $uid }}_lng" name="{{ $lngName }}" value="{{ old($lngName, $lng) }}" step="0.00000001" placeholder="114.123456"
                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
        </div>
    </div>
</div>

<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const centerLat = {{ $centerLat }};
        const centerLng = {{ $centerLng }};
        const map = L.map('{{ $uid }}_map').setView([centerLat, centerLng], {{ $zoom }});
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;
        const latInput = document.getElementById('{{ $uid }}_lat');
        const lngInput = document.getElementById('{{ $uid }}_lng');
        const errEl = document.getElementById('{{ $uid }}_err');
        const gpsBtn = document.getElementById('{{ $uid }}_gps');
        const gpsLabel = document.getElementById('{{ $uid }}_gps_label');

        function showError(msg) {
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        }

        function setMarker(lat, lng) {
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map);
        }

        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            latInput.value = lat;
            lngInput.value = lng;
            setMarker(lat, lng);
            errEl.classList.add('hidden');
        });

        if (latInput.value && lngInput.value) {
            setMarker(parseFloat(latInput.value), parseFloat(lngInput.value));
            map.setView([parseFloat(latInput.value), parseFloat(lngInput.value)], 15);
        }

        gpsBtn.addEventListener('click', function() {
            errEl.classList.add('hidden');
            if (!navigator.geolocation) {
                showError('Perangkat/browser ini tidak mendukung GPS. Silakan klik lokasi di peta secara manual.');
                return;
            }
            gpsLabel.textContent = 'Mencari lokasi...';
            gpsBtn.disabled = true;
            navigator.geolocation.getCurrentPosition(function(pos) {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                latInput.value = lat;
                lngInput.value = lng;
                setMarker(lat, lng);
                map.setView([lat, lng], 16);
                gpsLabel.textContent = 'Gunakan Lokasi GPS Saya';
                gpsBtn.disabled = false;
            }, function(err) {
                gpsLabel.textContent = 'Gunakan Lokasi GPS Saya';
                gpsBtn.disabled = false;
                if (err.code === err.PERMISSION_DENIED) {
                    showError('Izin lokasi ditolak. Aktifkan izin GPS untuk situs ini, atau klik lokasi di peta secara manual.');
                } else {
                    showError('Gagal mendapatkan lokasi GPS. Silakan klik lokasi di peta secara manual.');
                }
            }, { enableHighAccuracy: true, timeout: 10000 });
        });
    });
})();
</script>
