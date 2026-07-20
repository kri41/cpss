@extends('layouts.app')

@section('title', $kampung->nama_kampung . ' - Kampung Olahraga')

@section('content')
@php
    $skor = $kampung->skorPoin();
    $maxPoin = $komponenList->sum('poin');
    $isAdmin = auth()->user()->isAdmin();
    $isOwner = $kampung->user_id === auth()->id();
@endphp

<div x-data="{ rejectOpen: false }" class="max-w-5xl mx-auto px-4 py-4 sm:px-6">

    {{-- Back --}}
    <div class="mb-4">
        <a href="{{ route('kampung.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid lg:grid-cols-3 gap-5">

        {{-- LEFT: Info + QR --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-50 flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl font-bold text-gray-900">{{ $kampung->nama_kampung }}</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ collect([$kampung->desa, $kampung->kecamatan, $kampung->kabupaten, $kampung->provinsi])->filter()->implode(', ') ?: 'Lokasi belum diisi' }}
                        </p>
                    </div>
                    @php
                        $stColor = match($kampung->status_validasi) {
                            'validated' => 'bg-emerald-100 text-emerald-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            default     => 'bg-amber-100 text-amber-700',
                        };
                        $stLabel = match($kampung->status_validasi) {
                            'validated' => 'Terverifikasi',
                            'rejected'  => 'Ditolak',
                            default     => 'Menunggu Verifikasi',
                        };
                    @endphp
                    <span class="ml-3 shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $stColor }}">{{ $stLabel }}</span>
                </div>

                <div class="p-5 space-y-2 text-sm text-gray-600">
                    @if($kampung->alamat)
                    <div class="flex gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>{{ $kampung->alamat }}</span></div>
                    @endif
                    <div class="flex gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg><span>Relawan: <strong>{{ $kampung->user?->name ?? '-' }}</strong></span></div>
                    <div class="flex gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><span>Didaftarkan {{ $kampung->created_at->isoFormat('D MMM YYYY') }}</span></div>
                    @if($kampung->catatan_admin)
                    <div class="flex gap-2 p-3 bg-red-50 rounded-lg text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Catatan Admin: {{ $kampung->catatan_admin }}</span>
                    </div>
                    @endif
                </div>

                {{-- Admin Actions --}}
                @if($isAdmin)
                <div class="px-5 pb-5 flex gap-2 flex-wrap">
                    @if($kampung->status_validasi === 'pending')
                    <form method="POST" action="{{ route('kampung.validate', $kampung) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition">
                            Verifikasi & Aktifkan QR
                        </button>
                    </form>
                    <button @click="rejectOpen = true" class="px-4 py-2 text-sm font-semibold bg-red-50 hover:bg-red-100 text-red-700 rounded-xl transition">Tolak</button>
                    @elseif($kampung->status_validasi === 'validated')
                    <form method="POST" action="{{ route('kampung.cancel-validate', $kampung) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 text-sm font-semibold bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl transition">Batalkan Verifikasi</button>
                    </form>
                    @elseif($kampung->status_validasi === 'rejected')
                    <form method="POST" action="{{ route('kampung.validate', $kampung) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 text-sm font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl transition">Verifikasi Ulang</button>
                    </form>
                    @endif
                </div>
                @elseif($isOwner && $kampung->status_validasi !== 'validated')
                <div class="px-5 pb-5 flex gap-2">
                    <a href="{{ route('kampung.edit', $kampung) }}" class="px-4 py-2 text-sm font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl transition">Edit Data</a>
                    <form method="POST" action="{{ route('kampung.destroy', $kampung) }}" onsubmit="return confirm('Hapus kampung ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-semibold bg-red-50 hover:bg-red-100 text-red-700 rounded-xl transition">Hapus</button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Komponen Syarat Progress --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-gray-800">Komponen Syarat Kemenpora</h2>
                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full">Skor: {{ $skor }} / {{ $maxPoin }} poin</span>
                </div>
                @if($komponenList->isEmpty())
                <p class="text-sm text-gray-400 text-center py-3">Belum ada komponen syarat yang ditetapkan.</p>
                @else
                <div class="space-y-3">
                    @foreach($komponenList as $k)
                    @php
                        $fulfilled = $totalCheckin >= $k->target_checkin;
                        $pct = $k->target_checkin > 0 ? min(100, round($totalCheckin / $k->target_checkin * 100)) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                @if($fulfilled)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                                <span class="text-xs font-medium {{ $fulfilled ? 'text-emerald-700' : 'text-gray-700' }}">{{ $k->nama }}</span>
                            </div>
                            <span class="text-xs {{ $fulfilled ? 'text-emerald-600 font-bold' : 'text-gray-400' }}">
                                {{ number_format($totalCheckin) }} / {{ number_format($k->target_checkin) }} &middot; {{ $k->poin }}pt
                            </span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $fulfilled ? 'bg-emerald-500' : 'bg-blue-400' }}" style="width: {{ $pct }}%"></div>
                        </div>
                        @if($k->deskripsi)
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $k->deskripsi }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Recent Check-ins --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-800">Riwayat Check-in</h2>
                    <span class="text-xs text-gray-500">{{ number_format($totalCheckin) }} total</span>
                </div>
                @if($recentCheckins->isEmpty())
                <div class="text-center py-8 text-sm text-gray-400">Belum ada check-in</div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead><tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-2 text-left">Nama</th>
                            <th class="px-4 py-2 text-left">Umur</th>
                            <th class="px-4 py-2 text-left">Olahraga</th>
                            <th class="px-4 py-2 text-left">Foto</th>
                            <th class="px-4 py-2 text-left">Waktu</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50">
                        @foreach($recentCheckins as $ci)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-medium text-gray-900">{{ $ci->nama_peserta }}</td>
                            <td class="px-4 py-2.5 text-gray-600">{{ $ci->umur }} th</td>
                            <td class="px-4 py-2.5 text-gray-600">{{ $ci->jenisOlahraga?->nama ?? $ci->jenis_olahraga_nama ?? '-' }}</td>
                            <td class="px-4 py-2.5">
                                @if($ci->foto)
                                <a href="{{ asset('storage/' . $ci->foto) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $ci->foto) }}" class="h-8 w-8 rounded object-cover ring-1 ring-gray-200">
                                </a>
                                @else
                                <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $ci->created_at->isoFormat('D MMM, HH:mm') }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if($totalCheckin > 20)
                <div class="px-4 py-3 text-xs text-center text-gray-400">Menampilkan 20 terbaru dari {{ number_format($totalCheckin) }} check-in</div>
                @endif
                @endif
            </div>

        </div>

        {{-- RIGHT: QR Code --}}
        <div class="space-y-4">

            @if($kampung->status_validasi === 'validated' && $qrSvg)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <div class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5c0 1.5-1.5 3-3 3s-3-1.5-3-3 1.5-3 3-3 3 1.5 3 3z"/></svg>
                    QR Check-in Aktif
                </div>

                <div class="inline-block p-3 bg-white rounded-xl border border-gray-200 shadow-inner mb-3">
                    {!! $qrSvg !!}
                </div>

                <p class="text-xs text-gray-500 mb-3">Scan QR code ini untuk check-in olahraga di kampung ini.</p>

                <a href="{{ $qrUrl }}" target="_blank" class="block w-full px-3 py-2 text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl transition mb-2">
                    Buka Link Check-in
                </a>

                <button onclick="window.print()" class="block w-full px-3 py-2 text-xs font-semibold bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl transition">
                    Print QR Code
                </button>
            </div>
            @elseif($kampung->status_validasi !== 'validated')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-700 mb-1">QR Belum Aktif</p>
                <p class="text-xs text-gray-500">QR Code akan aktif setelah kampung ini diverifikasi oleh admin.</p>
            </div>
            @endif

            {{-- Stat summary --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-3">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Statistik</h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Total Check-in</span>
                    <span class="font-bold text-gray-900">{{ number_format($totalCheckin) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Skor Poin</span>
                    <span class="font-bold text-blue-600">{{ $skor }} pt</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Komponen Terpenuhi</span>
                    <span class="font-bold text-emerald-600">{{ $komponenList->filter(fn($k) => $totalCheckin >= $k->target_checkin)->count() }} / {{ $komponenList->count() }}</span>
                </div>
                @if($totalCheckin > 0)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Check-in Terbaru</span>
                    <span class="text-gray-500 text-xs">{{ $recentCheckins->first()?->created_at->diffForHumans() }}</span>
                </div>
                @endif
            </div>

        </div>
    </div>

</div>

{{-- Reject Modal --}}
<div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div @click.away="rejectOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
        <h3 class="text-base font-bold text-gray-900 mb-1">Tolak Kampung Olahraga</h3>
        <p class="text-sm text-gray-500 mb-4">Berikan alasan penolakan agar relawan dapat memperbaiki data.</p>
        <form method="POST" action="{{ route('kampung.reject', $kampung) }}">
            @csrf @method('PATCH')
            <textarea name="catatan_admin" rows="3" placeholder="Tuliskan alasan penolakan..." required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm mb-4 resize-none"></textarea>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition">Tolak</button>
                <button type="button" @click="rejectOpen = false" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</button>
            </div>
        </form>
    </div>
</div>

<style>
@media print {
    .lg\:ml-64, header, aside, nav, .sticky, a, button, form { display: none !important; }
    .inline-block { display: block !important; }
}
</style>
@endsection
