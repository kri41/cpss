<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Prasarana Olahraga</h2>
                <p class="text-xs text-gray-500 mt-0.5">Data fasilitas olahraga yang dilaporkan relawan.</p>
            </div>
            @if(auth()->user()?->isAdmin() || auth()->user()?->isRelawan())
            <a href="{{ route('prasarana.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Prasarana
            </a>
            @endif
        </div>
    </x-slot>

<div x-data="{ verifyOpen: false, rejectOpen: false, selected: null }">
    <!-- Ringkasan + Filter (satu bar ramping) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5">
            <!-- Ringkasan inline -->
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm mb-4 pb-4 border-b border-gray-100">
                <span class="font-bold text-gray-900">{{ $stats['total'] }}<span class="font-normal text-gray-400 ml-1">total</span></span>
                <span class="text-gray-300">&middot;</span>
                <span class="font-semibold text-gray-700">{{ $stats['avg_kondisi'] }}<span class="font-normal text-gray-400">/5</span><span class="text-gray-400 ml-1">rata-rata kondisi</span></span>
                <span class="text-gray-300">&middot;</span>
                <span class="font-semibold text-gray-700 truncate max-w-[10rem]">{{ $stats['wilayah_terbanyak'] }}<span class="font-normal text-gray-400 ml-1">terbanyak</span></span>
                <span class="text-gray-300">&middot;</span>
                <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $stats['tervalidasi'] }} tervalidasi</span>
                <span class="inline-flex items-center gap-1 text-amber-700 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $stats['pending'] }} pending</span>
                <span class="inline-flex items-center gap-1 text-red-700 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>{{ $stats['ditolak'] }} ditolak</span>
            </div>

            <!-- Filter Row -->
            <form method="GET" action="{{ route('dashboard.prasarana') }}" class="flex flex-wrap items-end gap-2 sm:gap-3">
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari Fasilitas</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama fasilitas..." class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                </div>
                <div class="flex-1 min-w-[calc(50%-4px)] sm:flex-none sm:min-w-0 sm:w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kabupaten</label>
                    <select name="kabupaten" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach($kabupatenList as $k)
                            <option value="{{ $k }}" {{ request('kabupaten') == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[calc(50%-4px)] sm:flex-none sm:min-w-0 sm:w-40">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kecamatan</label>
                    <select name="kecamatan" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach($kecamatanList as $k)
                            <option value="{{ $k }}" {{ request('kecamatan') == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[calc(50%-4px)] sm:flex-none sm:min-w-0 sm:w-44">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kategori Olahraga</label>
                    <select name="kategori" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[calc(50%-4px)] sm:flex-none sm:min-w-0 sm:w-36">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Tervalidasi</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto flex flex-wrap gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">Filter</button>
                    <a href="{{ route('dashboard.prasarana') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- List Prasarana -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 gap-4">
            @forelse($prasarana as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            @if($item->foto_path)
                                <img src="{{ Storage::url($item->foto_path) }}" alt="" class="h-14 w-14 rounded-xl object-cover shrink-0 bg-gray-100">
                            @else
                                <div class="h-14 w-14 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-base font-semibold text-gray-900 truncate">{{ $item->nama_fasilitas }}</h3>
                                    @if($item->status_validasi === 'validated')
                                        <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-500">
                                    @foreach($item->jenisOlahraga as $j)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700">{{ $j->nama }}</span>
                                    @endforeach
                                    <span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>{{ $item->desa ?? '-' }}, {{ $item->kecamatan ?? '-' }}</span>
                                    <span class="inline-flex items-center gap-1 text-xs">Kondisi: <strong class="text-gray-700">{{ $item->average_kondisi }}/5</strong><span class="inline-block w-12 h-1.5 bg-gray-200 rounded-full overflow-hidden align-middle ml-1"><span class="block h-full {{ $item->average_kondisi >= 4 ? 'bg-green-500' : ($item->average_kondisi >= 3 ? 'bg-blue-500' : ($item->average_kondisi >= 2 ? 'bg-amber-400' : 'bg-red-500')) }}" style="width: {{ min(100, max(0, ($item->average_kondisi / 5) * 100)) }}%"></span></span></span>
                                </div>
                                @if($item->status_validasi === 'rejected' && auth()->user()?->isAdmin() && $item->komentar_validasi)
                                    <p class="mt-2 text-xs text-red-700 bg-red-50 border border-red-100 rounded-lg px-2.5 py-1.5">
                                        <strong class="font-semibold">Alasan ditolak:</strong> {{ $item->komentar_validasi }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-start shrink-0">
                            <a href="{{ route('prasarana.show', $item) }}" class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Detail"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></a>
                            @auth
                                @if(auth()->user()->canEdit($item))
                                    <a href="{{ route('prasarana.edit', $item) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    @php
                                        $itemData = '{ id: ' . $item->id
                                            . ", nama: '" . addslashes($item->nama_fasilitas) . "'"
                                            . ", kategori: '" . addslashes($item->kategori_olahraga_label) . "'"
                                            . ", wilayah: '" . addslashes(($item->desa ?? '-') . ' / ' . ($item->kecamatan ?? '-') . ' / ' . ($item->kabupaten ?? '-')) . "'"
                                            . ", kondisi: '" . $item->average_kondisi . "'"
                                            . ", status: '" . $item->status_validasi . "'";
                                    @endphp
                                    @if($item->status_validasi === 'pending')
                                        <button @click="selected = {{ $itemData }}, action: '{{ route('prasarana.validate', $item) }}' }; verifyOpen = true" class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors" title="Verifikasi"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button>
                                        <button @click="selected = {{ $itemData }}, action: '{{ route('prasarana.reject', $item) }}' }; rejectOpen = true" class="p-2 rounded-lg text-orange-600 hover:bg-orange-50 transition-colors" title="Butuh Perbaikan"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg></button>
                                        <form method="POST" action="{{ route('prasarana.destroy', $item) }}" onsubmit="return confirm('Hapus data prasarana ini? Tindakan ini tidak bisa dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-red-600 transition-colors" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                        </form>
                                    @elseif($item->status_validasi === 'validated')
                                        {{-- Data sudah valid: cukup 3 aksi (Detail, Edit khusus super admin, Tolak) — tanpa hapus, supaya data resmi tidak kehapus tak sengaja. --}}
                                        <button @click="selected = {{ $itemData }}, action: '{{ route('prasarana.reject', $item) }}' }; rejectOpen = true" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Tolak"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                    @elseif($item->status_validasi === 'rejected')
                                        <button @click="selected = {{ $itemData }}, action: '{{ route('prasarana.validate', $item) }}' }; verifyOpen = true" class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors" title="Verifikasi Ulang"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button>
                                        <form method="POST" action="{{ route('prasarana.destroy', $item) }}" onsubmit="return confirm('Hapus data prasarana ini? Tindakan ini tidak bisa dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-red-600 transition-colors" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                        </form>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="p-4 bg-gray-100 rounded-full mb-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg></div>
                        <p class="text-gray-500 text-sm">Tidak ada data prasarana.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($prasarana instanceof \Illuminate\Contracts\Pagination\Paginator && $prasarana->hasPages())
            <div class="mt-6">{{ $prasarana->links() }}</div>
        @endif
    </div>

    <!-- Modal Verifikasi -->
    <div x-show="verifyOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm" @click="verifyOpen = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Verifikasi Prasarana</h3>
            <p class="text-sm text-gray-500 mb-4">Pastikan data berikut sudah benar sebelum memverifikasi.</p>

            <!-- Preview Data -->
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-5 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Nama Fasilitas</span><span class="font-semibold text-gray-900 text-right max-w-[60%]" x-text="selected?.nama"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kategori</span><span class="font-medium text-gray-900" x-text="selected?.kategori"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Wilayah</span><span class="font-medium text-gray-900 text-right max-w-[60%]" x-text="selected?.wilayah"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Rata-rata Kondisi</span><span class="font-medium text-gray-900" x-text="selected?.kondisi + ' / 5'"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status Saat Ini</span><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100" x-text="selected?.status === 'rejected' ? 'Ditolak' : 'Pending'"></span></div>
            </div>

            <form :action="selected?.action" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="space-y-3 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nama Verifikator</label>
                        <input type="text" value="{{ auth()->user()->name ?? '' }}" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-700">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Catatan / Komentar</label>
                        <textarea name="komentar_validasi" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" placeholder="Tulis catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="verifyOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition shadow-sm">Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Butuh Perbaikan / Tolak -->
    <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm" @click="rejectOpen = false">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Tandai Butuh Perbaikan</h3>
            <p class="text-sm text-gray-500 mb-4">Data akan dikembalikan ke pelapor untuk diperbaiki. Jelaskan apa yang perlu diperbaiki.</p>

            <!-- Preview Data -->
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-5 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Nama Fasilitas</span><span class="font-semibold text-gray-900 text-right max-w-[60%]" x-text="selected?.nama"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Kategori</span><span class="font-medium text-gray-900" x-text="selected?.kategori"></span></div>
                <div class="flex justify-between"><span class="text-gray-500">Wilayah</span><span class="font-medium text-gray-900 text-right max-w-[60%]" x-text="selected?.wilayah"></span></div>
            </div>

            <form :action="selected?.action" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="mb-5">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catatan Perbaikan <span class="text-red-500">*</span></label>
                    <textarea name="komentar_validasi" rows="3" required minlength="5" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500 shadow-sm" placeholder="Contoh: foto belum jelas, koordinat lokasi salah, dst."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="rejectOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition shadow-sm">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
