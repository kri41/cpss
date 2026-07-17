<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Data Partisipasi Masyarakat</h2>
                <p class="text-sm text-slate-500 mt-1">Catatan partisipasi dan kehadiran</p>
            </div>
            <div class="flex items-center gap-2">
                @if(auth()->user()?->isAdmin())
                <a href="{{ route('export.partisipasi', request()->query()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-emerald-700 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>
                @endif
                <a href="{{ route('partisipasi.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Catat Partisipasi
                </a>
            </div>
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

            @php
                $items = $partisipasi instanceof \Illuminate\Contracts\Pagination\Paginator ? $partisipasi->getCollection() : $partisipasi;
                $totalPartisipasi = $partisipasi instanceof \Illuminate\Contracts\Pagination\Paginator ? $partisipasi->total() : $partisipasi->count();
                $totalKehadiran = $items->flatMap->kehadiran->count();
                $validatedCount = $items->where('status_validasi', 'validated')->count();
                $avgRpe = $items->flatMap->kehadiran->avg('rpe');
            @endphp

            <!-- Sticky Stats Cards -->
            <div class="sticky top-0 z-20 bg-slate-50/95 backdrop-blur py-4 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-sky-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500">Total Partisipasi</p>
                                <p class="text-xl font-bold text-slate-800">{{ $totalPartisipasi }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-blue-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500">Kehadiran Tercatat</p>
                                <p class="text-xl font-bold text-slate-800">{{ $totalKehadiran }}</p>
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
                                <p class="text-xl font-bold text-slate-800">{{ $validatedCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-cyan-100 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500">Rata-rata RPE</p>
                                <p class="text-xl font-bold text-slate-800">{{ $avgRpe ? number_format($avgRpe, 1) : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card List -->
            <div class="grid grid-cols-1 gap-4">
                @forelse($partisipasi as $item)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                <div class="shrink-0 flex flex-col items-center justify-center w-14 h-14 rounded-xl bg-sky-50 border border-sky-100 text-sky-700">
                                    <span class="text-lg font-bold leading-none">{{ $item->tanggal_observasi->format('d') }}</span>
                                    <span class="text-[10px] font-medium uppercase tracking-wide">{{ $item->tanggal_observasi->format('M') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-base font-semibold text-slate-800 truncate">{{ $item->lokasi_observasi }}</h3>
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
                                        <span class="text-base font-semibold text-sky-600">{{ number_format($item->estimasi_jumlah_orang) }} orang</span>
                                        <span class="inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $item->desa }}, {{ $item->kecamatan }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Kehadiran: {{ $item->kehadiran->count() }} tercatat
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-start shrink-0">
                                <a href="{{ route('partisipasi.show', $item) }}" class="p-2 rounded-lg text-sky-600 hover:bg-sky-50 transition-colors" title="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @auth
                                    @if(auth()->user()->canEdit($item))
                                        <a href="{{ route('partisipasi.edit', $item) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('partisipasi.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
                                        <form action="{{ route('partisipasi.validate', $item) }}" method="POST" class="inline" onsubmit="return confirm('Validasi data partisipasi ini? Data yang sudah divalidasi tidak dapat diedit.');">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <p class="text-slate-500 text-sm">Tidak ada data partisipasi.</p>
                            <a href="{{ route('partisipasi.create') }}" class="mt-2 text-sky-600 hover:text-sky-800 text-sm font-medium">Catat data pertama</a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($partisipasi->hasPages())
                <div class="mt-6">
                    {{ $partisipasi->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
