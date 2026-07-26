@extends('layouts.app')

@section('title', 'Kelola Pengumuman - Dataraga')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-4 sm:px-6">

    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
        <h1 class="text-base font-bold text-gray-800">Kelola Pengumuman</h1>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid lg:grid-cols-5 gap-5">

        {{-- Form Tambah --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-sm font-bold text-gray-800 mb-4">Tambah Pengumuman</h2>
                <form method="POST" action="{{ route('pengumuman.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Cth: Jadwal Verifikasi Minggu Ini" required
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 @error('judul') border-red-400 @enderror">
                        @error('judul')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Isi <span class="text-red-500">*</span></label>
                        <textarea name="isi" rows="4" placeholder="Tulis isi pengumuman..." required
                                  class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 resize-none @error('isi') border-red-400 @enderror">{{ old('isi') }}</textarea>
                        @error('isi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Tambahkan</button>
                </form>
            </div>

            <div class="mt-4 bg-blue-50 rounded-2xl border border-blue-100 p-4 text-xs text-blue-700 space-y-1.5">
                <p class="font-semibold text-blue-800">Cara Kerja Pengumuman</p>
                <p>Pengumuman yang <strong>Aktif</strong> akan tampil di halaman dashboard untuk semua pengguna yang login.</p>
                <p>Nonaktifkan pengumuman lama tanpa perlu menghapusnya — riwayatnya tetap tersimpan di sini.</p>
            </div>
        </div>

        {{-- Daftar Pengumuman --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50">
                    <h2 class="text-sm font-bold text-gray-800">Daftar Pengumuman ({{ $pengumuman->count() }})</h2>
                </div>

                @if($pengumuman->isEmpty())
                <div class="text-center py-8 text-sm text-gray-400">Belum ada pengumuman.</div>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($pengumuman as $item)
                    <div class="p-4 hover:bg-gray-50 transition" x-data>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ $item->judul }}</span>
                                    @if($item->aktif)
                                    <span class="text-[10px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded-full font-medium">Aktif</span>
                                    @else
                                    <span class="text-[10px] bg-gray-100 text-gray-400 px-1.5 py-0.5 rounded-full font-medium">Nonaktif</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 whitespace-pre-line">{{ $item->isi }}</p>
                                <p class="text-[10px] text-gray-400 mt-1">{{ $item->user?->name ?? '-' }} &middot; {{ $item->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <button @click="$refs.editModal{{ $item->id }}.showModal()" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition text-xs">Edit</button>
                                <form method="POST" action="{{ route('pengumuman.destroy', $item) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition text-xs">Hapus</button>
                                </form>
                            </div>
                        </div>

                        {{-- Edit dialog --}}
                        <dialog x-ref="editModal{{ $item->id }}" class="rounded-2xl shadow-2xl max-w-sm w-full p-0 border-0 backdrop:bg-black/50">
                            <form method="POST" action="{{ route('pengumuman.update', $item) }}" class="p-5 space-y-3">
                                @csrf @method('PUT')
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-bold text-gray-900 text-sm">Edit Pengumuman</h3>
                                    <button type="button" @click="$refs.editModal{{ $item->id }}.close()" class="text-gray-400 hover:text-gray-600">✕</button>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Judul</label>
                                    <input type="text" name="judul" value="{{ $item->judul }}" required class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Isi</label>
                                    <textarea name="isi" rows="4" required class="w-full rounded-lg border-gray-300 text-sm resize-none">{{ $item->isi }}</textarea>
                                </div>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="aktif" value="1" {{ $item->aktif ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                    Aktif (tampil di dashboard)
                                </label>
                                <div class="flex gap-2 pt-1">
                                    <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Simpan</button>
                                    <button type="button" @click="$refs.editModal{{ $item->id }}.close()" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</button>
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
