<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Edit Prasarana</h2>
                <p class="text-sm text-slate-500 mt-1">Perbarui data prasarana olahraga</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('prasarana.update', $prasarana) }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Informasi Dasar -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Informasi Dasar</h3>
                            <div>
                                <label for="nama_fasilitas" class="block text-sm font-medium text-slate-700">Nama Fasilitas <span class="text-red-500">*</span></label>
                                <input id="nama_fasilitas" type="text" name="nama_fasilitas" value="{{ old('nama_fasilitas', $prasarana->nama_fasilitas) }}" required class="mt-1 block w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm" placeholder="Contoh: Lapangan Sepak Bola PPG">
                                @error('nama_fasilitas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium text-slate-700">Jenis Olahraga <span class="text-red-500">*</span> <span class="text-slate-400 font-normal">(bisa pilih lebih dari satu, fasilitas sering multifungsi)</span></label>
                                <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                    @php $oldJenis = old('jenis_olahraga_id', $prasarana->jenisOlahraga->pluck('id')->toArray()); @endphp
                                    @foreach($jenisOlahragaList as $j)
                                        <label class="flex items-center p-2.5 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 has-[:checked]:bg-sky-50 has-[:checked]:border-sky-300">
                                            <input type="checkbox" name="jenis_olahraga_id[]" value="{{ $j->id }}" {{ in_array($j->id, $oldJenis) ? 'checked' : '' }} class="w-4 h-4 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                                            <span class="ml-2 text-sm text-slate-700">{{ $j->nama }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('jenis_olahraga_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                @error('jenis_olahraga_id.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Lokasi & Peta -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Lokasi</h3>
                            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
                            <div class="mb-4">
                                <div id="map" class="w-full h-64 rounded-xl border border-slate-200 z-0"></div>
                                <p class="text-xs text-slate-500 mt-1">Klik pada peta untuk mengisi koordinat latitude dan longitude.</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="latitude" class="block text-sm font-medium text-slate-700">Latitude</label>
                                    <input id="latitude" type="number" step="any" name="latitude" value="{{ old('latitude', $prasarana->latitude) }}" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm" placeholder="-6.123456">
                                    @error('latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="longitude" class="block text-sm font-medium text-slate-700">Longitude</label>
                                    <input id="longitude" type="number" step="any" name="longitude" value="{{ old('longitude', $prasarana->longitude) }}" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm" placeholder="106.123456">
                                    @error('longitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="alamat" class="block text-sm font-medium text-slate-700">Alamat Lengkap</label>
                                <textarea id="alamat" name="alamat" rows="2" class="mt-1 block w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500 shadow-sm" placeholder="Jalan, RT/RW, patokan">{{ old('alamat', $prasarana->alamat) }}</textarea>
                                @error('alamat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Wilayah -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Wilayah Administrasi</h3>
                            <x-wilayah-dropdown :selectedProvinsi="$prasarana->provinsi" :selectedKabupaten="$prasarana->kabupaten" :selectedKecamatan="$prasarana->kecamatan" :selectedDesa="$prasarana->desa" />
                        </div>

                        <!-- Kondisi Fasilitas (1-5) -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Kondisi Fasilitas <span class="text-sm font-normal text-slate-500">(1 = Buruk Sekali, 5 = Baik Sekali)</span></h3>
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
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                @foreach($kondisiList as $field => $label)
                                    @php $currentVal = old($field, $prasarana->$field); @endphp
                                    <div class="star-rating-group" data-field="{{ $field }}">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">{{ $label }}</label>
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" data-value="{{ $i }}" class="star-btn p-1 transition-transform hover:scale-110 focus:outline-none">
                                                    <svg class="w-8 h-8 star-icon {{ $currentVal && $i <= $currentVal ? 'text-yellow-400' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </button>
                                            @endfor
                                            <span class="ml-2 text-sm font-medium rating-label {{ $currentVal ? 'text-slate-700' : 'text-slate-400' }}">
                                                {{ $currentVal ? $currentVal . ' — ' . ($ratingLabels[$currentVal] ?? '') : 'Belum dinilai' }}
                                            </span>
                                        </div>
                                        <input type="hidden" name="{{ $field }}" id="{{ $field }}" value="{{ $currentVal }}">
                                        @error($field)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Akses & Fasilitas -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 mb-4">Akses & Fasilitas Tambahan</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" name="akses_disabilitas" value="1" {{ old('akses_disabilitas', $prasarana->akses_disabilitas) ? 'checked' : '' }} class="w-4 h-4 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                                    <span class="ml-2 text-sm text-slate-700">Ramah Disabilitas</span>
                                </label>
                                <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" name="akses_parkir" value="1" {{ old('akses_parkir', $prasarana->akses_parkir) ? 'checked' : '' }} class="w-4 h-4 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                                    <span class="ml-2 text-sm text-slate-700">Akses Parkir</span>
                                </label>
                                <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" name="akses_transportasi" value="1" {{ old('akses_transportasi', $prasarana->akses_transportasi) ? 'checked' : '' }} class="w-4 h-4 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                                    <span class="ml-2 text-sm text-slate-700">Akses Transportasi</span>
                                </label>
                                <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" name="fasilitas_ruang_ganti" value="1" {{ old('fasilitas_ruang_ganti', $prasarana->fasilitas_ruang_ganti) ? 'checked' : '' }} class="w-4 h-4 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                                    <span class="ml-2 text-sm text-slate-700">Ruang Ganti</span>
                                </label>
                                <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="checkbox" name="fasilitas_tribun" value="1" {{ old('fasilitas_tribun', $prasarana->fasilitas_tribun) ? 'checked' : '' }} class="w-4 h-4 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                                    <span class="ml-2 text-sm text-slate-700">Tribun Penonton</span>
                                </label>
                            </div>
                        </div>

                        <!-- Foto -->
                        <div x-data="fotoUpload()" class="space-y-4">
                            <div>
                                <label for="foto" class="block text-sm font-medium text-slate-700">Foto Utama Fasilitas</label>
                                @if($prasarana->foto_path)
                                    <div class="mt-2 mb-2">
                                        <img src="{{ Storage::url($prasarana->foto_path) }}" alt="Foto Utama" class="h-32 object-cover rounded-lg border border-slate-200">
                                    </div>
                                @endif
                                <input id="foto" type="file" name="foto" accept="image/*"
                                    @change="previewUtama($event)"
                                    class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
                                <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah foto utama</p>
                                @error('foto')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                <template x-if="previewUrlUtama">
                                    <img :src="previewUrlUtama" alt="" class="mt-2 h-32 w-full object-cover rounded-lg border border-slate-200">
                                </template>
                            </div>

                            @if($prasarana->foto_tambahan && count($prasarana->foto_tambahan) > 0)
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Foto Tambahan Saat Ini</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($prasarana->foto_tambahan as $fotoPath)
                                    <div class="relative group">
                                        <img src="{{ Storage::url($fotoPath) }}" alt="" class="h-20 w-20 object-cover rounded-lg border border-slate-200">
                                        <label class="absolute inset-0 flex items-center justify-center bg-red-500/70 opacity-0 group-hover:opacity-100 rounded-lg cursor-pointer transition">
                                            <input type="checkbox" name="hapus_foto_tambahan[]" value="{{ $fotoPath }}" class="sr-only" @change="$el.closest('.relative').classList.toggle('opacity-50', $el.checked)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Hover foto dan centang ikon hapus untuk menghapus</p>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-slate-700">
                                    Tambah Foto Baru <span class="text-slate-400 font-normal">(Maks 4 foto total, tiap foto maks 2MB)</span>
                                </label>
                                <input type="file" name="foto_tambahan[]" accept="image/*" multiple
                                    @change="previewTambahan($event)"
                                    class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100" />
                                @error('foto_tambahan.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <template x-for="(url, i) in previewUrlsTambahan" :key="i">
                                        <img :src="url" alt="" class="h-20 w-20 object-cover rounded-lg border border-slate-200">
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('prasarana.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Batal</a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function fotoUpload() {
        return {
            previewUrlUtama: null,
            previewUrlsTambahan: [],
            previewUtama(e) {
                const file = e.target.files[0];
                if (file) this.previewUrlUtama = URL.createObjectURL(file);
            },
            previewTambahan(e) {
                this.previewUrlsTambahan = Array.from(e.target.files).slice(0, 4).map(f => URL.createObjectURL(f));
            }
        }
    }
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Leaflet Map ---
            const defaultLat = parseFloat(document.getElementById('latitude').value) || -6.2;
            const defaultLng = parseFloat(document.getElementById('longitude').value) || 106.8;
            const map = L.map('map').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let marker = null;
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

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
            });

            if (latInput.value && lngInput.value) {
                setMarker(parseFloat(latInput.value), parseFloat(lngInput.value));
                map.setView([parseFloat(latInput.value), parseFloat(lngInput.value)], 15);
            }

            // --- Star Rating ---
            const ratingLabels = {1: 'Buruk Sekali', 2: 'Buruk', 3: 'Cukup', 4: 'Baik', 5: 'Baik Sekali'};
            const ratingColors = ['', 'text-red-400', 'text-orange-400', 'text-yellow-400', 'text-sky-400', 'text-emerald-400'];

            document.querySelectorAll('.star-rating-group').forEach(group => {
                const field = group.dataset.field;
                const hiddenInput = group.querySelector('input[type="hidden"]');
                const label = group.querySelector('.rating-label');
                const stars = group.querySelectorAll('.star-btn');
                let currentValue = parseInt(hiddenInput.value) || 0;

                function updateStars(val, isHover = false) {
                    stars.forEach((btn, idx) => {
                        const icon = btn.querySelector('.star-icon');
                        const starVal = idx + 1;
                        if (starVal <= val) {
                            icon.classList.remove('text-slate-300');
                            icon.classList.add(ratingColors[val] || 'text-yellow-400');
                        } else {
                            icon.classList.add('text-slate-300');
                            icon.classList.remove('text-red-400', 'text-orange-400', 'text-yellow-400', 'text-sky-400', 'text-emerald-400');
                        }
                    });
                    if (val > 0) {
                        label.textContent = val + ' — ' + ratingLabels[val];
                        label.className = 'ml-2 text-sm font-medium ' + (ratingColors[val].replace('text-', 'text-'));
                    } else if (!isHover) {
                        label.textContent = 'Belum dinilai';
                        label.className = 'ml-2 text-sm font-medium text-slate-400';
                    }
                }

                stars.forEach((btn, idx) => {
                    btn.addEventListener('mouseenter', () => updateStars(idx + 1, true));
                    btn.addEventListener('mouseleave', () => updateStars(currentValue, false));
                    btn.addEventListener('click', () => {
                        currentValue = idx + 1;
                        hiddenInput.value = currentValue;
                        updateStars(currentValue);
                    });
                });

                if (currentValue > 0) updateStars(currentValue);
            });
        });
    </script>
    @endpush
</x-app-layout>