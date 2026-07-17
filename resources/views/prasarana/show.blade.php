@extends('layouts.public')

@section('title', $prasarana->nama_fasilitas . ' - Dataraga')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
<div class="min-h-[calc(100vh-3.5rem)] bg-gray-50">
    <!-- Hero Header -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('prasarana.index') }}" class="hover:text-blue-600 transition">Prasarana</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-700 font-medium">Detail</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-sky-100 text-sky-700">{{ $prasarana->kategori_olahraga }}</span>
                        @if($prasarana->status_validasi === 'validated')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 flex items-center gap-1">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Tervalidasi
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Menunggu Validasi</span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">{{ $prasarana->nama_fasilitas }}</h1>
                    <p class="mt-2 text-gray-500 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        {{ $prasarana->desa ?? '-' }} / {{ $prasarana->kecamatan ?? '-' }} / {{ $prasarana->kabupaten ?? '-' }}
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('prasarana.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                        Kembali
                    </a>
                    @auth
                        @if(auth()->user()->canEdit($prasarana))
                            <a href="{{ route('prasarana.edit', $prasarana) }}" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm font-medium hover:bg-sky-700 transition shadow-sm">
                                Edit
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Foto -->
        @php
            $semuaFoto = [];
            if ($prasarana->foto_path) $semuaFoto[] = Storage::url($prasarana->foto_path);
            foreach ($prasarana->foto_tambahan ?? [] as $fp) {
                $semuaFoto[] = Storage::url($fp);
            }
        @endphp
        @if(count($semuaFoto) > 0)
        <div x-data="{ aktif: 0 }" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="relative w-full bg-gray-100">
                <template x-for="(url, i) in {{ json_encode($semuaFoto) }}" :key="i">
                    <img :src="url" :alt="'Foto ' + (i+1)" x-show="aktif === i"
                         class="w-full h-64 sm:h-96 object-cover transition-opacity duration-300">
                </template>
                @if(count($semuaFoto) > 1)
                <button @click="aktif = (aktif - 1 + {{ count($semuaFoto) }}) % {{ count($semuaFoto) }}"
                        class="absolute left-3 top-1/2 -translate-y-1/2 p-2 bg-black/40 hover:bg-black/60 text-white rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="aktif = (aktif + 1) % {{ count($semuaFoto) }}"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-2 bg-black/40 hover:bg-black/60 text-white rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                    @for($i = 0; $i < count($semuaFoto); $i++)
                    <button @click="aktif = {{ $i }}"
                            :class="aktif === {{ $i }} ? 'bg-white' : 'bg-white/40'"
                            class="w-2 h-2 rounded-full transition-colors"></button>
                    @endfor
                </div>
                @endif
            </div>
            @if(count($semuaFoto) > 1)
            <div class="flex gap-2 p-3 overflow-x-auto">
                @foreach($semuaFoto as $i => $url)
                <img src="{{ $url }}" alt="" @click="aktif = {{ $i }}"
                     :class="aktif === {{ $i }} ? 'ring-2 ring-blue-500' : 'opacity-60'"
                     class="h-14 w-14 object-cover rounded-lg cursor-pointer shrink-0 transition hover:opacity-100">
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Umum -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Informasi Umum</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Club / Komunitas</p>
                                <p class="font-semibold text-gray-900">{{ $prasarana->club_komunitas ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Alamat</p>
                                <p class="font-semibold text-gray-900">{{ $prasarana->alamat ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Koordinat</p>
                                <p class="font-semibold text-gray-900">{{ $prasarana->latitude ? $prasarana->latitude . ', ' . $prasarana->longitude : '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Dilaporkan Oleh</p>
                                <p class="font-semibold text-gray-900">{{ $prasarana->user->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kondisi Fasilitas -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Kondisi Fasilitas</h2>
                    @php
                        $kondisiList = [
                            'kondisi_lantai' => 'Lantai',
                            'kondisi_ring' => 'Ring',
                            'kondisi_net' => 'Net',
                            'kondisi_gawang' => 'Gawang',
                            'kondisi_lapangan' => 'Lapangan',
                            'kondisi_ventilasi' => 'Ventilasi',
                            'kondisi_pencahayaan' => 'Pencahayaan',
                            'kondisi_kamar_mandi' => 'Kamar Mandi',
                        ];
                        $ratingLabels = [1 => 'Buruk Sekali', 2 => 'Buruk', 3 => 'Cukup', 4 => 'Baik', 5 => 'Baik Sekali'];
                        $ratingColors = [1 => 'bg-red-100 text-red-700 border-red-200', 2 => 'bg-orange-100 text-orange-700 border-orange-200', 3 => 'bg-yellow-100 text-yellow-700 border-yellow-200', 4 => 'bg-blue-100 text-blue-700 border-blue-200', 5 => 'bg-emerald-100 text-emerald-700 border-emerald-200'];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach($kondisiList as $field => $label)
                            @php $val = $prasarana->$field; @endphp
                            <div class="p-3 rounded-lg border {{ $val ? ($ratingColors[$val] ?? 'bg-gray-50 text-gray-700 border-gray-200') : 'bg-gray-50 text-gray-400 border-gray-100' }}">
                                <p class="text-xs font-medium opacity-80">{{ $label }}</p>
                                <div class="flex items-center gap-1 mt-1.5">
                                    @if($val)
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $val ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-sm font-bold">{{ $val }}</span>
                                    @else
                                        <span class="text-xs italic">Belum dinilai</span>
                                    @endif
                                </div>
                                @if($val)
                                    <p class="text-xs font-semibold mt-1">{{ $ratingLabels[$val] ?? '' }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center gap-3 p-4 rounded-lg {{ $prasarana->status_color }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <div>
                            <p class="text-xs font-medium opacity-80">Rata-rata Kondisi</p>
                            <p class="text-xl font-bold">{{ $prasarana->average_kondisi }} <span class="text-sm font-normal">/ 5</span> — {{ $prasarana->status }}</p>
                        </div>
                    </div>
                </div>

                <!-- Akses & Fasilitas -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Akses & Fasilitas Tambahan</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $fasilitas = [
                                ['label' => 'Ramah Disabilitas', 'value' => $prasarana->akses_disabilitas],
                                ['label' => 'Akses Parkir', 'value' => $prasarana->akses_parkir],
                                ['label' => 'Akses Transportasi', 'value' => $prasarana->akses_transportasi],
                                ['label' => 'Ruang Ganti', 'value' => $prasarana->fasilitas_ruang_ganti],
                                ['label' => 'Tribun Penonton', 'value' => $prasarana->fasilitas_tribun],
                            ];
                        @endphp
                        @foreach($fasilitas as $f)
                            <div class="flex items-center gap-3 p-3 rounded-lg border {{ $f['value'] ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-400' }}">
                                @if($f['value'])
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                @endif
                                <span class="text-sm font-medium">{{ $f['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Peta -->
                @if($prasarana->latitude && $prasarana->longitude)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Lokasi di Peta</h2>
                    <div id="map" class="w-full h-72 rounded-xl border border-gray-200 z-0"></div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Detail Laporan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dilaporkan</span>
                            <span class="text-gray-900 font-medium">{{ $prasarana->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Diupdate</span>
                            <span class="text-gray-900 font-medium">{{ $prasarana->updated_at->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Oleh</span>
                            <span class="text-gray-900 font-medium">{{ $prasarana->user->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($prasarana->latitude && $prasarana->longitude)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $prasarana->latitude }};
        const lng = {{ $prasarana->longitude }};
        const map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map);
    });
</script>
@endpush
@endif
@endsection
