<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Club</h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi club</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('clubs.update', $club) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                @unless($canEditDirectly)
                <div class="flex gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-amber-800">Data ini sudah tervalidasi / bukan milik Anda. Perubahan yang Anda kirim akan diajukan sebagai <strong>usulan perubahan</strong> untuk ditinjau admin, tidak langsung diterapkan. Logo dan jadwal latihan tidak bisa diusulkan lewat form ini.</p>
                </div>
                @endunless

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Informasi Dasar -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Club <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_club" value="{{ old('nama_club', $club->nama_club) }}" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ketua Club <span class="text-red-500">*</span></label>
                                    <input type="text" name="ketua_club" value="{{ old('ketua_club', $club->ketua_club) }}" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Narahubung <span class="text-red-500">*</span></label>
                                    <input type="text" name="narahubung" value="{{ old('narahubung', $club->narahubung) }}" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                                    <input type="tel" name="no_telepon" value="{{ old('no_telepon', $club->no_telepon) }}" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $club->email) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berdiri</label>
                                    <input type="date" name="tanggal_berdiri" value="{{ old('tanggal_berdiri', $club->tanggal_berdiri?->format('Y-m-d')) }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                    <textarea name="alamat" rows="2"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('alamat', $club->alamat) }}</textarea>
                                </div>

                                <div class="md:col-span-2">
                                    <x-wilayah-dropdown :selectedProvinsi="$club->provinsi" :selectedKabupaten="$club->kabupaten" :selectedKecamatan="$club->kecamatan" :selectedDesa="$club->desa" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="deskripsi" rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('deskripsi', $club->deskripsi) }}</textarea>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="aktif" value="1" {{ old('aktif', $club->aktif) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Club Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Prasarana -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prasarana</h3>
                            <select name="prasarana_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Pilih Prasarana (Opsional)</option>
                                @foreach($prasarana as $p)
                                    <option value="{{ $p->id }}" {{ old('prasarana_id', $club->prasarana_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_fasilitas }} ({{ $p->kategori_olahraga }})
                                    </option>
                                @endforeach
                            </select>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Olahraga</label>
                                <select name="jenis_olahraga_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <option value="">Pilih Jenis Olahraga (Opsional)</option>
                                    @foreach($jenisOlahragaList as $j)
                                        <option value="{{ $j->id }}" {{ old('jenis_olahraga_id', $club->jenis_olahraga_id) == $j->id ? 'selected' : '' }}>
                                            {{ $j->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Digunakan untuk auto-isi olahraga saat anggota check-in QR Kampung Olahraga</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        @if($canEditDirectly)
                        <!-- Logo -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Logo Club</h3>
                            <div class="space-y-4">
                                <div id="logo-preview" class="w-32 h-32 mx-auto rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden">
                                    @if($club->logo_path)
                                        <img src="{{ Storage::url($club->logo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-4xl font-bold text-gray-400">{{ substr($club->nama_club, 0, 1) }}</span>
                                    @endif
                                </div>
                                <input type="file" name="logo" id="logo-input" accept="image/*"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah logo</p>
                            </div>
                        </div>

                        <!-- Jadwal Latihan -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Latihan</h3>
                            <div id="jadwal-container" class="space-y-3">
                                @foreach($club->jadwalLatihan as $index => $jadwal)
                                    <div class="jadwal-item grid grid-cols-3 gap-2">
                                        <select name="jadwal[{{ $index }}][hari]" class="col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            <option value="">Hari</option>
                                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                                                <option value="{{ $h }}" {{ $jadwal->hari == $h ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                        <input type="time" name="jadwal[{{ $index }}][jam_mulai]" value="{{ $jadwal->jam_mulai->format('H:i') }}" class="col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <div class="flex gap-2">
                                            <input type="time" name="jadwal[{{ $index }}][jam_selesai]" value="{{ $jadwal->jam_selesai->format('H:i') }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            @if($index > 0)
                                                <button type="button" onclick="hapusJadwal(this)" class="text-red-500 hover:text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                @if($club->jadwalLatihan->count() == 0)
                                    <div class="jadwal-item grid grid-cols-3 gap-2">
                                        <select name="jadwal[0][hari]" class="col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            <option value="">Hari</option>
                                            <option value="Senin">Senin</option>
                                            <option value="Selasa">Selasa</option>
                                            <option value="Rabu">Rabu</option>
                                            <option value="Kamis">Kamis</option>
                                            <option value="Jumat">Jumat</option>
                                            <option value="Sabtu">Sabtu</option>
                                            <option value="Minggu">Minggu</option>
                                        </select>
                                        <input type="time" name="jadwal[0][jam_mulai]" class="col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <input type="time" name="jadwal[0][jam_selesai]" class="col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="tambahJadwal()" class="mt-3 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                + Tambah Jadwal
                            </button>
                        </div>
                        @endif

                        @unless($canEditDirectly)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Usulan Perubahan <span class="text-red-500">*</span></label>
                            <textarea name="alasan" rows="3" required minlength="10" placeholder="Jelaskan kenapa data ini perlu diubah..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('alasan') }}</textarea>
                            @error('alasan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        @endunless

                        <!-- Action Buttons -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="space-y-3">
                                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition-colors">
                                    {{ $canEditDirectly ? 'Simpan Perubahan' : 'Ajukan Perubahan' }}
                                </button>
                                <a href="{{ route('clubs.show', $club) }}" class="block w-full py-3 px-4 bg-gray-100 text-gray-700 text-center rounded-xl font-medium hover:bg-gray-200 transition-colors">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let jadwalCount = {{ $club->jadwalLatihan->count() > 0 ? $club->jadwalLatihan->count() : 1 }};
        
        function tambahJadwal() {
            const container = document.getElementById('jadwal-container');
            const newJadwal = document.createElement('div');
            newJadwal.className = 'jadwal-item grid grid-cols-3 gap-2';
            newJadwal.innerHTML = `
                <select name="jadwal[${jadwalCount}][hari]" class="col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Hari</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                    <option value="Minggu">Minggu</option>
                </select>
                <input type="time" name="jadwal[${jadwalCount}][jam_mulai]" class="col-span-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <div class="flex gap-2">
                    <input type="time" name="jadwal[${jadwalCount}][jam_selesai]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <button type="button" onclick="hapusJadwal(this)" class="text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(newJadwal);
            jadwalCount++;
        }

        function hapusJadwal(btn) {
            btn.closest('.jadwal-item').remove();
        }

        // Logo preview
        document.getElementById('logo-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
    @endpush
</x-app-layout>
