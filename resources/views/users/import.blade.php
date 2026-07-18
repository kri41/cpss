@extends('layouts.app')

@section('title', 'Import Bulk Pengguna - Dataraga')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Import Bulk Pengguna</h1>
                <p class="text-sm text-gray-500 mt-0.5">Tambah banyak pengguna sekaligus via file CSV</p>
            </div>
        </div>

        {{-- Langkah --}}
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6">
            <p class="text-sm font-semibold text-blue-800 mb-3">Cara Import:</p>
            <ol class="space-y-2 text-sm text-blue-700">
                <li class="flex items-start gap-2">
                    <span class="shrink-0 w-5 h-5 rounded-full bg-blue-200 text-blue-800 text-[11px] font-bold flex items-center justify-center mt-0.5">1</span>
                    <span>Download template CSV di bawah, buka dengan Excel atau Google Sheets.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="shrink-0 w-5 h-5 rounded-full bg-blue-200 text-blue-800 text-[11px] font-bold flex items-center justify-center mt-0.5">2</span>
                    <span>Isi data pengguna sesuai kolom. Kolom <strong>name, email, password, role</strong> wajib diisi.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="shrink-0 w-5 h-5 rounded-full bg-blue-200 text-blue-800 text-[11px] font-bold flex items-center justify-center mt-0.5">3</span>
                    <span>Simpan file sebagai <strong>CSV (Comma delimited)</strong> — bukan .xlsx.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="shrink-0 w-5 h-5 rounded-full bg-blue-200 text-blue-800 text-[11px] font-bold flex items-center justify-center mt-0.5">4</span>
                    <span>Upload file CSV, klik <strong>Proses &amp; Preview</strong>, cek hasil validasi, lalu konfirmasi import.</span>
                </li>
            </ol>
        </div>

        {{-- Template Download --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Template CSV</p>
                        <p class="text-xs text-gray-500">template_import_user.csv — sudah berisi contoh data</p>
                    </div>
                </div>
                <a href="{{ route('users.import.template') }}"
                   class="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Template
                </a>
            </div>
        </div>

        {{-- Keterangan Kolom --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
            <p class="text-sm font-semibold text-gray-700 mb-3">Keterangan Kolom</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-2 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kolom</th>
                            <th class="text-left py-2 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Wajib</th>
                            <th class="text-left py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr><td class="py-2 pr-4 font-mono text-xs text-blue-700 font-semibold">name</td><td class="py-2 pr-4"><span class="text-red-500 font-bold">Ya</span></td><td class="py-2 text-gray-600">Nama lengkap pengguna</td></tr>
                        <tr><td class="py-2 pr-4 font-mono text-xs text-blue-700 font-semibold">email</td><td class="py-2 pr-4"><span class="text-red-500 font-bold">Ya</span></td><td class="py-2 text-gray-600">Email unik, format valid</td></tr>
                        <tr><td class="py-2 pr-4 font-mono text-xs text-blue-700 font-semibold">password</td><td class="py-2 pr-4"><span class="text-red-500 font-bold">Ya</span></td><td class="py-2 text-gray-600">Minimal 8 karakter</td></tr>
                        <tr><td class="py-2 pr-4 font-mono text-xs text-blue-700 font-semibold">role</td><td class="py-2 pr-4"><span class="text-red-500 font-bold">Ya</span></td><td class="py-2 text-gray-600"><code class="bg-gray-100 px-1 rounded text-[11px]">relawan</code> / <code class="bg-gray-100 px-1 rounded text-[11px]">admin</code> / <code class="bg-gray-100 px-1 rounded text-[11px]">super_admin</code></td></tr>
                        <tr><td class="py-2 pr-4 font-mono text-xs text-blue-600">provinsi</td><td class="py-2 pr-4 text-gray-400">Opsional</td><td class="py-2 text-gray-600">Kode BPS provinsi (contoh: <code class="bg-gray-100 px-1 rounded text-[11px]">35</code> untuk Jawa Timur)</td></tr>
                        <tr><td class="py-2 pr-4 font-mono text-xs text-blue-600">kabupaten</td><td class="py-2 pr-4 text-gray-400">Opsional</td><td class="py-2 text-gray-600">Nama kabupaten/kota</td></tr>
                        <tr><td class="py-2 pr-4 font-mono text-xs text-blue-600">kecamatan</td><td class="py-2 pr-4 text-gray-400">Opsional</td><td class="py-2 text-gray-600">Nama kecamatan</td></tr>
                        <tr><td class="py-2 pr-4 font-mono text-xs text-blue-600">desa</td><td class="py-2 pr-4 text-gray-400">Opsional</td><td class="py-2 text-gray-600">Nama desa/kelurahan</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Upload --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm font-semibold text-gray-700 mb-4">Upload File CSV</p>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('users.import.preview') }}" enctype="multipart/form-data"
                  x-data="{ file: null, dragging: false }">
                @csrf

                {{-- Dropzone --}}
                <label
                    class="block w-full border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition"
                    :class="dragging ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50'"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; file = $event.dataTransfer.files[0]">
                    <input type="file" name="file" accept=".csv,text/csv" class="hidden"
                           @change="file = $event.target.files[0]">

                    <template x-if="!file">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-sm font-medium text-gray-600">Seret & lepas file CSV di sini</p>
                            <p class="text-xs text-gray-400 mt-1">atau klik untuk pilih file &bull; Maks. 2MB</p>
                        </div>
                    </template>
                    <template x-if="file">
                        <div class="flex items-center justify-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-800" x-text="file.name"></p>
                                <p class="text-xs text-gray-400" x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                            </div>
                        </div>
                    </template>
                </label>

                <div class="mt-4 flex items-center gap-3">
                    <button type="submit"
                            :disabled="!file"
                            :class="file ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                            class="flex-1 py-3 text-white font-semibold rounded-xl transition text-sm shadow-sm">
                        Proses &amp; Preview
                    </button>
                    <a href="{{ route('users.index') }}" class="px-5 py-3 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
