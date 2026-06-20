<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Nama Event</h3>
                            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $event->nama_event }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tingkat</h3>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $event->tingkat === 'Desa/Kelurahan' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $event->tingkat === 'Kecamatan' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $event->tingkat === 'Kabupaten/Kota' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ $event->tingkat }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $event->status === 'Akan Datang' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $event->status === 'Berlangsung' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $event->status === 'Selesai' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $event->status }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Wilayah</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $event->desa ?? '-' }} / {{ $event->kecamatan ?? '-' }} / {{ $event->kabupaten ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tanggal Mulai</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $event->tanggal_mulai->format('d F Y') }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tanggal Selesai</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $event->tanggal_selesai?->format('d F Y') ?? '-' }}</p>
                        </div>

                        @if($event->deskripsi_kegiatan)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Deskripsi Kegiatan</h3>
                                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ $event->deskripsi_kegiatan }}</p>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Dibuat Oleh</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $event->user->name ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Waktu Pembuatan</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $event->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 space-x-4">
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                        @auth
                            @if(auth()->user()->isAdmin() || auth()->user()->isRelawan())
                            <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
