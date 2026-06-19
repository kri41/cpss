<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Talenta Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('talenta.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Atlet -->
                            <div>
                                <x-input-label for="nama_atlet" :value="__('Nama Atlet')" />
                                <x-text-input id="nama_atlet" class="block mt-1 w-full" type="text" name="nama_atlet" :value="old('nama_atlet')" required autofocus />
                                <x-input-error :messages="$errors->get('nama_atlet')" class="mt-2" />
                            </div>

                            <!-- Cabang Olahraga -->
                            <div>
                                <x-input-label for="cabang_olahraga" :value="__('Cabang Olahraga')" />
                                <x-text-input id="cabang_olahraga" class="block mt-1 w-full" type="text" name="cabang_olahraga" :value="old('cabang_olahraga')" required placeholder="Contoh: Atletik, Renang, Badminton" />
                                <x-input-error :messages="$errors->get('cabang_olahraga')" class="mt-2" />
                            </div>

                            <!-- Asal Sekolah/Klub -->
                            <div class="md:col-span-2">
                                <x-input-label for="asal_sekolah_atau_klub" :value="__('Asal Sekolah/Klub')" />
                                <x-text-input id="asal_sekolah_atau_klub" class="block mt-1 w-full" type="text" name="asal_sekolah_atau_klub" :value="old('asal_sekolah_atau_klub')" required />
                                <x-input-error :messages="$errors->get('asal_sekolah_atau_klub')" class="mt-2" />
                            </div>

                            <!-- Prestasi Tertinggi -->
                            <div class="md:col-span-2">
                                <x-input-label for="prestasi_tertinggi" :value="__('Prestasi Tertinggi (Opsional)')" />
                                <textarea id="prestasi_tertinggi" name="prestasi_tertinggi" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Contoh: Juara 1 Kejuaraan Nasional 2024">{{ old('prestasi_tertinggi') }}</textarea>
                                <x-input-error :messages="$errors->get('prestasi_tertinggi')" class="mt-2" />
                            </div>

                            <!-- Status Pembinaan -->
                            <div class="md:col-span-2">
                                <x-input-label for="status_pembinaan" :value="__('Status Pembinaan')" />
                                <select id="status_pembinaan" name="status_pembinaan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Aktif PPLP" {{ old('status_pembinaan') == 'Aktif PPLP' ? 'selected' : '' }}>Aktif PPLP</option>
                                    <option value="Mandiri" {{ old('status_pembinaan') == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                    <option value="Lulus" {{ old('status_pembinaan') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                </select>
                                <x-input-error :messages="$errors->get('status_pembinaan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('talenta.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
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
