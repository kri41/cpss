<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Detail Partisipasi</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Lokasi Observasi</h3>
                            <p class="mt-1 text-lg font-bold text-gray-900">{{ $partisipasi->lokasi_observasi }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Wilayah</h3>
                            <p class="mt-1 text-sm text-gray-700">{{ $partisipasi->desa ?? '-' }} / {{ $partisipasi->kecamatan ?? '-' }} / {{ $partisipasi->kabupaten ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Tanggal Observasi</h3>
                            <p class="mt-1 text-sm text-gray-700">{{ $partisipasi->tanggal_observasi->format('d F Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Estimasi Jumlah Orang</h3>
                            <p class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($partisipasi->estimasi_jumlah_orang) }} orang</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Kehadiran Tercatat</h3>
                            <p class="mt-1 text-2xl font-bold text-green-600">{{ $partisipasi->kehadiran->count() }} orang</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Mayoritas Kelompok Usia</h3>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $partisipasi->mayoritas_usia }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Dicatat Oleh</h3>
                            <p class="mt-1 text-sm text-gray-700">{{ $partisipasi->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Waktu Pencatatan</h3>
                            <p class="mt-1 text-sm text-gray-700">{{ $partisipasi->created_at->format('d F Y H:i') }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Status Validasi</h3>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $partisipasi->status_validasi === 'validated' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                                    {{ $partisipasi->status_validasi === 'validated' ? 'Tervalidasi' : 'Menunggu Validasi' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->canEdit($partisipasi) || auth()->user()->isAdmin())
                            <div class="mt-8 p-6 bg-blue-50 rounded-2xl border border-blue-100">
                                <div class="flex flex-col md:flex-row items-center gap-6">
                                    <div class="text-center md:text-left flex-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">QR Code Partisipasi</h3>
                                        <p class="text-sm text-gray-600 mb-4">Bagikan QR ini ke partisipan agar mereka bisa mendaftarkan kehadiran sendiri.</p>
                                        <a href="{{ route('partisipasi.qr.show', $partisipasi) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 20h2v-4H6v4zm6-6h2v-4h-2v4zm-6 0h2v-4H6v4zm12-6h2V4h-2v4zM6 10h2V4H6v6zm6-6h2V4h-2v4z"/></svg>
                                            Lihat QR Code
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth

                    @auth
                        @if(auth()->user()->canEdit($partisipasi) || auth()->user()->isAdmin())
                            <div class="mt-8">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Kehadiran Manual</h3>
                                <form method="POST" action="{{ route('partisipasi.kehadiran.store', $partisipasi) }}" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 relative">
                                    @csrf
                                    <div class="col-span-2 relative">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Peserta</label>
                                        <input type="text" name="nama_peserta" id="nama-peserta" required autocomplete="off" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nama lengkap" oninput="fetchNamaSuggestions(this.value)">
                                        <div id="nama-suggestions-box" class="absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-40 overflow-y-auto hidden"></div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Jenis Kelamin</label>
                                        <div class="flex items-center gap-3 mt-2">
                                            <label class="inline-flex items-center text-sm text-gray-700">
                                                <input type="radio" name="jenis_kelamin" value="L" class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                                <span class="ml-1">Laki-laki</span>
                                            </label>
                                            <label class="inline-flex items-center text-sm text-gray-700">
                                                <input type="radio" name="jenis_kelamin" value="P" class="text-blue-600 focus:ring-blue-500 border-gray-300">
                                                <span class="ml-1">Perempuan</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Jenis Olahraga</label>
                                        <select name="jenis_olahraga" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Pilih</option>
                                            @foreach($jenisOlahraga as $jo)
                                                <option value="{{ $jo }}">{{ $jo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">RPE (1-10)</label>
                                        <input type="range" name="rpe" min="1" max="10" value="5" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('rpe-label').innerText = this.value + ' - ' + rpeText(this.value)">
                                        <div class="flex justify-between items-center mt-1">
                                            <span id="rpe-label" class="text-xs font-semibold text-blue-600">5 - Sedang</span>
                                            <span class="text-[10px] text-gray-400">1=Sangat Ringan, 10=Maksimal</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Usia</label>
                                        <input type="number" name="usia" min="0" max="120" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Tahun">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Penyandang Disabilitas</label>
                                        <label class="inline-flex items-center text-sm text-gray-700 mt-2">
                                            <input type="checkbox" name="kategori_khusus" value="Penyandang Disabilitas" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2">Ya, penyandang disabilitas</span>
                                        </label>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">Tambah</button>
                                    </div>
                                </form>
                                <script>
                                    function rpeText(val) {
                                        const labels = {1:'Sangat Ringan',2:'Ringan',3:'Agak Ringan',4:'Sedang-Ringan',5:'Sedang',6:'Sedang-Berat',7:'Berat',8:'Sangat Berat',9:'Hampir Maksimal',10:'Maksimal'};
                                        return labels[val] || '';
                                    }
                                    let debounceTimer;
                                    function fetchNamaSuggestions(q) {
                                        const box = document.getElementById('nama-suggestions-box');
                                        if (q.length < 2) { box.classList.add('hidden'); return; }
                                        clearTimeout(debounceTimer);
                                        debounceTimer = setTimeout(() => {
                                            fetch('/api/kehadiran/autocomplete-nama?q=' + encodeURIComponent(q))
                                                .then(r => r.json())
                                                .then(data => {
                                                    box.innerHTML = '';
                                                    if (data.length === 0) { box.classList.add('hidden'); return; }
                                                    data.forEach(nama => {
                                                        const div = document.createElement('div');
                                                        div.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer';
                                                        div.textContent = nama;
                                                        div.onclick = function() {
                                                            document.getElementById('nama-peserta').value = nama;
                                                            box.classList.add('hidden');
                                                        };
                                                        box.appendChild(div);
                                                    });
                                                    box.classList.remove('hidden');
                                                });
                                        }, 300);
                                    }
                                    document.addEventListener('click', function(e) {
                                        if (!e.target.closest('#nama-peserta')) {
                                            document.getElementById('nama-suggestions-box').classList.add('hidden');
                                        }
                                    });
                                </script>
                            </div>
                        @endif
                    @endauth

                    <div class="mt-10">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Kehadiran Individu</h3>
                        @if($partisipasi->kehadiran->count() > 0)
                            <div class="overflow-x-auto rounded-xl border border-gray-100">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Gender</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Olahraga</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">RPE</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usia</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kelompok</th>
                                            @auth
                                                @if(auth()->user()->canEdit($partisipasi) || auth()->user()->isAdmin())
                                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                                                @endif
                                            @endauth
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($partisipasi->kehadiran as $k)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $k->nama_peserta }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $k->jenis_kelamin ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $k->jenis_olahraga ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $k->rpe ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $k->usia ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $k->kelompok_usia ?? '-' }}</td>
                                            @auth
                                                @if(auth()->user()->canEdit($partisipasi) || auth()->user()->isAdmin())
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                        <form method="POST" action="{{ route('partisipasi.kehadiran.destroy', $k) }}" onsubmit="return confirm('Yakin ingin menghapus kehadiran ini?')" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
                                                    </td>
                                                @endif
                                            @endauth
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-xl p-8 text-center text-gray-500">
                                <p>Belum ada data kehadiran individu untuk observasi ini.</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-8 space-x-3">
                        <a href="{{ route('partisipasi.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Kembali</a>
                        @auth
                            @if(auth()->user()->canEdit($partisipasi))
                                <a href="{{ route('partisipasi.edit', $partisipasi) }}" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition">Edit</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>