@extends('layouts.app')

@section('title', $kampung->nama_kampung . ' - Kampung Olahraga')

@section('content')
@php
    $skor = $kampung->skorPoin();
    $maxPoin = $komponenList->sum('poin');
    $isAdmin = auth()->user()->isAdmin();
    $isOwner = $kampung->user_id === auth()->id();
    $canManage = $isAdmin || $isOwner;
@endphp

<div x-data="{ rejectOpen: false, qrOpen: false, qrShown: null, attachFasilOpen: false, attachKlubOpen: false }">
<div class="max-w-5xl mx-auto px-4 py-4 sm:px-6">

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
                            @if($kampung->rt_rw_label)
                            <span class="font-medium text-gray-600">{{ $kampung->rt_rw_label }}</span> &middot;
                            @endif
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

            {{-- Fasil Terdaftar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">Fasil Terdaftar</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Fasilitas (dari menu Prasarana) yang menjadi bagian rumah kampung ini. Tiap fasil punya QR check-in sendiri.</p>
                    </div>
                    @if($canManage)
                    <button @click="attachFasilOpen = true" class="shrink-0 px-3 py-1.5 text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition">+ Daftarkan Fasil</button>
                    @endif
                </div>
                @if($kampung->fasil->isEmpty())
                <div class="text-center py-8 text-sm text-gray-400">Belum ada fasil yang terdaftar di kampung ini.</div>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($kampung->fasil as $fasil)
                    <div class="px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $fasil->nama_fasilitas }}</p>
                            <p class="text-xs text-gray-500">{{ $fasil->kategori_olahraga }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if(isset($fasilQr[$fasil->id]))
                            <button @click="qrShown = {{ $fasil->id }}; qrOpen = true" class="px-3 py-1.5 text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition">Lihat QR</button>
                            @else
                            <span class="px-3 py-1.5 text-xs font-medium bg-amber-50 text-amber-600 rounded-lg">QR belum aktif</span>
                            @endif
                            @if($canManage)
                            <form method="POST" action="{{ route('kampung.fasil.detach', [$kampung, $fasil]) }}" onsubmit="return confirm('Lepas fasil ini dari kampung? QR akan dinonaktifkan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-700 rounded-lg transition">Lepas</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Klub/Komunitas Terdaftar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">Klub/Komunitas Terdaftar</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Muncul sebagai pilihan saat peserta check-in via QR di kampung ini.</p>
                    </div>
                    @if($canManage)
                    <button @click="attachKlubOpen = true" class="shrink-0 px-3 py-1.5 text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition">+ Daftarkan Klub</button>
                    @endif
                </div>
                @if($kampung->klubKomunitas->isEmpty())
                <div class="text-center py-8 text-sm text-gray-400">Belum ada klub/komunitas yang terdaftar di kampung ini.</div>
                @else
                <div class="p-5 flex flex-wrap gap-2">
                    @foreach($kampung->klubKomunitas as $klub)
                    <span class="inline-flex items-center gap-2 pl-3 pr-1.5 py-1.5 bg-gray-50 border border-gray-200 rounded-full text-xs font-medium text-gray-700">
                        {{ $klub->nama_club }}
                        @if($canManage)
                        <form method="POST" action="{{ route('kampung.klub.detach', [$kampung, $klub]) }}" onsubmit="return confirm('Lepas klub/komunitas ini dari kampung?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-5 h-5 inline-flex items-center justify-center rounded-full hover:bg-red-100 text-gray-400 hover:text-red-600 transition">&times;</button>
                        </form>
                        @endif
                    </span>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Komponen Syarat Progress --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0369a1 100%);">
                    <div style="position:absolute; top:-30px; right:-20px; width:120px; height:120px; background:rgba(255,255,255,0.06); border-radius:9999px;"></div>
                    <div class="relative flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0 w-10 h-10 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center">
                                <i class="fas fa-shield-halved text-amber-300"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-white leading-tight">Seberapa Aktif Kampung Olahragamu?</h2>
                                <p class="text-[11px] text-blue-200">Menuju pengakuan resmi Kemenpora RI</p>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-[10px] text-blue-200 uppercase tracking-wide">Skor</p>
                            <p class="text-lg font-black text-white leading-tight">{{ $skor }}<span class="text-xs font-medium text-blue-200">/{{ $maxPoin }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    @if($komponenList->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-3">Belum ada komponen syarat yang ditetapkan.</p>
                    @else
                    @php
                        $nextIndex = $komponenList->search(fn($k) => $totalCheckin < $k->target_checkin);
                    @endphp
                    <div class="relative space-y-5">
                        @foreach($komponenList as $i => $k)
                        @php
                            $fulfilled = $totalCheckin >= $k->target_checkin;
                            $isNext = $i === $nextIndex;
                            $pct = $k->target_checkin > 0 ? min(100, round($totalCheckin / $k->target_checkin * 100)) : 0;
                        @endphp
                        <div class="relative flex gap-3">
                            {{-- Tangga / connector --}}
                            <div class="flex flex-col items-center shrink-0">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                    {{ $fulfilled ? 'bg-emerald-500 text-white shadow-md shadow-emerald-200' : ($isNext ? 'bg-white border-2 border-blue-400 text-blue-600 animate-pulse' : 'bg-gray-100 text-gray-400') }}">
                                    @if($fulfilled)
                                    <i class="fas fa-check text-xs"></i>
                                    @else
                                    {{ $i + 1 }}
                                    @endif
                                </div>
                                @if(!$loop->last)
                                <div class="w-0.5 flex-1 mt-1 rounded-full {{ $fulfilled ? 'bg-emerald-300' : 'bg-gray-100' }}" style="min-height: 1.75rem;"></div>
                                @endif
                            </div>

                            <div class="flex-1 pb-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-sm font-bold {{ $fulfilled ? 'text-emerald-700' : 'text-gray-800' }}">{{ $k->nama }}</span>
                                            @if($fulfilled)
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full">Tercapai</span>
                                            @elseif($isNext)
                                            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-full">Target berikutnya</span>
                                            @endif
                                        </div>
                                        @if($k->deskripsi)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $k->deskripsi }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 inline-flex items-center gap-1 text-[11px] font-bold {{ $fulfilled ? 'text-emerald-600' : 'text-amber-600' }}">
                                        <i class="fas fa-medal text-[10px]"></i> +{{ $k->poin }}
                                    </span>
                                </div>

                                <div class="mt-2 flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all {{ $fulfilled ? 'bg-emerald-500' : 'bg-gradient-to-r from-blue-400 to-sky-400' }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-[11px] shrink-0 {{ $fulfilled ? 'text-emerald-600 font-bold' : 'text-gray-400' }}">
                                        {{ number_format($totalCheckin) }}/{{ number_format($k->target_checkin) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
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

        {{-- RIGHT: QR Info --}}
        <div class="space-y-4">

            @if($kampung->status_validasi === 'validated')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5c0 1.5-1.5 3-3 3s-3-1.5-3-3 1.5-3 3-3 3 1.5 3 3z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-700 mb-1">QR Aktif per Fasil</p>
                <p class="text-xs text-gray-500">Setiap fasil yang terdaftar punya QR check-in sendiri. Lihat tombol "Lihat QR" pada daftar Fasil Terdaftar di samping.</p>
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-700 mb-1">QR Belum Aktif</p>
                <p class="text-xs text-gray-500">QR fasil akan aktif setelah kampung ini diverifikasi oleh admin.</p>
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
{{-- /max-w-5xl content wrapper --}}

{{-- Per-Fasil QR Modal --}}
@foreach($fasilQr as $fasilId => $qr)
<div x-show="qrOpen && qrShown === {{ $fasilId }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div @click.away="qrOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-xs w-full p-6 text-center">
        <div class="inline-block p-3 bg-white rounded-xl border border-gray-200 shadow-inner mb-3">
            {!! $qr['svg'] !!}
        </div>
        <p class="text-xs text-gray-500 mb-3">Scan QR code ini untuk check-in olahraga di fasil ini.</p>
        <a href="{{ $qr['url'] }}" target="_blank" class="block w-full px-3 py-2 text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl transition mb-2">Buka Link Check-in</a>
        <button onclick="window.print()" class="block w-full px-3 py-2 text-xs font-semibold bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl transition mb-2">Print QR Code</button>
        <button type="button" @click="qrOpen = false" class="block w-full px-3 py-2 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition">Tutup</button>
    </div>
</div>
@endforeach

{{-- Attach Fasil Modal --}}
@if($canManage)
<div x-show="attachFasilOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div @click.away="attachFasilOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
        <h3 class="text-base font-bold text-gray-900 mb-1">Daftarkan Fasil</h3>
        <p class="text-sm text-gray-500 mb-4">Pilih fasilitas (dari menu Prasarana) yang se-wilayah dan belum terdaftar di kampung lain.</p>
        <form method="POST" action="{{ route('kampung.fasil.attach', $kampung) }}">
            @csrf
            <select name="prasarana_id" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm mb-4">
                <option value="">Pilih fasil...</option>
                @foreach($candidateFasil as $f)
                <option value="{{ $f->id }}">{{ $f->nama_fasilitas }} ({{ $f->kategori_olahraga }})</option>
                @endforeach
            </select>
            @if($candidateFasil->isEmpty())
            <p class="text-xs text-amber-600 mb-4">Tidak ada fasil kandidat. Pastikan Prasarana sudah divalidasi, berada di wilayah (kabupaten/kecamatan/desa) yang sama dengan kampung ini, dan belum terdaftar di kampung lain.</p>
            @endif
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Daftarkan</button>
                <button type="button" @click="attachFasilOpen = false" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Attach Klub Modal --}}
<div x-show="attachKlubOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div @click.away="attachKlubOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
        <h3 class="text-base font-bold text-gray-900 mb-1">Daftarkan Klub/Komunitas</h3>
        <p class="text-sm text-gray-500 mb-4">Pilih klub/komunitas yang se-wilayah dan belum terdaftar di kampung ini.</p>
        <form method="POST" action="{{ route('kampung.klub.attach', $kampung) }}">
            @csrf
            <select name="club_id" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm mb-4">
                <option value="">Pilih klub/komunitas...</option>
                @foreach($candidateKlub as $k)
                <option value="{{ $k->id }}">{{ $k->nama_club }}</option>
                @endforeach
            </select>
            @if($candidateKlub->isEmpty())
            <p class="text-xs text-amber-600 mb-4">Tidak ada klub/komunitas kandidat. Pastikan sudah divalidasi, aktif, dan berada di wilayah yang sama dengan kampung ini.</p>
            @endif
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">Daftarkan</button>
                <button type="button" @click="attachKlubOpen = false" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">Batal</button>
            </div>
        </form>
    </div>
</div>
@endif

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

</div>
{{-- /x-data scope --}}

<style>
@media print {
    .lg\:ml-64, header, aside, nav, .sticky, a, button, form { display: none !important; }
    .inline-block { display: block !important; }
}
</style>
@endsection
