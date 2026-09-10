<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Daftar Lokasi Sasaran Asesor</h2>
                <p class="text-sm text-gray-500 mt-1">Untuk dicetak/dibagikan ke asesor sebelum turun lapangan &mdash; blind, tanpa data kondisi.</p>
            </div>
            <a href="{{ route('asesmen-pakar.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium shrink-0">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filter --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kab/Kota</label>
                        <select name="kabupaten" class="w-full rounded-lg border-gray-200 text-sm">
                            <option value="">Semua</option>
                            @foreach($kabupatenList as $k)
                                <option value="{{ $k }}" @selected($filter['kabupaten'] === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kecamatan</label>
                        <select name="kecamatan" class="w-full rounded-lg border-gray-200 text-sm">
                            <option value="">Semua</option>
                            @foreach($kecamatanList as $k)
                                <option value="{{ $k }}" @selected($filter['kecamatan'] === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status Data</label>
                        <select name="status" class="w-full rounded-lg border-gray-200 text-sm">
                            <option value="validated" @selected($filter['status'] === 'validated')>Tervalidasi saja</option>
                            <option value="semua" @selected($filter['status'] === 'semua')>Semua status</option>
                            <option value="pending" @selected($filter['status'] === 'pending')>Pending saja</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="belum" value="1" @checked($filter['belum']) class="rounded border-gray-300 text-blue-600">
                            Belum diasesmen
                        </label>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-4 flex flex-wrap gap-2 pt-1">
                        <button class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Terapkan</button>
                        <a href="{{ route('asesmen-pakar.lokasi.csv', request()->query()) }}" class="px-5 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">Unduh CSV</a>
                        <a href="{{ route('asesmen-pakar.lokasi.pdf', request()->query()) }}" class="px-5 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900">Daftar Lokasi (PDF)</a>
                        <a href="{{ route('asesmen-pakar.borang.pdf', request()->query()) }}" class="px-5 py-2 bg-amber-600 text-white text-sm font-semibold rounded-lg hover:bg-amber-700">Borang Kosong (PDF)</a>
                    </div>
                </form>
                <p class="text-xs text-gray-400 mt-3"><strong>Borang Kosong</strong> = lembar audit polos (tanpa logo) satu halaman per prasarana, untuk diisi tangan oleh asesor.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-900">{{ $rows->count() }} lokasi</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-5 py-3 text-left w-14">ID</th>
                                <th class="px-4 py-3 text-left">Nama Fasilitas</th>
                                <th class="px-4 py-3 text-left">Kategori</th>
                                <th class="px-4 py-3 text-left">Koordinat</th>
                                <th class="px-4 py-3 text-left">Wilayah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($rows as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-mono text-gray-500">#{{ $p->id }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $p->nama_fasilitas }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $p->jenisOlahraga->pluck('nama')->implode(', ') ?: '-' }}</td>
                                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">
                                        @if($p->latitude && $p->longitude){{ $p->latitude }}, {{ $p->longitude }}
                                        @else <span class="text-gray-300">tidak ada</span> @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ collect([$p->desa, $p->kecamatan, $p->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 italic">Tidak ada lokasi yang cocok dengan filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
