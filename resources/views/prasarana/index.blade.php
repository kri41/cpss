<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Manajemen Prasarana</h2>
                <p class="text-sm text-slate-500 mt-1">Kelola data prasarana olahraga</p>
            </div>
            @auth
                @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                <a href="{{ route('prasarana.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Prasarana
                </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-xl">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-2 text-sm text-green-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Filter Form -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
                <form method="GET" action="{{ route('prasarana.index') }}" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Cari Fasilitas</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama fasilitas..." class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div class="w-40">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Kabupaten</label>
                        <select name="kabupaten" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach($kabupatenList as $k)
                                <option value="{{ $k }}" {{ request('kabupaten') == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-40">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Kecamatan</label>
                        <select name="kecamatan" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach($kecamatanList as $k)
                                <option value="{{ $k }}" {{ request('kecamatan') == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-44">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Kategori Olahraga</label>
                        <select name="kategori" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700 transition">Filter</button>
                        <a href="{{ route('prasarana.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition">Reset</a>
                    </div>
                </form>
            </div>

            @php
                $items = $prasarana instanceof \Illuminate\Contracts\Pagination\Paginator ? $prasarana->getCollection() : $prasarana;
                $totalPrasarana = $prasarana instanceof \Illuminate\Contracts\Pagination\Paginator ? $prasarana->total() : $prasarana->count();
                $avgKondisi = $items->count() > 0 ? round($items->avg('average_kondisi'), 1) : 0;
                $wilayahTerbanyak = $items->countBy('kabupaten')->sortDesc()->keys()->first() ?? '-';
                $tervalidasi = $items->where('status_validasi', 'validated')->count();
            @endphp

            <!-- Sticky Stats Cards -->
            <div class="sticky top-0 z-20 bg-slate-50/95 backdrop-blur py-4 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-sky-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500">Total Prasarana</p>
                                <p class="text-xl font-bold text-slate-800">{{ $totalPrasarana }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-amber-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500">Rata-rata Kondisi</p>
                                <p class="text-xl font-bold text-slate-800">{{ $avgKondisi }}<span class="text-sm font-normal text-slate-400">/5</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-blue-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500">Wilayah Terbanyak</p>
                                <p class="text-xl font-bold text-slate-800 truncate">{{ $wilayahTerbanyak }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-emerald-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500">Tervalidasi</p>
                                <p class="text-xl font-bold text-slate-800">{{ $tervalidasi }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card List -->
            <div class="grid grid-cols-1 gap-4">
                @forelse($prasarana as $item)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                @if($item->foto_path)
                                    <img src="{{ Storage::url($item->foto_path) }}" alt="" class="h-14 w-14 rounded-xl object-cover shrink-0 bg-slate-100">
                                @else
                                    <div class="h-14 w-14 rounded-xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-base font-semibold text-slate-800 truncate">{{ $item->nama_fasilitas }}</h3>
                                        @if($item->status_validasi === 'validated')
                                            <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-slate-300 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-slate-500">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-sky-50 text-sky-700">
                                            {{ $item->kategori_olahraga }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $item->desa ?? '-' }}, {{ $item->kecamatan ?? '-' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs">
                                            Kondisi: <strong class="text-slate-700">{{ $item->average_kondisi }}/5</strong>
                                            <span class="inline-block w-12 h-1.5 bg-slate-200 rounded-full overflow-hidden align-middle ml-1">
                                                @php
                                                    $kondisiPct = min(100, max(0, ($item->average_kondisi / 5) * 100));
                                                    $kondisiColor = $item->average_kondisi >= 4 ? 'bg-green-500' : ($item->average_kondisi >= 3 ? 'bg-sky-500' : ($item->average_kondisi >= 2 ? 'bg-amber-400' : 'bg-red-500'));
                                                @endphp
                                                <span class="block h-full {{ $kondisiColor }}" style="width: {{ $kondisiPct }}%"></span>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-start shrink-0">
                                <a href="{{ route('prasarana.show', $item) }}" class="p-2 rounded-lg text-sky-600 hover:bg-sky-50 transition-colors" title="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @auth
                                    @if(auth()->user()->canEdit($item))
                                        <a href="{{ route('prasarana.edit', $item) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('prasarana.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    @if(auth()->user()->canValidate($item) && $item->status_validasi !== 'validated')
                                        <form action="{{ route('prasarana.validate', $item) }}" method="POST" class="inline" onsubmit="return confirm('Validasi data prasarana ini? Data yang sudah divalidasi tidak dapat diedit.');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors" title="Validasi">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-slate-100 rounded-full mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <p class="text-slate-500 text-sm">Tidak ada data prasarana.</p>
                            @auth
                                @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                                <a href="{{ route('prasarana.create') }}" class="mt-2 text-sky-600 hover:text-sky-800 text-sm font-medium">Tambah data pertama</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforelse
            </div>

            @if($prasarana instanceof \Illuminate\Contracts\Pagination\Paginator && $prasarana->hasPages())
                <div class="mt-6">
                    {{ $prasarana->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
