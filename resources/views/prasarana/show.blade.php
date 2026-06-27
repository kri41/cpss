<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Detail Prasarana</h2>
                <p class="text-sm text-slate-500 mt-1">Informasi lengkap fasilitas olahraga</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Foto & Info Utama -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                @if($prasarana->foto_path)
                    <div class="w-full h-64 sm:h-80 bg-slate-100">
                        <img src="{{ Storage::url($prasarana->foto_path) }}" alt="Foto {{ $prasarana->nama_fasilitas }}" class="w-full h-full object-cover">
                    </div>
                @endif
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-2xl font-bold text-slate-800">{{ $prasarana->nama_fasilitas }}</h2>
                                @if($prasarana->status_validasi === 'validated')
                                    <svg class="h-6 w-6 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100">Menunggu Validasi</span>
                                @endif
                            </div>
                            <p class="text-slate-500 mt-1">{{ $prasarana->kategori_olahraga }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('prasarana.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Kembali</a>
                            @auth
                                @if(auth()->user()->canEdit($prasarana))
                                    <a href="{{ route('prasarana.edit', $prasarana) }}" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700">Edit</a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                        <div>
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Club / Komunitas</h3>
                            <p class="mt-1 text-sm text-slate-700">{{ $prasarana->club_komunitas ?: '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Wilayah</h3>
                            <p class="mt-1 text-sm text-slate-700">{{ $prasarana->desa ?? '-' }} / {{ $prasarana->kecamatan ?? '-' }} / {{ $prasarana->kabupaten ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Alamat</h3>
                            <p class="mt-1 text-sm text-slate-700">{{ $prasarana->alamat ?: '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Koordinat</h3>
                            <p class="mt-1 text-sm text-slate-700">{{ $prasarana->latitude ? $prasarana->latitude . ', ' . $prasarana->longitude : '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Dilaporkan Oleh</h3>
                            <p class="mt-1 text-sm text-slate-700">{{ $prasarana->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Tanggal Laporan</h3>
                            <p class="mt-1 text-sm text-slate-700">{{ $prasarana->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kondisi Fasilitas -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Kondisi Fasilitas</h3>
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
                        $ratingColors = [1 => 'bg-red-100 text-red-700', 2 => 'bg-orange-100 text-orange-700', 3 => 'bg-yellow-100 text-yellow-700', 4 => 'bg-blue-100 text-blue-700', 5 => 'bg-green-100 text-green-700'];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($kondisiList as $field => $label)
                            @php $val = $prasarana->$field; @endphp
                            <div class="p-4 rounded-lg border border-slate-100 {{ $val ? ($ratingColors[$val] ?? 'bg-slate-50 text-slate-700') : 'bg-slate-50 text-slate-400' }}">
                                <p class="text-xs font-medium opacity-80">{{ $label }}</p>
                                <div class="flex items-center gap-1 mt-2">
                                    @if($val)
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $val ? 'text-yellow-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-1 text-sm font-semibold">{{ $val }}</span>
                                    @else
                                        <span class="text-sm italic">Belum dinilai</span>
                                    @endif
                                </div>
                                @if($val)
                                    <p class="text-sm font-medium mt-1">{{ $ratingLabels[$val] ?? '' }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex items-center gap-3 p-4 rounded-lg {{ $prasarana->status_color }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <div>
                            <p class="text-xs font-medium opacity-80">Rata-rata Kondisi</p>
                            <p class="text-xl font-bold">{{ $prasarana->average_kondisi }} <span class="text-sm font-normal">/ 5</span> — {{ $prasarana->status }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Akses & Fasilitas -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Akses & Fasilitas Tambahan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
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
                            <div class="flex items-center gap-3 p-3 rounded-lg border {{ $f['value'] ? 'border-green-200 bg-green-50 text-green-700' : 'border-slate-200 bg-slate-50 text-slate-400' }}">
                                @if($f['value'])
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                <span class="text-sm font-medium">{{ $f['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Peta -->
            @if($prasarana->latitude && $prasarana->longitude)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">Lokasi di Peta</h3>
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
                        <div id="map" class="w-full h-72 rounded-xl border border-slate-200 z-0"></div>
                    </div>
                </div>
            @endif
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
</x-app-layout>