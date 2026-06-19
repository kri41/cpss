<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Prasarana') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('prasarana.update', $prasarana) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Fasilitas -->
                            <div>
                                <x-input-label for="nama_fasilitas" :value="__('Nama Fasilitas')" />
                                <x-text-input id="nama_fasilitas" class="block mt-1 w-full" type="text" name="nama_fasilitas" :value="old('nama_fasilitas', $prasarana->nama_fasilitas)" required autofocus />
                                <x-input-error :messages="$errors->get('nama_fasilitas')" class="mt-2" />
                            </div>

                            <!-- Club/Komunitas -->
                            <div>
                                <x-input-label for="club_komunitas" :value="__('Club/Komunitas')" />
                                <x-text-input id="club_komunitas" class="block mt-1 w-full" type="text" name="club_komunitas" :value="old('club_komunitas', $prasarana->club_komunitas)" placeholder="Contoh: Club Sepakbola Merdeka" />
                                <x-input-error :messages="$errors->get('club_komunitas')" class="mt-2" />
                                <p class="text-sm text-gray-500 mt-1">Opsional - Isi jika fasilitas ini digunakan oleh club/komunitas tertentu</p>
                            </div>

                            <!-- Kategori Olahraga -->
                            <div>
                                <x-input-label for="kategori_olahraga" :value="__('Kategori Olahraga')" />
                                <x-text-input id="kategori_olahraga" class="block mt-1 w-full" type="text" name="kategori_olahraga" :value="old('kategori_olahraga', $prasarana->kategori_olahraga)" required />
                                <x-input-error :messages="$errors->get('kategori_olahraga')" class="mt-2" />
                            </div>

                            <!-- Latitude -->
                            <div>
                                <x-input-label for="latitude" :value="__('Latitude')" />
                                <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="any" name="latitude" :value="old('latitude', $prasarana->latitude)" />
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </div>

                            <!-- Longitude -->
                            <div>
                                <x-input-label for="longitude" :value="__('Longitude')" />
                                <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="any" name="longitude" :value="old('longitude', $prasarana->longitude)" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </div>

                            <!-- Kondisi Lantai -->
                            <div>
                                <x-input-label for="kondisi_lantai" :value="__('Kondisi Lantai')" />
                                <select id="kondisi_lantai" name="kondisi_lantai" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Pilih Kondisi</option>
                                    <option value="Baik" {{ old('kondisi_lantai', $prasarana->kondisi_lantai) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Sedang" {{ old('kondisi_lantai', $prasarana->kondisi_lantai) == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                    <option value="Rusak Berat" {{ old('kondisi_lantai', $prasarana->kondisi_lantai) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                                <x-input-error :messages="$errors->get('kondisi_lantai')" class="mt-2" />
                            </div>

                            <!-- Akses Disabilitas -->
                            <div class="flex items-center mt-8">
                                <input id="akses_disabilitas" type="checkbox" name="akses_disabilitas" value="1" {{ old('akses_disabilitas', $prasarana->akses_disabilitas) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="akses_disabilitas" class="ml-2 block text-sm text-gray-900">
                                    {{ __('Ramah Disabilitas') }}
                                </label>
                            </div>
                        </div>

                        <!-- Foto -->
                        <div class="mt-6">
                            <x-input-label for="foto" :value="__('Foto Fasilitas')" />
                            @if($prasarana->foto_path)
                                <div class="mt-2 mb-2">
                                    <img src="{{ Storage::url($prasarana->foto_path) }}" alt="Foto {{ $prasarana->nama_fasilitas }}" class="h-32 object-cover rounded">
                                </div>
                            @endif
                            <input id="foto" type="file" name="foto" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah foto</p>
                            <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('prasarana.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
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
