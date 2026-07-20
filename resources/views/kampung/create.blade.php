@extends('layouts.app')

@section('title', 'Daftarkan Kampung Olahraga - Dataraga')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:px-6">

    <div class="mb-5">
        <a href="{{ route('kampung.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50">
            <h1 class="text-base font-bold text-gray-800">Daftarkan Kampung Olahraga</h1>
            <p class="text-xs text-gray-500 mt-0.5">Setelah didaftarkan, admin akan memverifikasi dan mengaktifkan QR Code.</p>
        </div>

        <div class="mx-5 mt-4 flex gap-2.5 p-3 bg-blue-50/70 border border-blue-100 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-blue-800">Kampung Olahraga boleh didaftarkan dalam skala kecil (per RT/RW). Beberapa relawan di desa yang sama tetap bisa mendaftarkan kampungnya masing-masing dan mendapatkan penghargaan/poin sendiri-sendiri.</p>
        </div>

        <form method="POST" action="{{ route('kampung.store') }}" class="p-5 space-y-4">
            @csrf

            @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kampung / Kelurahan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kampung" value="{{ old('nama_kampung') }}" placeholder="Cth: Kampung Olahraga Sukamaju" required
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm @error('nama_kampung') border-red-400 @enderror">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" rows="2" placeholder="Alamat lengkap kampung olahraga..." class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm resize-none">{{ old('alamat') }}</textarea>
            </div>

            {{-- Wilayah --}}
            <x-wilayah-dropdown
                :selectedProvinsi="old('provinsi')"
                :selectedKabupaten="old('kabupaten')"
                :selectedKecamatan="old('kecamatan')"
                :selectedDesa="old('desa')"
            />

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RT <span class="text-xs text-gray-400">(opsional)</span></label>
                    <input type="text" name="rt" value="{{ old('rt') }}" placeholder="Cth: 003" maxlength="5"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RW <span class="text-xs text-gray-400">(opsional)</span></label>
                    <input type="text" name="rw" value="{{ old('rw') }}" placeholder="Cth: 005" maxlength="5"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                </div>
            </div>

            {{-- Koordinat (opsional) --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitude <span class="text-xs text-gray-400">(opsional)</span></label>
                    <input type="number" name="latitude" value="{{ old('latitude') }}" step="0.00000001" placeholder="-7.12345678"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitude <span class="text-xs text-gray-400">(opsional)</span></label>
                    <input type="number" name="longitude" value="{{ old('longitude') }}" step="0.00000001" placeholder="112.98765432"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Daftarkan Kampung Olahraga
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
