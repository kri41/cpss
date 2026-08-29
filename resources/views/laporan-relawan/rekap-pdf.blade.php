<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @include('pdf.partials.styles')

    table.data td.rank { text-align: center; font-weight: bold; color: #1e3a8a; width: 26px; }
    table.data tr.top3 td { background: #eff6ff; }
</style>
</head>
<body>

@php $pdfFooterLeft = 'Rekapitulasi Kinerja Relawan'; @endphp

{{-- ===== LETTERHEAD ===== --}}
@include('pdf.partials.letterhead', ['docType' => 'Rekap Peringkat Relawan'])

{{-- ===== JUDUL ===== --}}
<div class="report-band">
    <h1>Rekapitulasi Kinerja Relawan</h1>
    <p>Peringkat seluruh relawan berdasarkan poin dan jumlah kontribusi data keolahragaan.</p>
    <span class="band-badge">{{ $agg['total_relawan'] }} Relawan &middot; Per {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</span>
</div>

{{-- ===== KPI ===== --}}
<div class="kpi-row">
    <div class="kpi accent">
        <div class="kpi-num">{{ number_format($agg['total_relawan']) }}</div>
        <div class="kpi-label">Total Relawan</div>
        <div class="kpi-sub">{{ $agg['aktif'] }} aktif</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ number_format($agg['total_poin']) }}</div>
        <div class="kpi-label">Poin Terkumpul</div>
        <div class="kpi-sub">&nbsp;</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ number_format($agg['kontribusi']) }}</div>
        <div class="kpi-label">Total Kontribusi</div>
        <div class="kpi-sub">&nbsp;</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ number_format($agg['total_relawan'] ? round($agg['total_poin'] / $agg['total_relawan']) : 0) }}</div>
        <div class="kpi-label">Rata-rata Poin</div>
        <div class="kpi-sub">per relawan</div>
    </div>
</div>

{{-- ===== TABEL PERINGKAT ===== --}}
<div class="section">
    <div class="section-head">Peringkat Relawan</div>
    <table class="data">
        <thead><tr>
            <th class="col-no">#</th>
            <th>Nama Relawan</th>
            <th>Wilayah</th>
            <th class="num" style="width:38px">Pras</th>
            <th class="num" style="width:38px">Klub</th>
            <th class="num" style="width:40px">Event</th>
            <th class="num" style="width:44px">Partsp</th>
            <th class="num" style="width:40px">Kmpg</th>
            <th class="num" style="width:36px">Lenc</th>
            <th class="num" style="width:46px">Poin</th>
        </tr></thead>
        <tbody>
        @foreach($relawan as $i => $r)
            <tr class="{{ $i < 3 ? 'top3' : '' }}">
                <td class="rank">{{ $i + 1 }}</td>
                <td>{{ $r->name }}<br><span class="muted" style="font-size:7px">{{ $r->email }}</span></td>
                <td>{{ collect([$r->kecamatan, $r->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                <td class="num">{{ $r->prasarana_count }}</td>
                <td class="num">{{ $r->clubs_count }}</td>
                <td class="num">{{ $r->events_count }}</td>
                <td class="num">{{ $r->partisipasi_count }}</td>
                <td class="num">{{ $r->kampung_olahraga_count }}</td>
                <td class="num">{{ $r->badges_count }}</td>
                <td class="num"><strong>{{ number_format($r->total_poin ?? 0) }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="note">Pras = Prasarana &middot; Partsp = Partisipasi &middot; Kmpg = Kampung Olahraga &middot; Lenc = Lencana. Baris tersorot = 3 besar.</div>
</div>

@include('pdf.partials.footer-script')

</body>
</html>
