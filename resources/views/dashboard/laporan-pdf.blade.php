<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1e293b; }

/* HEADER */
.header { background: #1d4ed8; color: #fff; padding: 18px 20px 14px; margin-bottom: 16px; }
.header-row { display: table; width: 100%; }
.header-left  { display: table-cell; vertical-align: middle; }
.header-right { display: table-cell; vertical-align: middle; text-align: right; width: 160px; }
.app-name  { font-size: 22px; font-weight: bold; letter-spacing: 1px; color: #fff; }
.app-sub   { font-size: 10px; color: #bfdbfe; margin-top: 2px; }
.doc-type  { font-size: 9px; color: #93c5fd; text-transform: uppercase; letter-spacing: 0.5px; }
.doc-date  { font-size: 10px; color: #dbeafe; margin-top: 2px; }
.scope-badge { display: inline-block; margin-top: 6px; padding: 2px 10px; background: rgba(255,255,255,0.2); border-radius: 10px; font-size: 9px; font-weight: bold; color: #fff; }

/* RINGKASAN */
.summary-grid { display: table; width: 100%; margin-bottom: 16px; border-collapse: separate; border-spacing: 6px; }
.summary-cell { display: table-cell; width: 25%; }
.summary-box  { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; background: #f8fafc; }
.summary-num  { font-size: 22px; font-weight: 900; color: #1d4ed8; line-height: 1; }
.summary-valid { font-size: 9px; color: #16a34a; font-weight: bold; margin-top: 2px; }
.summary-label { font-size: 9px; color: #64748b; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.3px; }

/* SECTION */
.section { margin-bottom: 18px; page-break-inside: avoid; }
.section-title { font-size: 11px; font-weight: bold; color: #1e3a8a; background: #eff6ff; border-left: 4px solid #1d4ed8; padding: 6px 10px; margin-bottom: 0; }
.section-sub   { font-size: 9px; color: #3b82f6; padding: 3px 10px 5px; background: #f0f9ff; margin-bottom: 0; }

/* TABLE */
table { width: 100%; border-collapse: collapse; font-size: 9px; }
thead th { background: #1d4ed8; color: #fff; padding: 5px 6px; text-align: left; font-weight: bold; font-size: 8px; text-transform: uppercase; letter-spacing: 0.2px; }
tbody td { padding: 5px 6px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
tbody tr:nth-child(even) td { background: #f8fafc; }
.no-col { width: 22px; text-align: center; color: #94a3b8; }
.badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; }
.b-valid    { background: #dcfce7; color: #166534; }
.b-pending  { background: #fef9c3; color: #854d0e; }
.b-rejected { background: #fee2e2; color: #991b1b; }
.empty { text-align: center; color: #94a3b8; padding: 14px; font-style: italic; }

/* INFO USER */
.user-info { display: table; width: 100%; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; }
.ui-cell { display: table-cell; vertical-align: middle; }
.ui-right { text-align: right; }
.ui-name { font-size: 13px; font-weight: bold; color: #0c4a6e; }
.ui-email { font-size: 9px; color: #0284c7; margin-top: 1px; }
.ui-wilayah { font-size: 9px; color: #0369a1; margin-top: 2px; }
.ui-role { display: inline-block; padding: 2px 8px; background: #0284c7; color: #fff; border-radius: 8px; font-size: 8px; font-weight: bold; }

/* FOOTER */
.footer { border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 10px; display: table; width: 100%; }
.fl { display: table-cell; font-size: 8px; color: #94a3b8; }
.fr { display: table-cell; text-align: right; font-size: 8px; color: #94a3b8; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <div class="header-row">
        <div class="header-left">
            <div class="app-name">DATARAGA</div>
            <div class="app-sub">Sistem Informasi Olahraga Daerah</div>
            <div class="scope-badge">{{ $isRelawan ? 'Laporan Data Pribadi' : 'Laporan Keseluruhan Data' }}</div>
        </div>
        <div class="header-right">
            <div class="doc-type">Laporan Resmi</div>
            <div class="doc-date">{{ now()->isoFormat('D MMMM YYYY') }}</div>
        </div>
    </div>
</div>

{{-- INFO RELAWAN (jika bukan admin) --}}
@if($isRelawan)
<div class="user-info">
    <div class="ui-cell">
        <div class="ui-name">{{ $user->name }}</div>
        <div class="ui-email">{{ $user->email }}</div>
        @if($user->kabupaten)
        <div class="ui-wilayah">{{ collect([$user->desa, $user->kecamatan, $user->kabupaten])->filter()->implode(', ') }}</div>
        @endif
    </div>
    <div class="ui-cell ui-right">
        <span class="ui-role">Relawan</span>
    </div>
</div>
@endif

{{-- RINGKASAN --}}
<div class="summary-grid">
    <div class="summary-cell">
        <div class="summary-box">
            <div class="summary-num">{{ $stats['prasarana'] }}</div>
            <div class="summary-valid">✓ {{ $stats['prasarana_validated'] }} valid</div>
            <div class="summary-label">Prasarana</div>
        </div>
    </div>
    <div class="summary-cell">
        <div class="summary-box">
            <div class="summary-num">{{ $stats['events'] }}</div>
            <div class="summary-valid">✓ {{ $stats['events_validated'] }} valid</div>
            <div class="summary-label">Event</div>
        </div>
    </div>
    <div class="summary-cell">
        <div class="summary-box">
            <div class="summary-num">{{ $stats['clubs'] }}</div>
            <div class="summary-valid">✓ {{ $stats['clubs_validated'] }} valid</div>
            <div class="summary-label">Klub</div>
        </div>
    </div>
    <div class="summary-cell">
        <div class="summary-box">
            <div class="summary-num">{{ $stats['partisipasi'] }}</div>
            <div class="summary-valid">✓ {{ $stats['partisipasi_validated'] }} valid</div>
            <div class="summary-label">Partisipasi</div>
        </div>
    </div>
</div>

{{-- PRASARANA --}}
@if($prasarana->count())
<div class="section">
    <div class="section-title">Prasarana Olahraga</div>
    <div class="section-sub">{{ $prasarana->count() }} entri &mdash; {{ $prasarana->where('status_validasi','validated')->count() }} tervalidasi</div>
    <table>
        <thead><tr>
            <th class="no-col">#</th>
            <th>Nama Fasilitas</th>
            <th style="width:90px">Kategori</th>
            <th>Lokasi</th>
            @if(!$isRelawan)<th style="width:70px">Relawan</th>@endif
            <th style="width:55px">Status</th>
            <th style="width:55px">Tgl Input</th>
        </tr></thead>
        <tbody>
        @foreach($prasarana as $i => $p)
        <tr>
            <td class="no-col">{{ $i+1 }}</td>
            <td>{{ $p->nama_fasilitas ?? $p->nama ?? '-' }}</td>
            <td>{{ $p->kategori_olahraga_label ?? '-' }}</td>
            <td>{{ collect([$p->desa, $p->kecamatan, $p->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
            @if(!$isRelawan)<td>{{ $p->user?->name ?? '-' }}</td>@endif
            <td><span class="badge {{ $p->status_validasi==='validated'?'b-valid':($p->status_validasi==='rejected'?'b-rejected':'b-pending') }}">{{ $p->status_validasi==='validated'?'Valid':($p->status_validasi==='rejected'?'Tolak':'Pending') }}</span></td>
            <td>{{ $p->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- EVENTS --}}
@if($events->count())
<div class="section">
    <div class="section-title">Event Olahraga</div>
    <div class="section-sub">{{ $events->count() }} entri &mdash; {{ $events->where('status_validasi','validated')->count() }} tervalidasi</div>
    <table>
        <thead><tr>
            <th class="no-col">#</th>
            <th>Nama Event</th>
            <th style="width:60px">Tanggal</th>
            <th>Lokasi</th>
            @if(!$isRelawan)<th style="width:70px">Relawan</th>@endif
            <th style="width:55px">Status</th>
            <th style="width:55px">Tgl Input</th>
        </tr></thead>
        <tbody>
        @foreach($events as $i => $e)
        <tr>
            <td class="no-col">{{ $i+1 }}</td>
            <td>{{ $e->nama_event ?? $e->nama ?? '-' }}</td>
            <td>{{ $e->tanggal_mulai ? \Carbon\Carbon::parse($e->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
            <td>{{ $e->lokasi ?? collect([$e->desa, $e->kecamatan, $e->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
            @if(!$isRelawan)<td>{{ $e->user?->name ?? '-' }}</td>@endif
            <td><span class="badge {{ $e->status_validasi==='validated'?'b-valid':($e->status_validasi==='rejected'?'b-rejected':'b-pending') }}">{{ $e->status_validasi==='validated'?'Valid':($e->status_validasi==='rejected'?'Tolak':'Pending') }}</span></td>
            <td>{{ $e->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- CLUBS --}}
@if($clubs->count())
<div class="section">
    <div class="section-title">Klub Olahraga</div>
    <div class="section-sub">{{ $clubs->count() }} entri &mdash; {{ $clubs->where('status_validasi','validated')->count() }} tervalidasi</div>
    <table>
        <thead><tr>
            <th class="no-col">#</th>
            <th>Nama Klub</th>
            <th style="width:80px">Cabang</th>
            <th>Lokasi</th>
            @if(!$isRelawan)<th style="width:70px">Relawan</th>@endif
            <th style="width:55px">Status</th>
            <th style="width:55px">Tgl Input</th>
        </tr></thead>
        <tbody>
        @foreach($clubs as $i => $c)
        <tr>
            <td class="no-col">{{ $i+1 }}</td>
            <td>{{ $c->nama_club ?? $c->nama ?? '-' }}</td>
            <td>{{ $c->cabang_olahraga ?? '-' }}</td>
            <td>{{ collect([$c->desa, $c->kecamatan, $c->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
            @if(!$isRelawan)<td>{{ $c->user?->name ?? '-' }}</td>@endif
            <td><span class="badge {{ $c->status_validasi==='validated'?'b-valid':($c->status_validasi==='rejected'?'b-rejected':'b-pending') }}">{{ $c->status_validasi==='validated'?'Valid':($c->status_validasi==='rejected'?'Tolak':'Pending') }}</span></td>
            <td>{{ $c->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- PARTISIPASI --}}
@if($partisipasi->count())
<div class="section">
    <div class="section-title">Partisipasi Kegiatan</div>
    <div class="section-sub">{{ $partisipasi->count() }} entri &mdash; {{ $partisipasi->where('status_validasi','validated')->count() }} tervalidasi</div>
    <table>
        <thead><tr>
            <th class="no-col">#</th>
            <th>Nama Kegiatan / Lokasi</th>
            <th style="width:60px">Tanggal</th>
            <th style="width:55px">Est. Orang</th>
            @if(!$isRelawan)<th style="width:70px">Relawan</th>@endif
            <th style="width:55px">Status</th>
            <th style="width:55px">Tgl Input</th>
        </tr></thead>
        <tbody>
        @foreach($partisipasi as $i => $p)
        <tr>
            <td class="no-col">{{ $i+1 }}</td>
            <td>{{ $p->nama_kegiatan ?? $p->lokasi_observasi ?? '-' }}</td>
            <td>{{ $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') : ($p->tanggal_observasi ? \Carbon\Carbon::parse($p->tanggal_observasi)->format('d/m/Y') : '-') }}</td>
            <td style="text-align:center">{{ number_format($p->estimasi_jumlah_orang ?? 0) }}</td>
            @if(!$isRelawan)<td>{{ $p->user?->name ?? '-' }}</td>@endif
            <td><span class="badge {{ $p->status_validasi==='validated'?'b-valid':($p->status_validasi==='rejected'?'b-rejected':'b-pending') }}">{{ $p->status_validasi==='validated'?'Valid':($p->status_validasi==='rejected'?'Tolak':'Pending') }}</span></td>
            <td>{{ $p->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- FOOTER --}}
<div class="footer">
    <div class="fl">Dataraga &copy; {{ now()->year }} &mdash; Dicetak oleh {{ $user->name }}</div>
    <div class="fr">{{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</div>
</div>

</body>
</html>
