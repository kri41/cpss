@extends('layouts.app')

@section('title', 'Komponen Syarat Kampung Olahraga - Dataraga')

@section('content')
<div x-data="{ editId: null, editData: {} }" class="max-w-4xl mx-auto px-4 py-4 sm:px-6">

    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('kampung.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-base font-bold text-gray-800">Kelola Komponen Syarat</h1>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid lg:grid-cols-5 gap-5">

        {{-- Form Tambah --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-4">Tambah Komponen</h2>
                <form method="POST" action="{{ route('komponen-syarat.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Komponen <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Cth: Partisipasi Awal" required
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500 @error('nama') border-red-400 @enderror">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" placeholder="Penjelasan singkat..." class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500 resize-none">{{ old('deskripsi') }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Target Check-in <span class="text-red-500">*</span></label>
                            <input type="number" name="target_checkin" value="{{ old('target_checkin', 10) }}" min="1" required
                                   class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Poin <span class="text-red-500">*</span></label>
                            <input type="number" name="poin" value="{{ old('poin', 5) }}" min="0" required
                                   class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Urutan</label>
                        <input type="number" name="urutan" value="{{ old('urutan', ($komponen->count() + 1)) }}" min="0"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition">Tambahkan</button>
                </form>
            </div>

            <div class="mt-4 bg-blue-50 rounded-2xl border border-blue-100 p-4 text-xs text-blue-700 space-y-1.5">
                <p class="font-semibold text-blue-800">Cara Kerja Komponen Syarat</p>
                <p>Setiap kampung olahraga yang memiliki jumlah check-in &ge; <strong>Target Check-in</strong> akan mendapatkan poin komponen tersebut.</p>
                <p>Poin total kampung = jumlah poin semua komponen yang terpenuhi.</p>
                <p>Kampung dengan poin tertinggi dapat diajukan ke Kemenpora.</p>
            </div>
        </div>

        {{-- Tabel Komponen --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-bold text-gray-800">Daftar Komponen ({{ $komponen->count() }})</h2>
                </div>

                @if($komponen->isEmpty())
                <div class="text-center py-8 text-sm text-gray-400">Belum ada komponen syarat.</div>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($komponen as $k)
                    <div class="p-4 hover:bg-gray-50 transition" x-data>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-[10px] font-bold text-gray-400 w-5">#{{ $k->urutan }}</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $k->nama }}</span>
                                    @if(!$k->aktif)
                                    <span class="text-[10px] bg-gray-100 text-gray-400 px-1.5 py-0.5 rounded-full font-medium">Nonaktif</span>
                                    @endif
                                </div>
                                @if($k->deskripsi)
                                <p class="text-xs text-gray-500 ml-7">{{ $k->deskripsi }}</p>
                                @endif
                                <div class="flex items-center gap-3 ml-7 mt-1">
                                    <span class="text-xs text-blue-600 font-semibold">Target: {{ number_format($k->target_checkin) }} check-in</span>
                                    <span class="text-xs text-amber-600 font-semibold">{{ $k->poin }} poin</span>
                                </div>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <button @click="$refs.editModal{{ $k->id }}.showModal()" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs">Edit</button>
                                <form method="POST" action="{{ route('komponen-syarat.destroy', $k) }}" onsubmit="return confirm('Hapus komponen ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition text-xs">Hapus</button>
                                </form>
                            </div>
                        </div>

                        {{-- Edit dialog --}}
                        <dialog x-ref="editModal{{ $k->id }}" class="rounded-2xl shadow-2xl max-w-sm w-full p-0 border-0 backdrop:bg-black/50">
                            <form method="POST" action="{{ route('komponen-syarat.update', $k) }}" class="p-5 space-y-3">
                                @csrf @method('PUT')
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-bold text-gray-900 text-sm">Edit Komponen</h3>
                                    <button type="button" @click="$refs.editModal{{ $k->id }}.close()" class="text-gray-400 hover:text-gray-600">✕</button>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama</label>
                                    <input type="text" name="nama" value="{{ $k->nama }}" required class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi</label>
                                    <textarea name="deskripsi" rows="2" class="w-full rounded-lg border-gray-300 text-sm resize-none">{{ $k->deskripsi }}</textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Target Check-in</label>
                                        <input type="number" name="target_checkin" value="{{ $k->target_checkin }}" min="1" required class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Poin</label>
                                        <input type="number" name="poin" value="{{ $k->poin }}" min="0" required class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Urutan</label>
                                        <input type="number" name="urutan" value="{{ $k->urutan }}" min="0" class="w-full rounded-lg border-gray-300 text-sm">
                                    </div>
                                    <div class="flex items-end pb-1.5">
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                            <input type="checkbox" name="aktif" value="1" {{ $k->aktif ? 'checked' : '' }} class="rounded border-gray-300 text-green-600">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Simpan</button>
                                    <button type="button" @click="$refs.editModal{{ $k->id }}.close()" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</button>
                                </div>
                            </form>
                        </dialog>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
