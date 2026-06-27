<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Partisipasi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('partisipasi.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Lokasi Observasi -->
                            <div class="md:col-span-2">
                                <x-input-label for="lokasi_observasi" :value="__('Lokasi Observasi')" />
                                <x-text-input id="lokasi_observasi" class="block mt-1 w-full" type="text" name="lokasi_observasi" :value="old('lokasi_observasi')" required placeholder="Contoh: Lapangan Desa Suka Maju, Stadion Mini Kecamatan" />
                                <x-input-error :messages="$errors->get('lokasi_observasi')" class="mt-2" />
                            </div>

                            <!-- Wilayah -->
                            <div class="md:col-span-2">
                                <x-wilayah-dropdown />
                            </div>

                            <!-- Tanggal Observasi -->
                            <div>
                                <x-input-label for="tanggal_observasi" :value="__('Tanggal Observasi')" />
                                <x-text-input id="tanggal_observasi" class="block mt-1 w-full" type="date" name="tanggal_observasi" :value="old('tanggal_observasi', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tanggal_observasi')" class="mt-2" />
                            </div>

                            <!-- Estimasi Jumlah Orang -->
                            <div>
                                <x-input-label for="estimasi_jumlah_orang" :value="__('Estimasi Jumlah Orang')" />
                                <x-text-input id="estimasi_jumlah_orang" class="block mt-1 w-full" type="number" name="estimasi_jumlah_orang" :value="old('estimasi_jumlah_orang')" required min="1" placeholder="Jumlah orang yang terlihat berolahraga" />
                                <x-input-error :messages="$errors->get('estimasi_jumlah_orang')" class="mt-2" />
                            </div>

                            <!-- Mayoritas Usia -->
                            <div class="md:col-span-2">
                                <x-input-label for="mayoritas_usia" :value="__('Mayoritas Kelompok Usia')" />
                                <select id="mayoritas_usia" name="mayoritas_usia" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Kelompok Usia</option>
                                    <option value="Anak/Pelajar" {{ old('mayoritas_usia') == 'Anak/Pelajar' ? 'selected' : '' }}>Anak/Pelajar</option>
                                    <option value="Dewasa" {{ old('mayoritas_usia') == 'Dewasa' ? 'selected' : '' }}>Dewasa</option>
                                    <option value="Lansia" {{ old('mayoritas_usia') == 'Lansia' ? 'selected' : '' }}>Lansia</option>
                                </select>
                                <x-input-error :messages="$errors->get('mayoritas_usia')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('partisipasi.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                {{ __('Batal') }}
                            </a>

                            <x-primary-button>
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
