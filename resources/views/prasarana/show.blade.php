<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Prasarana') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($prasarana->foto_path)
                            <div class="md:col-span-2">
                                <img src="{{ Storage::url($prasarana->foto_path) }}" alt="Foto {{ $prasarana->nama_fasilitas }}" class="w-full h-64 object-cover rounded-lg">
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nama Fasilitas</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $prasarana->nama_fasilitas }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Club/Komunitas</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $prasarana->club_komunitas ?: '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kategori Olahraga</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $prasarana->kategori_olahraga }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Wilayah</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $prasarana->desa ?? '-' }} / {{ $prasarana->kecamatan ?? '-' }} / {{ $prasarana->kabupaten ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Kondisi Lantai</h3>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $prasarana->kondisi_lantai === 'Baik' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $prasarana->kondisi_lantai === 'Sedang' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $prasarana->kondisi_lantai === 'Rusak Berat' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $prasarana->kondisi_lantai }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Ramah Disabilitas</h3>
                            <p class="mt-1 text-lg text-gray-900">
                                @if($prasarana->akses_disabilitas)
                                    <span class="text-green-600 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Ya
                                    </span>
                                @else
                                    <span class="text-gray-400">Tidak</span>
                                @endif
                            </p>
                        </div>

                        @if($prasarana->latitude && $prasarana->longitude)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Koordinat</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ $prasarana->latitude }}, {{ $prasarana->longitude }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Lokasi di Peta</h3>
                                <div class="mt-2 h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <a href="https://www.google.com/maps?q={{ $prasarana->latitude }},{{ $prasarana->longitude }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Lihat di Google Maps
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Dilaporkan Oleh</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $prasarana->user->name ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tanggal Laporan</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $prasarana->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 space-x-4">
                        <a href="{{ route('prasarana.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                        @auth
                            @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                            <a href="{{ route('prasarana.edit', $prasarana) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Edit') }}
                            </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
