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
    $pdfFooterLeft = ($isRelawan ? 'Laporan Data Pribadi' : 'Laporan Keseluruhan Data') . ' — dicetak oleh ' . $user->name;

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
@endphp

{{-- ===== LETTERHEAD ===== --}}
<div class="letterhead">
    <div class="lh-brand">
        <span class="brand-mark">DATARAGA</span>
        <div class="brand-tag">Sistem Informasi Olahraga Daerah &mdash; Kamu Gerak, Indonesia Tahu</div>
    </div>
    <div class="lh-meta">
        <div class="m-label">{{ $isRelawan ? 'Laporan Data Pribadi' : 'Laporan Keseluruhan Data' }}</div>
        <div class="m-value">{{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
    </div>
</div>

{{-- ===== JUDUL ===== --}}
<div class="report-band">
    <h1>{{ $isRelawan ? 'Laporan Kontribusi Data' : 'Laporan Keseluruhan Data Keolahragaan' }}</h1>
    <p>Rekap prasarana, klub/komunitas, event, dan partisipasi kegiatan olahraga.</p>
    <span class="band-badge">{{ $isRelawan ? 'Data Pribadi Relawan' : 'Seluruh Wilayah' }} &middot; Per {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</span>
</div>

{{-- ===== IDENTITAS (relawan) ===== --}}
@if($isRelawan)
<div class="identity">
    <div class="id-main">
        <div class="id-name">{{ $user->name }}</div>
        <div class="id-sub">{{ $user->email }}</div>
        <div class="id-sub">{{ collect([$user->desa, $user->kecamatan, $user->kabupaten])->filter()->implode(', ') ?: '-' }}</div>
    </div>
    <div class="id-side">
        <span class="id-chip">RELAWAN</span>
    </div>
</div>
@endif

{{-- ===== RINGKASAN ===== --}}
<div class="kpi-row">
    <div class="kpi accent">
        <div class="kpi-num">{{ $stats['prasarana'] }}</div>
        <div class="kpi-label">Prasarana</div>
        <div class="kpi-sub">{{ $stats['prasarana_validated'] }} valid</div>
    </div>
    <div class="kpi accent">
        <div class="kpi-num">{{ $stats['clubs'] }}</div>
        <div class="kpi-label">Klub/Komunitas</div>
        <div class="kpi-sub">{{ $stats['clubs_validated'] }} valid</div>
    </div>
    <div class="kpi accent">
        <div class="kpi-num">{{ $stats['events'] }}</div>
        <div class="kpi-label">Event</div>
        <div class="kpi-sub">{{ $stats['events_validated'] }} valid</div>
    </div>
    <div class="kpi accent">
        <div class="kpi-num">{{ $stats['partisipasi'] }}</div>
        <div class="kpi-label">Partisipasi</div>
        <div class="kpi-sub">{{ $stats['partisipasi_validated'] }} valid</div>
    </div>
</div>

{{-- ===== PRASARANA ===== --}}
<div class="section">
    <div class="section-head">Prasarana Olahraga <span class="count">{{ $prasarana->count() }} entri</span></div>
    @if($prasarana->isEmpty())
        <div class="empty">Tidak ada data prasarana.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Fasilitas</th>
                <th style="width:80px">Kategori</th>
                <th>Lokasi</th>
                @unless($isRelawan)<th style="width:70px">Relawan</th>@endunless
                <th style="width:42px">Status</th>
                <th style="width:52px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($prasarana as $i => $p)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $p->nama_fasilitas ?? '-' }}</td>
                    <td>{{ $p->kategori_olahraga_label ?? '-' }}</td>
                    <td>{{ $lokasi($p) }}</td>
                    @unless($isRelawan)<td>{{ $p->user?->name ?? '-' }}</td>@endunless
                    <td><span class="{{ $pill($p->status_validasi) }}">{{ $pillText($p->status_validasi) }}</span></td>
                    <td>{{ $p->created_at->format('d/m/y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ===== KLUB ===== --}}
<div class="section">
    <div class="section-head">Klub / Komunitas <span class="count">{{ $clubs->count() }} entri</span></div>
    @if($clubs->isEmpty())
        <div class="empty">Tidak ada data klub/komunitas.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Klub</th>
                <th style="width:80px">Cabang</th>
                <th>Lokasi</th>
                @unless($isRelawan)<th style="width:70px">Relawan</th>@endunless
                <th style="width:42px">Status</th>
                <th style="width:52px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($clubs as $i => $c)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $c->nama_club ?? '-' }}</td>
                    <td>{{ $c->jenisOlahraga?->nama ?? '-' }}</td>
                    <td>{{ $lokasi($c) }}</td>
                    @unless($isRelawan)<td>{{ $c->user?->name ?? '-' }}</td>@endunless
                    <td><span class="{{ $pill($c->status_validasi) }}">{{ $pillText($c->status_validasi) }}</span></td>
                    <td>{{ $c->created_at->format('d/m/y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ===== EVENT ===== --}}
<div class="section">
    <div class="section-head">Event Olahraga <span class="count">{{ $events->count() }} entri</span></div>
    @if($events->isEmpty())
        <div class="empty">Tidak ada data event.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Event</th>
                <th style="width:60px">Tingkat</th>
                <th>Lokasi</th>
                @unless($isRelawan)<th style="width:70px">Relawan</th>@endunless
                <th style="width:52px">Mulai</th>
                <th style="width:42px">Status</th>
            </tr></thead>
            <tbody>
            @foreach($events as $i => $e)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $e->nama_event ?? '-' }}</td>
                    <td>{{ $e->tingkat ?? '-' }}</td>
                    <td>{{ $lokasi($e) }}</td>
                    @unless($isRelawan)<td>{{ $e->user?->name ?? '-' }}</td>@endunless
                    <td>{{ $e->tanggal_mulai ? \Carbon\Carbon::parse($e->tanggal_mulai)->format('d/m/y') : '-' }}</td>
                    <td><span class="{{ $pill($e->status_validasi) }}">{{ $pillText($e->status_validasi) }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ===== PARTISIPASI ===== --}}
<div class="section">
    <div class="section-head">Partisipasi Kegiatan <span class="count">{{ $partisipasi->count() }} entri</span></div>
    @if($partisipasi->isEmpty())
        <div class="empty">Tidak ada data partisipasi.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Lokasi Observasi</th>
                <th>Wilayah</th>
                <th class="num" style="width:50px">Est. Org</th>
                @unless($isRelawan)<th style="width:70px">Relawan</th>@endunless
                <th style="width:42px">Status</th>
                <th style="width:52px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($partisipasi as $i => $p)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $p->lokasi_observasi ?? '-' }}</td>
                    <td>{{ $lokasi($p) }}</td>
                    <td class="num">{{ number_format($p->estimasi_jumlah_orang ?? 0) }}</td>
                    @unless($isRelawan)<td>{{ $p->user?->name ?? '-' }}</td>@endunless
                    <td><span class="{{ $pill($p->status_validasi) }}">{{ $pillText($p->status_validasi) }}</span></td>
                    <td>{{ $p->tanggal_observasi ? \Carbon\Carbon::parse($p->tanggal_observasi)->format('d/m/y') : $p->created_at->format('d/m/y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

@include('pdf.partials.footer-script')

</body>
</html>
