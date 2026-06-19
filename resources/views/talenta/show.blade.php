<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Talenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Nama Atlet</h3>
                            <p class="mt-1 text-xl font-bold text-gray-900">{{ $talenta->nama_atlet }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Cabang Olahraga</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $talenta->cabang_olahraga }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Asal Sekolah/Klub</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $talenta->asal_sekolah_atau_klub }}</p>
                        </div>

                        @if($talenta->prestasi_tertinggi)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Prestasi Tertinggi</h3>
                                <div class="mt-1 p-4 bg-yellow-50 rounded-lg">
                                    <p class="text-gray-900 whitespace-pre-line">{{ $talenta->prestasi_tertinggi }}</p>
                                </div>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status Pembinaan</h3>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $talenta->status_pembinaan === 'Aktif PPLP' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $talenta->status_pembinaan === 'Mandiri' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $talenta->status_pembinaan === 'Lulus' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $talenta->status_pembinaan }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Dicatat Oleh</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $talenta->user->name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 space-x-4">
                        <a href="{{ route('talenta.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Kembali') }}
                        </a>
                        <a href="{{ route('talenta.edit', $talenta) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
