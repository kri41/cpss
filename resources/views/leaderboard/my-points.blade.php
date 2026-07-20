@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Poin & Lencana Saya</h1>
            <p class="mt-1 text-sm text-gray-500">Riwayat transaksi poin dan pencapaian lencana</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Poin</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalPoin }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Lencana</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $badges->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Transaksi Valid</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $transactions->where('status', 'valid')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Badges -->
        @if($badges->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Lencana yang Diperoleh</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($badges as $badge)
                <div class="bg-white rounded-xl shadow-sm p-4 text-center border border-gray-100">
                    <div class="mx-auto h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mb-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ $badge->nama }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $badge->deskripsi }}</p>
                    <p class="text-xs text-blue-500 mt-1">{{ \Carbon\Carbon::parse($badge->pivot->earned_at)->format('d M Y') }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Transaction History -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Riwayat Transaksi Poin</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktivitas</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Poin</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $tx->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                {{ str_replace('_', ' ', $tx->related_type) }}
                            </td>
                            <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                {{ $tx->jenis_aksi }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-bold {{ $tx->status === 'valid' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $tx->status === 'valid' ? '+' : '' }}{{ $tx->poin }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                @if($tx->status === 'valid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Valid</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                    @if($tx->alasan_pembatalan)
                                        <p class="text-xs text-gray-500 mt-1">{{ $tx->alasan_pembatalan }}</p>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                Belum ada transaksi poin.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
