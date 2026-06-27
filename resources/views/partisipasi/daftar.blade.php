<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Kehadiran - {{ $partisipasi->lokasi_observasi }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl mb-3">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Daftar Kehadiran</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $partisipasi->lokasi_observasi }}</p>
                <p class="text-xs text-gray-400">{{ $partisipasi->tanggal_observasi->format('d F Y') }}</p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl text-sm border border-green-200 text-center">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('partisipasi.daftar', $partisipasi) }}" class="space-y-4">
                @csrf
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_peserta" id="nama-peserta" required autocomplete="off" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nama Anda" oninput="fetchNamaSuggestions(this.value)">
                    <div id="nama-suggestions-box" class="absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-40 overflow-y-auto hidden"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usia</label>
                        <input type="number" name="usia" min="0" max="120" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Tahun">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Olahraga</label>
                    <select name="jenis_olahraga" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih jenis olahraga</option>
                        @foreach($jenisOlahraga as $jo)
                            <option value="{{ $jo }}">{{ $jo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RPE (1-10)</label>
                    <input type="range" name="rpe" min="1" max="10" value="5" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('rpe-label-daftar').innerText = this.value + ' - ' + rpeText(this.value)">
                    <div class="flex justify-between items-center mt-1">
                        <span id="rpe-label-daftar" class="text-xs font-semibold text-blue-600">5 - Sedang</span>
                        <span class="text-[10px] text-gray-400">1=Sangat Ringan, 10=Maksimal</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelompok Usia</label>
                    <select name="kelompok_usia" class="w-full rounded-lg border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih</option>
                        <option value="Anak">Anak</option>
                        <option value="Remaja">Remaja</option>
                        <option value="Dewasa">Dewasa</option>
                        <option value="Lansia">Lansia</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penyandang Disabilitas</label>
                    <label class="inline-flex items-center text-sm text-gray-700 mt-1">
                        <input type="checkbox" name="kategori_khusus" value="Penyandang Disabilitas" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">Ya, penyandang disabilitas</span>
                    </label>
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Kehadiran
                </button>
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

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">Didaftarkan oleh relawan: {{ $partisipasi->user->name ?? '-' }}</p>
            </div>
        </div>
    </div>
</body>
</html>