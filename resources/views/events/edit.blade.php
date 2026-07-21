<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('events.update', $event) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Event -->
                            <div class="md:col-span-2">
                                <x-input-label for="nama_event" :value="__('Nama Event')" />
                                <x-text-input id="nama_event" class="block mt-1 w-full" type="text" name="nama_event" :value="old('nama_event', $event->nama_event)" required autofocus />
                                <x-input-error :messages="$errors->get('nama_event')" class="mt-2" />
                            </div>

                            <!-- Tingkat -->
                            <div>
                                <x-input-label for="tingkat" :value="__('Tingkat Event')" />
                                <select id="tingkat" name="tingkat" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="Desa/Kelurahan" {{ old('tingkat', $event->tingkat) == 'Desa/Kelurahan' ? 'selected' : '' }}>Desa/Kelurahan</option>
                                    <option value="Kecamatan" {{ old('tingkat', $event->tingkat) == 'Kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                                    <option value="Kabupaten/Kota" {{ old('tingkat', $event->tingkat) == 'Kabupaten/Kota' ? 'selected' : '' }}>Kabupaten/Kota</option>
                                </select>
                                <x-input-error :messages="$errors->get('tingkat')" class="mt-2" />
                            </div>

                            <!-- Tanggal Mulai -->
                            <div>
                                <x-input-label for="tanggal_mulai" :value="__('Tanggal Mulai')" />
                                <x-text-input id="tanggal_mulai" class="block mt-1 w-full" type="date" name="tanggal_mulai" :value="old('tanggal_mulai', $event->tanggal_mulai->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                            </div>

                            <!-- Tanggal Selesai -->
                            <div>
                                <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai (Opsional)')" />
                                <x-text-input id="tanggal_selesai" class="block mt-1 w-full" type="date" name="tanggal_selesai" :value="old('tanggal_selesai', $event->tanggal_selesai?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                            </div>

                            <!-- Deskripsi Kegiatan -->
                            <div class="md:col-span-2">
                                <x-input-label for="deskripsi_kegiatan" :value="__('Deskripsi Kegiatan')" />
                                <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('deskripsi_kegiatan', $event->deskripsi_kegiatan) }}</textarea>
                                <x-input-error :messages="$errors->get('deskripsi_kegiatan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-wilayah-dropdown :selectedProvinsi="$event->provinsi" :selectedKabupaten="$event->kabupaten" :selectedKecamatan="$event->kecamatan" :selectedDesa="$event->desa" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('events.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
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
