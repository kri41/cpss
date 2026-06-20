<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $club->nama_club }}</h2>
                <p class="text-sm text-gray-500 mt-1">Detail informasi club</p>
            </div>
            <div class="flex items-center space-x-3">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                    <a href="{{ route('clubs.edit', $club) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-xl font-medium hover:bg-yellow-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    @endif
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Info Utama -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Header Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-start gap-6">
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                                @if($club->logo_path)
                                    <img src="{{ Storage::url($club->logo_path) }}" alt="{{ $club->nama_club }}" class="w-full h-full object-cover rounded-2xl">
                                @else
                                    {{ substr($club->nama_club, 0, 1) }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $club->nama_club }}</h1>
                                    @if($club->aktif)
                                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Aktif</span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Nonaktif</span>
                                    @endif
                                </div>
                                @if($club->deskripsi)
                                    <p class="text-gray-600">{{ $club->deskripsi }}</p>
                                @endif
                                <p class="text-sm text-gray-500 mt-2">
                                    <span class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $club->desa ?? '-' }} / {{ $club->kecamatan ?? '-' }} / {{ $club->kabupaten ?? '-' }}
                                    </span>
                                </p>
                                <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                                    @if($club->tanggal_berdiri)
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Berdiri {{ $club->tanggal_berdiri->format('d F Y') }}
                                        </span>
                                    @endif
                                    @if($club->umur)
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $club->umur }} tahun
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontak</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-500">Ketua Club</p>
                                    <p class="font-medium text-gray-900">{{ $club->ketua_club }}</p>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-500">Narahubung</p>
                                    <p class="font-medium text-gray-900">{{ $club->narahubung }}</p>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-500">Telepon</p>
                                    <p class="font-medium text-gray-900">{{ $club->no_telepon }}</p>
                                </div>
                            </div>

                            @if($club->email)
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
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
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Latihan</h3>
                        @if($jadwalByHari->count() > 0)
                            <div class="space-y-4">
                                @foreach($jadwalByHari as $hari => $jadwals)
                                    <div class="flex items-start">
                                        <div class="w-24 flex-shrink-0">
                                            <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 rounded-lg text-sm font-medium">
                                                {{ $hari }}
                                            </span>
                                        </div>
                                        <div class="flex-1 flex flex-wrap gap-2">
                                            @foreach($jadwals as $jadwal)
                                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm">
                                                    {{ $jadwal->waktu_format }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Belum ada jadwal latihan</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Prasarana -->
                    @if($club->prasarana)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prasarana</h3>
                            <div class="p-4 bg-blue-50 rounded-xl">
                                <div class="flex items-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="font-medium text-gray-900">{{ $club->prasarana->nama_fasilitas }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $club->prasarana->kategori_olahraga }}</p>
                                <a href="{{ route('prasarana.show', $club->prasarana) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Detail →</a>
                            </div>
                        </div>
                    @endif

                    <!-- Info Tambahan -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Info</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dibuat</span>
                                <span class="text-gray-900">{{ $club->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Diupdate</span>
                                <span class="text-gray-900">{{ $club->updated_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Oleh</span>
                                <span class="text-gray-900">{{ $club->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <form action="{{ route('clubs.destroy', $club) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus club ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-3 px-4 bg-red-50 text-red-600 rounded-xl font-medium hover:bg-red-100 transition-colors flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Club
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
