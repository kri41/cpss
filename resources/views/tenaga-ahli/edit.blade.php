<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tenaga Ahli') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('tenaga-ahli.update', $tenagaAhli) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Tenaga Ahli -->
                            <div>
                                <x-input-label for="nama_tenaga_ahli" :value="__('Nama Tenaga Ahli')" />
                                <x-text-input id="nama_tenaga_ahli" class="block mt-1 w-full" type="text" name="nama_tenaga_ahli" :value="old('nama_tenaga_ahli', $tenagaAhli->nama_tenaga_ahli)" required autofocus />
                                <x-input-error :messages="$errors->get('nama_tenaga_ahli')" class="mt-2" />
                            </div>

                            <!-- Profesi -->
                            <div>
                                <x-input-label for="profesi" :value="__('Profesi')" />
                                <select id="profesi" name="profesi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Profesi</option>
                                    <option value="Pelatih" {{ old('profesi', $tenagaAhli->profesi) == 'Pelatih' ? 'selected' : '' }}>Pelatih</option>
                                    <option value="Wasit/Juri" {{ old('profesi', $tenagaAhli->profesi) == 'Wasit/Juri' ? 'selected' : '' }}>Wasit/Juri</option>
                                    <option value="Guru PJOK" {{ old('profesi', $tenagaAhli->profesi) == 'Guru PJOK' ? 'selected' : '' }}>Guru PJOK</option>
                                    <option value="Instruktur Senam" {{ old('profesi', $tenagaAhli->profesi) == 'Instruktur Senam' ? 'selected' : '' }}>Instruktur Senam</option>
                                </select>
                                <x-input-error :messages="$errors->get('profesi')" class="mt-2" />
                            </div>

                            <!-- Nomor Sertifikat -->
                            <div>
                                <x-input-label for="nomor_sertifikat" :value="__('Nomor Sertifikat (Opsional)')" />
                                <x-text-input id="nomor_sertifikat" class="block mt-1 w-full" type="text" name="nomor_sertifikat" :value="old('nomor_sertifikat', $tenagaAhli->nomor_sertifikat)" />
                                <x-input-error :messages="$errors->get('nomor_sertifikat')" class="mt-2" />
                            </div>

                            <!-- Tingkat Lisensi -->
                            <div>
                                <x-input-label for="tingkat_lisensi" :value="__('Tingkat Lisensi')" />
                                <select id="tingkat_lisensi" name="tingkat_lisensi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Tingkat Lisensi</option>
                                    <option value="Internasional" {{ old('tingkat_lisensi', $tenagaAhli->tingkat_lisensi) == 'Internasional' ? 'selected' : '' }}>Internasional</option>
                                    <option value="Nasional" {{ old('tingkat_lisensi', $tenagaAhli->tingkat_lisensi) == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                                    <option value="Daerah" {{ old('tingkat_lisensi', $tenagaAhli->tingkat_lisensi) == 'Daerah' ? 'selected' : '' }}>Daerah</option>
                                    <option value="Belum Berlisensi" {{ old('tingkat_lisensi', $tenagaAhli->tingkat_lisensi) == 'Belum Berlisensi' ? 'selected' : '' }}>Belum Berlisensi</option>
                                </select>
                                <x-input-error :messages="$errors->get('tingkat_lisensi')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('tenaga-ahli.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                {{ __('Batal') }}
                            </a>

                            <x-primary-button>
                                {{ __('Perbarui') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
