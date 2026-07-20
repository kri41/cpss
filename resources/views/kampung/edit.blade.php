@extends('layouts.app')

@section('title', 'Edit Kampung Olahraga - Dataraga')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:px-6">

    <div class="mb-5">
        <a href="{{ route('kampung.show', $kampung) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50">
            <h1 class="text-base font-bold text-gray-800">Edit Kampung Olahraga</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ $kampung->nama_kampung }}</p>
        </div>

        <form method="POST" action="{{ route('kampung.update', $kampung) }}" class="p-5 space-y-4">
            @csrf @method('PUT')

            @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kampung / Kelurahan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kampung" value="{{ old('nama_kampung', $kampung->nama_kampung) }}" required
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm @error('nama_kampung') border-red-400 @enderror">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" rows="2" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm resize-none">{{ old('alamat', $kampung->alamat) }}</textarea>
            </div>

            <x-wilayah-dropdown
                :selectedProvinsi="old('provinsi', $kampung->provinsi)"
                :selectedKabupaten="old('kabupaten', $kampung->kabupaten)"
                :selectedKecamatan="old('kecamatan', $kampung->kecamatan)"
                :selectedDesa="old('desa', $kampung->desa)"
            />

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RT <span class="text-xs text-gray-400">(opsional)</span></label>
                    <input type="text" name="rt" value="{{ old('rt', $kampung->rt) }}" placeholder="Cth: 003" maxlength="5"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RW <span class="text-xs text-gray-400">(opsional)</span></label>
                    <input type="text" name="rw" value="{{ old('rw', $kampung->rw) }}" placeholder="Cth: 005" maxlength="5"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                    <input type="number" name="latitude" value="{{ old('latitude', $kampung->latitude) }}" step="0.00000001"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                    <input type="number" name="longitude" value="{{ old('longitude', $kampung->longitude) }}" step="0.00000001"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm shadow-sm">
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="flex-1 flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition text-sm">
                    Simpan Perubahan
                </button>
                <a href="{{ route('kampung.show', $kampung) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
