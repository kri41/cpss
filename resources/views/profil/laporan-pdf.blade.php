<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @include('pdf.partials.styles')
</style>
</head>
<body>

@php
    $pill = fn ($s) => match ($s) {
        'validated' => 'pill pill-green',
        'rejected'  => 'pill pill-red',
        default     => 'pill pill-amber',
    };
    $pillText = fn ($s) => match ($s) {
        'validated' => 'Valid',
        'rejected'  => 'Tolak',
        default     => 'Pending',
    };
    $lokasi = fn ($m) => collect([$m->desa, $m->kecamatan, $m->kabupaten])->filter()->implode(', ') ?: '-';

    $validated = $items->where('status_validasi', 'validated')->count();
    $rejected  = $items->where('status_validasi', 'rejected')->count();
    $pending   = $items->count() - $validated - $rejected;

    $pdfFooterLeft = $judul . ' — ' . $user->name;
@endphp

{{-- ===== LETTERHEAD ===== --}}
@include('pdf.partials.letterhead', ['docType' => 'Laporan Kontribusi Relawan'])

{{-- ===== JUDUL ===== --}}
<div class="report-band">
    <h1>{{ $judul }}</h1>
    <p>Data kontribusi relawan yang dihasilkan otomatis dari sistem Dataraga.</p>
    <span class="band-badge">Per {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</span>
</div>

{{-- ===== IDENTITAS ===== --}}
<div class="identity">
    <div class="id-main">
        <div class="id-name">{{ $user->name }}</div>
        <div class="id-sub">{{ $user->email }}</div>
        <div class="id-sub">{{ collect([$user->desa, $user->kecamatan, $user->kabupaten])->filter()->implode(', ') ?: '-' }}</div>
        <div style="margin-top:5px"><span class="id-chip">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span></div>
    </div>
    <div class="id-side">
        <div class="id-rank">{{ $items->count() }}</div>
        <div class="id-rank-label">total entri</div>
    </div>
</div>

{{-- ===== RINGKASAN ===== --}}
<div class="kpi-row">
    <div class="kpi accent">
        <div class="kpi-num">{{ $items->count() }}</div>
        <div class="kpi-label">Total Data</div>
        <div class="kpi-sub">&nbsp;</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ $validated }}</div>
        <div class="kpi-label">Tervalidasi</div>
        <div class="kpi-sub">{{ $items->count() ? round($validated / $items->count() * 100) : 0 }}%</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ $pending }}</div>
        <div class="kpi-label">Menunggu</div>
        <div class="kpi-sub">&nbsp;</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ $rejected }}</div>
        <div class="kpi-label">Ditolak</div>
        <div class="kpi-sub">&nbsp;</div>
    </div>
</div>

{{-- ===== TABEL DATA ===== --}}
<div class="section">
    <div class="section-head">Rincian Data <span class="count">{{ $items->count() }} entri</span></div>

    @if($items->isEmpty())
        <div class="empty">Belum ada data untuk kategori ini.</div>

    @elseif($jenis === 'prasarana')
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Fasilitas</th>
                <th style="width:90px">Kategori</th>
                <th>Lokasi</th>
                <th style="width:42px">Status</th>
                <th style="width:52px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $item->nama_fasilitas ?? '-' }}</td>
                    <td>{{ $item->kategori_olahraga_label ?? '-' }}</td>
                    <td>{{ $lokasi($item) }}</td>
                    <td><span class="{{ $pill($item->status_validasi) }}">{{ $pillText($item->status_validasi) }}</span></td>
                    <td>{{ $item->created_at->format('d/m/y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @elseif($jenis === 'events')
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Event</th>
                <th style="width:64px">Tingkat</th>
                <th>Lokasi</th>
                <th style="width:52px">Mulai</th>
                <th style="width:42px">Status</th>
            </tr></thead>
            <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $item->nama_event ?? '-' }}</td>
                    <td>{{ $item->tingkat ?? '-' }}</td>
                    <td>{{ $lokasi($item) }}</td>
                    <td>{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/y') : '-' }}</td>
                    <td><span class="{{ $pill($item->status_validasi) }}">{{ $pillText($item->status_validasi) }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @elseif($jenis === 'clubs')
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Klub</th>
                <th style="width:90px">Cabang</th>
                <th>Lokasi</th>
                <th style="width:42px">Status</th>
                <th style="width:52px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $item->nama_club ?? '-' }}</td>
                    <td>{{ $item->jenisOlahraga?->nama ?? '-' }}</td>
                    <td>{{ $lokasi($item) }}</td>
                    <td><span class="{{ $pill($item->status_validasi) }}">{{ $pillText($item->status_validasi) }}</span></td>
                    <td>{{ $item->created_at->format('d/m/y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @elseif($jenis === 'partisipasi')
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Lokasi Observasi</th>
                <th>Wilayah</th>
                <th class="num" style="width:52px">Est. Org</th>
                <th style="width:42px">Status</th>
                <th style="width:52px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $item->lokasi_observasi ?? '-' }}</td>
                    <td>{{ $lokasi($item) }}</td>
                    <td class="num">{{ number_format($item->estimasi_jumlah_orang ?? 0) }}</td>
                    <td><span class="{{ $pill($item->status_validasi) }}">{{ $pillText($item->status_validasi) }}</span></td>
                    <td>{{ $item->tanggal_observasi ? \Carbon\Carbon::parse($item->tanggal_observasi)->format('d/m/y') : $item->created_at->format('d/m/y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

@include('pdf.partials.footer-script')

</body>
</html>
