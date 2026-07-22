@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@section('title', $club->nama_club . ' - Dataraga')

@section('content')
<div class="min-h-[calc(100vh-3.5rem)] bg-gray-50">
    <!-- Hero Header -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('clubs.index') }}" class="hover:text-blue-600 transition">Klub</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-700 font-medium">Detail</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                        @if($club->aktif)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Nonaktif</span>
                        @endif
                        @if($club->status_validasi === 'validated')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Tervalidasi</span>
                        @elseif($club->status_validasi === 'rejected')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">Butuh Perbaikan</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Menunggu Validasi</span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">{{ $club->nama_club }}</h1>
                    @if($club->deskripsi)
                        <p class="mt-2 text-gray-600 max-w-2xl">{{ $club->deskripsi }}</p>
                    @endif
                    @if($club->status_validasi === 'rejected' && $club->komentar_validasi)
                    <div class="mt-3 flex gap-2 p-3 bg-orange-50 border border-orange-100 rounded-lg text-sm text-orange-800 max-w-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span><strong>Catatan Admin:</strong> {{ $club->komentar_validasi }}</span>
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ auth()->check() ? route('dashboard.clubs') : route('clubs.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                        Kembali
                    </a>
                    @auth
                        @if(auth()->user()->canEdit($club))
                            <a href="{{ route('clubs.edit', $club) }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600 transition shadow-sm">
                                Edit
                            </a>
                        @else
                            <a href="{{ route('clubs.edit', $club) }}" class="px-4 py-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-sm font-medium hover:bg-amber-100 transition">
                                Ajukan Perubahan
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Utama -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-start gap-6">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shrink-0 shadow-sm">
                            @if($club->logo_path)
                                <img src="{{ Storage::url($club->logo_path) }}" alt="{{ $club->nama_club }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                {{ substr($club->nama_club, 0, 1) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Wilayah</p>
                                        <p class="font-semibold text-gray-900">{{ $club->desa ?? '-' }} / {{ $club->kecamatan ?? '-' }} / {{ $club->kabupaten ?? '-' }}</p>
                                    </div>
                                </div>
                                @if($club->tanggal_berdiri)
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tanggal Berdiri</p>
                                        <p class="font-semibold text-gray-900">{{ $club->tanggal_berdiri->translatedFormat('d F Y') }}</p>
                                    </div>
                                </div>
                                @endif
                                @if($club->umur)
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Usia Club</p>
                                        <p class="font-semibold text-gray-900">{{ $club->umur }} tahun</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kontak -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Informasi Kontak</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Ketua Club</p>
                                <p class="font-medium text-gray-900">{{ $club->ketua_club }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Narahubung</p>
                                <p class="font-medium text-gray-900">{{ $club->narahubung }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Telepon</p>
                                <p class="font-medium text-gray-900">{{ $club->no_telepon }}</p>
                            </div>
                        </div>
                        @if($club->email)
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-900">{{ $club->email }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if($club->alamat)
                    <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm text-gray-500 mb-1">Alamat</p>
                        <p class="font-medium text-gray-900">{{ $club->alamat }}</p>
                    </div>
                    @endif
                </div>

                <!-- Jadwal Latihan -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Jadwal Latihan</h2>
                    @if($jadwalByHari->count() > 0)
                        <div class="space-y-3">
                            @foreach($jadwalByHari as $hari => $jadwals)
                                <div class="flex items-start">
                                    <div class="w-24 flex-shrink-0">
                                        <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium">
                                            {{ $hari }}
                                        </span>
                                    </div>
                                    <div class="flex-1 flex flex-wrap gap-2">
                                        @foreach($jadwals as $jadwal)
                                            <span class="inline-flex items-center px-3 py-1 bg-gray-50 text-gray-700 rounded-lg text-sm border border-gray-100">
                                                {{ $jadwal->waktu_format }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-sm">Belum ada jadwal latihan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Prasarana -->
                @if($club->prasarana)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Prasarana</h3>
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="flex items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <span class="font-medium text-gray-900">{{ $club->prasarana->nama_fasilitas }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ $club->prasarana->kategori_olahraga_label }}</p>
                        <a href="{{ route('prasarana.show', $club->prasarana) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Lihat Detail <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Info Tambahan -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Info</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="text-gray-900 font-medium">{{ $club->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Diupdate</span>
                            <span class="text-gray-900 font-medium">{{ $club->updated_at->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Oleh</span>
                            <span class="text-gray-900 font-medium">{{ $club->user->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
