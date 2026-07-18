@extends('layouts.app')

@section('title', 'Preview Import Pengguna - Dataraga')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('users.import.form') }}" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Preview Import Pengguna</h1>
                <p class="text-sm text-gray-500 mt-0.5">Tinjau data sebelum disimpan ke database</p>
            </div>
        </div>

        {{-- Ringkasan Badge --}}
        <div class="flex flex-wrap gap-3 mb-6">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-xl">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <span class="text-sm font-semibold text-emerald-700">{{ count($valid) }} data valid</span>
            </div>
            @if(count($invalid) > 0)
            <div class="flex items-center gap-2 px-4 py-2.5 bg-red-50 border border-red-200 rounded-xl">
                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                <span class="text-sm font-semibold text-red-700">{{ count($invalid) }} data error</span>
            </div>
            @endif
            <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl">
                <span class="text-sm text-gray-600">Total: <strong>{{ count($valid) + count($invalid) }}</strong> baris</span>
            </div>
        </div>

        {{-- Data Error --}}
        @if(count($invalid) > 0)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <h2 class="text-sm font-bold text-red-700">Data yang Tidak Bisa Diimport</h2>
            </div>
            <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-red-50 border-b border-red-100">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-red-600 uppercase tracking-wide">Baris</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-red-600 uppercase tracking-wide">Nama</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-red-600 uppercase tracking-wide">Email</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-red-600 uppercase tracking-wide">Role</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-red-600 uppercase tracking-wide">Alasan Error</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-50">
                            @foreach($invalid as $row)
                            <tr class="hover:bg-red-50/50">
                                <td class="px-4 py-3 text-gray-500 font-mono">{{ $row['baris'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $row['name'] }}</td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $row['email'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">{{ $row['role'] ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($row['errors'] as $err)
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 font-medium">{{ $err }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Data Valid --}}
        @if(count($valid) > 0)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h2 class="text-sm font-bold text-emerald-700">Data yang Akan Diimport</h2>
            </div>
            <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-emerald-50 border-b border-emerald-100">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wide">#</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wide">Nama</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wide">Email</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wide">Role</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wide">Wilayah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-emerald-50">
                            @foreach($valid as $i => $row)
                            <tr class="hover:bg-emerald-50/40">
                                <td class="px-4 py-3 text-gray-400 font-mono">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $row['name'] }}</td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $row['email'] }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $roleColor = match($row['role']) {
                                            'super_admin' => 'bg-purple-100 text-purple-700',
                                            'admin'       => 'bg-blue-100 text-blue-700',
                                            default       => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full font-medium {{ $roleColor }}">{{ $row['role'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    {{ collect([$row['kabupaten'], $row['kecamatan'], $row['desa']])->filter()->implode(', ') ?: '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tombol Konfirmasi --}}
        <div class="flex flex-wrap items-center gap-3">
            <form method="POST" action="{{ route('users.import.confirm.store') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Konfirmasi Import {{ count($valid) }} Pengguna
                </button>
            </form>
            <a href="{{ route('users.import.form') }}"
               class="px-5 py-3 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                Upload Ulang
            </a>
            <a href="{{ route('users.index') }}"
               class="px-5 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                Batal
            </a>
        </div>

        @else
        {{-- Semua data error, tidak ada yang bisa diimport --}}
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-5">
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Tidak ada data yang bisa diimport</p>
                    <p class="text-sm text-amber-700 mt-1">Semua baris memiliki error. Perbaiki file CSV dan upload ulang.</p>
                </div>
            </div>
        </div>
        <a href="{{ route('users.import.form') }}"
           class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition shadow-sm text-sm">
            Upload Ulang
        </a>
        @endif

    </div>
</div>
@endsection
