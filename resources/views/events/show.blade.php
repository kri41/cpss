@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@section('title', $event->nama_event . ' - Dataraga')

@section('content')
<div class="min-h-[calc(100vh-3.5rem)] bg-gray-50">
    <!-- Hero Header -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('events.index') }}" class="hover:text-blue-600 transition">Event</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-700 font-medium">Detail</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            {{ $event->tingkat === 'Desa/Kelurahan' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $event->tingkat === 'Kecamatan' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $event->tingkat === 'Kabupaten/Kota' ? 'bg-purple-100 text-purple-700' : '' }}">
                            {{ $event->tingkat }}
                        </span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            {{ $event->status === 'Akan Datang' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $event->status === 'Berlangsung' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $event->status === 'Selesai' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ $event->status }}
                        </span>
                        @if($event->status_validasi === 'validated')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Tervalidasi</span>
                        @elseif($event->status_validasi === 'rejected')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">Butuh Perbaikan</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Menunggu Validasi</span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">{{ $event->nama_event }}</h1>
                    @if($event->status_validasi === 'rejected' && $event->komentar_validasi)
                    <div class="mt-3 flex gap-2 p-3 bg-orange-50 border border-orange-100 rounded-lg text-sm text-orange-800 max-w-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span><strong>Catatan Admin:</strong> {{ $event->komentar_validasi }}</span>
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ auth()->check() ? route('dashboard.events') : route('events.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                        Kembali
                    </a>
                    @auth
                        @if(auth()->user()->canEdit($event))
                            <a href="{{ route('events.edit', $event) }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600 transition shadow-sm">
                                Edit
                            </a>
                        @else
                            <a href="{{ route('events.edit', $event) }}" class="px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-sm font-medium hover:bg-amber-100 transition">
                                Ajukan Perubahan
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                @if($event->deskripsi_kegiatan)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Deskripsi Kegiatan</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $event->deskripsi_kegiatan }}</p>
                </div>
                @endif

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Informasi Event</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal Mulai</p>
                                <p class="font-semibold text-gray-900">{{ $event->tanggal_mulai->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal Selesai</p>
                                <p class="font-semibold text-gray-900">{{ $event->tanggal_selesai?->translatedFormat('d F Y') ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 sm:col-span-2">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-2">Lokasi Kegiatan</p>
                                @php
                                    $levels = [
                                        ['label' => 'Provinsi',   'kode' => $event->provinsi],
                                        ['label' => 'Kabupaten',  'kode' => $event->kabupaten],
                                        ['label' => 'Kecamatan',  'kode' => $event->kecamatan],
                                        ['label' => 'Desa/Kel.',  'kode' => $event->desa],
                                    ];
                                    $hasLokasi = collect($levels)->filter(fn($l) => $l['kode'])->isNotEmpty();
                                @endphp
                                @if($hasLokasi)
                                <div class="flex flex-wrap items-center gap-1">
                                    @foreach($levels as $i => $level)
                                        @if($level['kode'])
                                            @php $nama = $wilayahNama[$level['kode']] ?? $level['kode']; @endphp
                                            @if($i > 0)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            @endif
                                            <span class="inline-flex flex-col">
                                                <span class="text-[9px] text-gray-400 leading-none">{{ $level['label'] }}</span>
                                                <span class="font-semibold text-gray-900 text-sm leading-tight">{{ $nama }}</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                                @else
                                    <p class="font-semibold text-gray-400">Lokasi belum diisi</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Dibuat Oleh</p>
                                <p class="font-semibold text-gray-900">{{ $event->user->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Detail</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Waktu Pembuatan</span>
                            <span class="text-gray-900 font-medium">{{ $event->created_at->translatedFormat('d M Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Terakhir Diupdate</span>
                            <span class="text-gray-900 font-medium">{{ $event->updated_at->translatedFormat('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
