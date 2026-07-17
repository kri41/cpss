@extends($layout)

@section('title', 'Peta Event Olahraga - Dataraga')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ===== HEADER ===== --}}
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Peta Distribusi Event</h1>
                <p class="text-sm text-gray-500 mt-1">Klik provinsi untuk melihat event olahraga di wilayah tersebut</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $isDashboard ? route('dashboard.events') : route('events.index') }}"
                   class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Tampilan Daftar
                </a>
            </div>
        </div>

        {{-- ===== LEGENDA & STATS ===== --}}
        <div class="flex flex-wrap items-center gap-4 mb-4">
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-4 h-4 rounded border border-gray-200 inline-block" style="background:#f9fafb"></span> Tidak ada event
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-4 h-4 rounded inline-block" style="background:#bbf7d0"></span> 1–3
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-4 h-4 rounded inline-block" style="background:#4ade80"></span> 4–8
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-4 h-4 rounded inline-block" style="background:#16a34a"></span> 9–15
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-4 h-4 rounded inline-block" style="background:#14532d"></span> &gt; 15
            </div>
        </div>

        {{-- ===== LAYOUT MAP + PANEL ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- MAP --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div id="peta-indonesia" class="w-full" style="height: 520px; z-index: 1;"></div>
                </div>
                {{-- Tooltip hover --}}
                <div id="peta-tooltip"
                     class="hidden fixed z-[9000] px-3 py-2 bg-gray-900/90 text-white text-xs rounded-xl shadow-xl pointer-events-none max-w-[200px]">
                </div>
            </div>

            {{-- PANEL EVENT --}}
            <div class="lg:col-span-1">
                <div id="event-panel" class="bg-white rounded-2xl shadow-sm border border-gray-100 h-full flex flex-col" style="min-height: 520px;">
                    @if($selectedKode && $selectedNama)
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Provinsi Dipilih</p>
                                <h3 class="text-base font-bold text-gray-900 mt-0.5">{{ $selectedNama }}</h3>
                            </div>
                            <a href="{{ $isDashboard ? route('dashboard.events.peta') : route('events.peta') }}"
                               class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Reset">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        </div>
                        <div class="flex-1 overflow-y-auto p-3 space-y-2">
                            @forelse($selectedEvents as $event)
                            <a href="{{ route('events.show', $event) }}"
                               class="block p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/30 transition group">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-blue-700">{{ $event->nama_event }}</p>
                                        <div class="flex flex-wrap gap-1.5 mt-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700">{{ $event->tingkat }}</span>
                                            @if($event->kabupaten)
                                            <span class="text-[10px] text-gray-400">{{ $event->kecamatan ? $event->kecamatan . ', ' : '' }}{{ $event->kabupaten }}</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($event->tanggal_mulai)->isoFormat('D MMM YYYY') }}
                                            @if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai)
                                                — {{ \Carbon\Carbon::parse($event->tanggal_selesai)->isoFormat('D MMM YYYY') }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-md mt-0.5
                                        {{ $event->status === 'Akan Datang' ? 'bg-amber-50 text-amber-600' : ($event->status === 'Berlangsung' ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-400') }}">
                                        {{ $event->status }}
                                    </span>
                                </div>
                            </a>
                            @empty
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="p-3 bg-gray-100 rounded-full mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-sm text-gray-400">Belum ada event tervalidasi<br>di provinsi ini.</p>
                            </div>
                            @endforelse
                        </div>
                        @if($selectedEvents->count() > 0)
                        <div class="p-3 border-t border-gray-100 text-center">
                            <span class="text-xs text-gray-400">{{ $selectedEvents->count() }} event ditemukan</span>
                        </div>
                        @endif
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700">Pilih Provinsi</p>
                            <p class="text-xs text-gray-400 mt-1 max-w-[180px]">Klik salah satu provinsi di peta untuk melihat event olahraga di wilayah tersebut.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ===== TOTAL STATS ===== --}}
        <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
                $totalProvinsiAda = $provinsiCounts->count();
                $totalEvent = $provinsiCounts->sum();
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-extrabold text-gray-900">{{ $totalEvent }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Event Tervalidasi</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-extrabold text-blue-600">{{ $totalProvinsiAda }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Provinsi Terjangkau</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-extrabold text-gray-900">{{ $maxCount }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Event Terbanyak</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-2xl font-extrabold text-gray-900">34</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Provinsi RI</p>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    // Data dari PHP (kode BPS → jumlah event)
    const provinsiCounts = @json($provinsiCounts);
    const selectedKode   = @json($selectedKode);
    const isDashboard    = {{ $isDashboard ? 'true' : 'false' }};
    const baseUrl        = isDashboard ? '/dashboard/events/peta' : '/events/peta';

    // Mapping: GeoJSON state name (lowercase) → { kode BPS, nama tampil }
    // Diperlukan karena GeoJSON menggunakan nama berbeda dari DB wilayah
    const STATE_MAP = {
        'aceh':                { kode: '11', nama: 'Aceh' },
        'bali':                { kode: '51', nama: 'Bali' },
        'bangka-belitung':     { kode: '19', nama: 'Kepulauan Bangka Belitung' },
        'banten':              { kode: '36', nama: 'Banten' },
        'bengkulu':            { kode: '17', nama: 'Bengkulu' },
        'gorontalo':           { kode: '75', nama: 'Gorontalo' },
        'jakarta raya':        { kode: '31', nama: 'DKI Jakarta' },
        'jambi':               { kode: '15', nama: 'Jambi' },
        'jawa barat':          { kode: '32', nama: 'Jawa Barat' },
        'jawa tengah':         { kode: '33', nama: 'Jawa Tengah' },
        'jawa timur':          { kode: '35', nama: 'Jawa Timur' },
        'kalimantan barat':    { kode: '61', nama: 'Kalimantan Barat' },
        'kalimantan selatan':  { kode: '63', nama: 'Kalimantan Selatan' },
        'kalimantan tengah':   { kode: '62', nama: 'Kalimantan Tengah' },
        'kalimantan timur':    { kode: '64', nama: 'Kalimantan Timur' },
        'kalimantan utara':    { kode: '65', nama: 'Kalimantan Utara' },
        'kepulauan riau':      { kode: '21', nama: 'Kepulauan Riau' },
        'lampung':             { kode: '18', nama: 'Lampung' },
        'maluku':              { kode: '81', nama: 'Maluku' },
        'maluku utara':        { kode: '82', nama: 'Maluku Utara' },
        'nusa tenggara barat': { kode: '52', nama: 'Nusa Tenggara Barat' },
        'nusa tenggara timur': { kode: '53', nama: 'Nusa Tenggara Timur' },
        'papua':               { kode: '91', nama: 'Papua' },
        'papua barat':         { kode: '92', nama: 'Papua Barat' },
        'riau':                { kode: '14', nama: 'Riau' },
        'sulawesi barat':      { kode: '76', nama: 'Sulawesi Barat' },
        'sulawesi selatan':    { kode: '73', nama: 'Sulawesi Selatan' },
        'sulawesi tengah':     { kode: '72', nama: 'Sulawesi Tengah' },
        'sulawesi tenggara':   { kode: '74', nama: 'Sulawesi Tenggara' },
        'sulawesi utara':      { kode: '71', nama: 'Sulawesi Utara' },
        'sumatera barat':      { kode: '13', nama: 'Sumatera Barat' },
        'sumatera selatan':    { kode: '16', nama: 'Sumatera Selatan' },
        'sumatera utara':      { kode: '12', nama: 'Sumatera Utara' },
        'yogyakarta':          { kode: '34', nama: 'D.I. Yogyakarta' },
    };

    // Warna choropleth
    function getColor(count) {
        if (!count || count === 0) return '#f9fafb';
        if (count <= 3)  return '#bbf7d0';
        if (count <= 8)  return '#4ade80';
        if (count <= 15) return '#16a34a';
        return '#14532d';
    }

    // Ambil nama provinsi dari GeoJSON feature (property 'state')
    function getStateName(feature) {
        const p = feature.properties;
        return (p.state || p.Propinsi || p.PROPINSI || p.NAME_1 || p.name || '').trim();
    }

    // Lookup state name → {kode, count}
    function getProvinsiInfo(feature) {
        const key = getStateName(feature).toLowerCase();
        const entry = STATE_MAP[key];
        if (!entry) return { kode: null, nama: getStateName(feature), count: 0 };
        const count = provinsiCounts[entry.kode] || 0;
        return { kode: entry.kode, nama: entry.nama, count };
    }

    // ── Init Leaflet map ──────────────────────────────────────────────
    const map = L.map('peta-indonesia', {
        center: [-2.5, 118],
        zoom: 5,
        zoomControl: true,
        attributionControl: true,
        scrollWheelZoom: true,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &amp; CartoDB',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    const tooltip = document.getElementById('peta-tooltip');
    let geojsonLayer = null;

    function featureStyle(feature) {
        const info = getProvinsiInfo(feature);
        const isSelected = selectedKode && info.kode === selectedKode;
        return {
            fillColor:   getColor(info.count),
            fillOpacity: 0.85,
            color:       isSelected ? '#2563eb' : '#ffffff',
            weight:      isSelected ? 2.5 : 0.8,
            opacity:     1,
        };
    }

    function onEachFeature(feature, layer) {
        layer.on({
            mouseover(e) {
                const info = getProvinsiInfo(feature);
                layer.setStyle({ weight: 2.5, color: '#2563eb', fillOpacity: 0.95 });
                tooltip.innerHTML =
                    '<strong>' + info.nama + '</strong>' +
                    (info.count > 0
                        ? '<br/><span style="color:#4ade80">&#9679; ' + info.count + ' event</span>'
                        : '<br/><span style="color:#9ca3af">Belum ada event</span>');
                tooltip.classList.remove('hidden');
                tooltip.style.left = (e.originalEvent.clientX + 14) + 'px';
                tooltip.style.top  = (e.originalEvent.clientY - 10) + 'px';
            },
            mousemove(e) {
                tooltip.style.left = (e.originalEvent.clientX + 14) + 'px';
                tooltip.style.top  = (e.originalEvent.clientY - 10) + 'px';
            },
            mouseout() {
                geojsonLayer && geojsonLayer.resetStyle(layer);
                tooltip.classList.add('hidden');
            },
            click() {
                const info = getProvinsiInfo(feature);
                if (info.kode) {
                    window.location.href = baseUrl +
                        '?provinsi=' + encodeURIComponent(info.kode) +
                        '&nama='     + encodeURIComponent(info.nama);
                }
            }
        });
    }

    // ── Fetch GeoJSON dari storage lokal via route Laravel ────────────
    fetch('/api/geojson/indonesia-provinces')
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(geojson => {
            geojsonLayer = L.geoJSON(geojson, {
                style: featureStyle,
                onEachFeature: onEachFeature,
            }).addTo(map);
            map.fitBounds(geojsonLayer.getBounds(), { padding: [10, 10] });
        })
        .catch(err => {
            console.error('Gagal memuat peta:', err);
            document.getElementById('peta-indonesia').innerHTML =
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#9ca3af;font-size:14px;text-align:center;padding:2rem">' +
                '<div><p style="font-weight:600">Gagal memuat peta</p>' +
                '<p style="font-size:12px;margin-top:4px">Error: ' + err.message + '</p></div></div>';
        });

    // ── Tooltip ikuti kursor ──────────────────────────────────────────
    document.addEventListener('mousemove', e => {
        if (!tooltip.classList.contains('hidden')) {
            tooltip.style.left = (e.clientX + 14) + 'px';
            tooltip.style.top  = (e.clientY - 10) + 'px';
        }
    });
})();
</script>
@endpush
