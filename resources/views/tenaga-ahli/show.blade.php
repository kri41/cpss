<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tenaga Ahli') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nama Tenaga Ahli</h3>
                            <p class="mt-1 text-xl font-bold text-gray-900">{{ $tenagaAhli->nama_tenaga_ahli }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Profesi</h3>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $tenagaAhli->profesi }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nomor Sertifikat</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $tenagaAhli->nomor_sertifikat ?? '-' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tingkat Lisensi</h3>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $tenagaAhli->tingkat_lisensi === 'Internasional' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $tenagaAhli->tingkat_lisensi === 'Nasional' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $tenagaAhli->tingkat_lisensi === 'Daerah' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $tenagaAhli->tingkat_lisensi === 'Belum Berlisensi' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $tenagaAhli->tingkat_lisensi }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status Sertifikasi</h3>
                            <p class="mt-1 text-lg">
                                @if($tenagaAhli->bersertifikat)
                                    <span class="text-green-600 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Bersertifikat
                                    </span>
                                @else
                                    <span class="text-gray-400">Belum Bersertifikat</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Dicatat Oleh</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $tenagaAhli->user->name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 space-x-4">
                        <a href="{{ route('tenaga-ahli.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                        <a href="{{ route('tenaga-ahli.edit', $tenagaAhli) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
