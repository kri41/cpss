<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

    .header { border-bottom: 3px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 16px; }
    .header-top { display: table; width: 100%; }
    .header-left { display: table-cell; vertical-align: middle; width: 70%; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }
    .app-name { font-size: 20px; font-weight: bold; color: #1d4ed8; letter-spacing: 1px; }
    .app-sub  { font-size: 10px; color: #64748b; margin-top: 2px; }
    .doc-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
    .doc-date  { font-size: 10px; color: #475569; margin-top: 2px; }

    .judul-box { background: #eff6ff; border-left: 4px solid #1d4ed8; padding: 10px 14px; margin-bottom: 14px; border-radius: 0 4px 4px 0; }
    .judul-box h1 { font-size: 14px; font-weight: bold; color: #1e3a8a; }
    .judul-box p  { font-size: 10px; color: #3b82f6; margin-top: 3px; }

    .info-table { width: 100%; margin-bottom: 16px; }
    .info-table td { padding: 3px 0; font-size: 10px; }
    .info-table td.label { width: 110px; color: #64748b; }
    .info-table td.sep   { width: 16px; color: #94a3b8; }
    .info-table td.value { color: #1e293b; font-weight: bold; }

    .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .data-table th {
        background: #1d4ed8; color: #fff; padding: 7px 8px;
        text-align: left; font-size: 10px; font-weight: bold;
        text-transform: uppercase; letter-spacing: 0.3px;
    }
    .data-table td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; font-size: 10px; vertical-align: top; }
    .data-table tr:nth-child(even) td { background: #f8fafc; }
    .data-table tr:last-child td { border-bottom: none; }

    .no-col { width: 28px; text-align: center; color: #94a3b8; }

    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    .badge-valid    { background: #dcfce7; color: #166534; }
    .badge-pending  { background: #fef9c3; color: #854d0e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }

    .empty-row td { text-align: center; color: #94a3b8; padding: 24px; font-style: italic; }

    .footer { border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 8px; display: table; width: 100%; }
    .footer-left  { display: table-cell; font-size: 9px; color: #94a3b8; }
    .footer-right { display: table-cell; text-align: right; font-size: 9px; color: #94a3b8; }
    .summary-box { background: #f1f5f9; padding: 8px 12px; border-radius: 4px; margin-bottom: 16px; font-size: 10px; color: #475569; }
    .summary-box strong { color: #1e293b; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <div class="header-top">
        <div class="header-left">
            <div class="app-name">DATARAGA</div>
            <div class="app-sub">Sistem Informasi Olahraga Daerah</div>
        </div>
        <div class="header-right">
            <div class="doc-label">Tanggal Cetak</div>
            <div class="doc-date">{{ now()->isoFormat('D MMMM YYYY') }}</div>
        </div>
    </div>
</div>

{{-- JUDUL --}}
<div class="judul-box">
    <h1>{{ $judul }}</h1>
    <p>Data kontribusi relawan — dicetak otomatis dari sistem</p>
</div>

{{-- INFO RELAWAN --}}
<table class="info-table">
    <tr>
        <td class="label">Nama Relawan</td>
        <td class="sep">:</td>
        <td class="value">{{ $user->name }}</td>
    </tr>
    <tr>
        <td class="label">Email</td>
        <td class="sep">:</td>
        <td class="value">{{ $user->email }}</td>
    </tr>
    @if($user->kabupaten)
    <tr>
        <td class="label">Wilayah</td>
        <td class="sep">:</td>
        <td class="value">{{ collect([$user->desa, $user->kecamatan, $user->kabupaten])->filter()->implode(', ') }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">Total Data</td>
        <td class="sep">:</td>
        <td class="value">{{ $items->count() }} entri</td>
    </tr>
</table>

{{-- RINGKASAN --}}
@php
    $validated = $items->where('status_validasi', 'validated')->count();
    $pending   = $items->where('status_validasi', 'pending')->count();
    $rejected  = $items->where('status_validasi', 'rejected')->count();
@endphp
<div class="summary-box">
    Total: <strong>{{ $items->count() }}</strong> &nbsp;|&nbsp;
    Tervalidasi: <strong>{{ $validated }}</strong> &nbsp;|&nbsp;
    Menunggu: <strong>{{ $pending }}</strong> &nbsp;|&nbsp;
    Ditolak: <strong>{{ $rejected }}</strong>
</div>

{{-- TABEL DATA --}}
@if($jenis === 'prasarana')
<table class="data-table">
    <thead>
        <tr>
            <th class="no-col">#</th>
            <th>Nama Prasarana</th>
            <th style="width:110px">Jenis</th>
            <th>Lokasi</th>
            <th style="width:70px">Status</th>
            <th style="width:80px">Tgl Input</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $i => $item)
        <tr>
            <td class="no-col">{{ $i + 1 }}</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->jenis ?? '-' }}</td>
            <td>{{ collect([$item->desa, $item->kecamatan, $item->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
            <td>
                <span class="badge {{ $item->status_validasi === 'validated' ? 'badge-valid' : ($item->status_validasi === 'rejected' ? 'badge-rejected' : 'badge-pending') }}">
                    {{ $item->status_validasi === 'validated' ? 'Valid' : ($item->status_validasi === 'rejected' ? 'Ditolak' : 'Pending') }}
                </span>
            </td>
            <td>{{ $item->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="6">Belum ada data prasarana.</td></tr>
        @endforelse
    </tbody>
</table>

@elseif($jenis === 'events')
<table class="data-table">
    <thead>
        <tr>
            <th class="no-col">#</th>
            <th>Nama Event</th>
            <th style="width:80px">Tanggal</th>
            <th>Lokasi</th>
            <th style="width:70px">Status</th>
            <th style="width:80px">Tgl Input</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $i => $item)
        <tr>
            <td class="no-col">{{ $i + 1 }}</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
            <td>{{ $item->lokasi ?? '-' }}</td>
            <td>
                <span class="badge {{ $item->status_validasi === 'validated' ? 'badge-valid' : ($item->status_validasi === 'rejected' ? 'badge-rejected' : 'badge-pending') }}">
                    {{ $item->status_validasi === 'validated' ? 'Valid' : ($item->status_validasi === 'rejected' ? 'Ditolak' : 'Pending') }}
                </span>
            </td>
            <td>{{ $item->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="6">Belum ada data event.</td></tr>
        @endforelse
    </tbody>
</table>

@elseif($jenis === 'clubs')
<table class="data-table">
    <thead>
        <tr>
            <th class="no-col">#</th>
            <th>Nama Klub</th>
            <th style="width:100px">Cabang Olahraga</th>
            <th>Lokasi</th>
            <th style="width:70px">Status</th>
            <th style="width:80px">Tgl Input</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $i => $item)
        <tr>
            <td class="no-col">{{ $i + 1 }}</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->cabang_olahraga ?? $item->jenis_olahraga ?? '-' }}</td>
            <td>{{ collect([$item->desa, $item->kecamatan, $item->kabupaten])->filter()->implode(', ') ?: ($item->lokasi ?? '-') }}</td>
            <td>
                <span class="badge {{ $item->status_validasi === 'validated' ? 'badge-valid' : ($item->status_validasi === 'rejected' ? 'badge-rejected' : 'badge-pending') }}">
                    {{ $item->status_validasi === 'validated' ? 'Valid' : ($item->status_validasi === 'rejected' ? 'Ditolak' : 'Pending') }}
                </span>
            </td>
            <td>{{ $item->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="6">Belum ada data klub.</td></tr>
        @endforelse
    </tbody>
</table>

@elseif($jenis === 'partisipasi')
<table class="data-table">
    <thead>
        <tr>
            <th class="no-col">#</th>
            <th>Nama Kegiatan</th>
            <th style="width:80px">Tanggal</th>
            <th style="width:70px">Est. Peserta</th>
            <th style="width:70px">Status</th>
            <th style="width:80px">Tgl Input</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $i => $item)
        <tr>
            <td class="no-col">{{ $i + 1 }}</td>
            <td>{{ $item->nama_kegiatan ?? $item->nama ?? '-' }}</td>
            <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
            <td style="text-align:center">{{ number_format($item->estimasi_jumlah_orang ?? 0) }}</td>
            <td>
                <span class="badge {{ $item->status_validasi === 'validated' ? 'badge-valid' : ($item->status_validasi === 'rejected' ? 'badge-rejected' : 'badge-pending') }}">
                    {{ $item->status_validasi === 'validated' ? 'Valid' : ($item->status_validasi === 'rejected' ? 'Ditolak' : 'Pending') }}
                </span>
            </td>
            <td>{{ $item->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="6">Belum ada data partisipasi.</td></tr>
        @endforelse
    </tbody>
</table>
@endif

{{-- FOOTER --}}
<div class="footer">
    <div class="footer-left">Dataraga &copy; {{ now()->year }} &mdash; Dokumen ini dibuat otomatis oleh sistem</div>
    <div class="footer-right">{{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</div>
</div>

</body>
</html>
