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
    $pdfFooterLeft = 'Laporan Kinerja Relawan — ' . $relawan->name;

    $idDate = fn ($d, $fmt = 'D MMMM YYYY') => $d ? \Carbon\Carbon::parse($d)->locale('id')->isoFormat($fmt) : '-';

    $kategoriMeta = [
        'prasarana'        => 'Prasarana',
        'club'             => 'Klub/Komunitas',
        'event'            => 'Event',
        'partisipasi'      => 'Partisipasi',
        'kampung_olahraga' => 'Kampung Olahraga',
    ];
    $poinMax = max(1, $poinPerKategori->max() ?? 1);
    $wilayah = collect([$relawan->desa, $relawan->kecamatan, $relawan->kabupaten])->filter()->implode(', ') ?: '-';

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
@endphp

{{-- ===== LETTERHEAD ===== --}}
<div class="letterhead">
    <div class="lh-brand">
        <span class="brand-mark">DATARAGA</span>
        <div class="brand-tag">Cloud Participatory Sport Sensing &mdash; Kamu Gerak, Indonesia Tahu</div>
    </div>
    <div class="lh-meta">
        <div class="m-label">Laporan Kinerja Relawan</div>
        <div class="m-value">{{ $idDate(now()) }}</div>
    </div>
</div>

{{-- ===== JUDUL ===== --}}
<div class="report-band">
    <h1>Laporan Kinerja Relawan</h1>
    <p>Rekapitulasi poin, lencana, dan seluruh kontribusi data keolahragaan.</p>
    <span class="band-badge">Per {{ $idDate(now()) }}</span>
</div>

{{-- ===== IDENTITAS ===== --}}
<div class="identity">
    <div class="id-main">
        <div class="id-name">{{ $relawan->name }}</div>
        <div class="id-sub">{{ $relawan->email }}</div>
        <div class="id-sub">{{ $wilayah }}</div>
        <div style="margin-top:5px"><span class="id-chip">RELAWAN</span></div>
    </div>
    <div class="id-side">
        <div class="id-rank">#{{ $rank }}</div>
        <div class="id-rank-label">dari {{ $totalRelawan }} relawan</div>
    </div>
</div>

{{-- ===== KPI ===== --}}
<div class="kpi-row">
    <div class="kpi accent">
        <div class="kpi-num">{{ number_format($relawan->total_poin ?? 0) }}</div>
        <div class="kpi-label">Total Poin</div>
        <div class="kpi-sub">+{{ number_format($poinBulanIni) }} bulan ini</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ $totalKontribusi }}</div>
        <div class="kpi-label">Total Kontribusi</div>
        <div class="kpi-sub">&nbsp;</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ $totalValid }}</div>
        <div class="kpi-label">Tervalidasi</div>
        <div class="kpi-sub">{{ $totalKontribusi ? round($totalValid / $totalKontribusi * 100) : 0 }}% dari total</div>
    </div>
    <div class="kpi">
        <div class="kpi-num">{{ $badges->count() }}</div>
        <div class="kpi-label">Lencana</div>
        <div class="kpi-sub">&nbsp;</div>
    </div>
</div>

<div class="info-line">
    Bergabung <strong>{{ $idDate($relawan->created_at) }}</strong>
    &middot; Kontribusi pertama
    <strong>{{ $idDate($kontribusiPertama, 'D MMM YYYY') }}</strong>
    &middot; Kontribusi terakhir
    <strong>{{ $idDate($kontribusiTerakhir, 'D MMM YYYY') }}</strong>
</div>

{{-- ===== POIN PER KATEGORI ===== --}}
<div class="section">
    <div class="section-head">Perolehan Poin per Kategori</div>
    @if($poinPerKategori->sum() === 0)
        <div class="empty">Belum ada poin tervalidasi.</div>
    @else
        <div class="catbar">
            @foreach($kategoriMeta as $key => $label)
                @php $val = $poinPerKategori[$key] ?? 0; @endphp
                <div class="catbar-row">
                    <div class="catbar-name">{{ $label }}</div>
                    <div class="catbar-track">
                        <div class="catbar-fill" style="width: {{ max(2, round($val / $poinMax * 100)) }}%"></div>
                    </div>
                    <div class="catbar-val">{{ number_format($val) }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ===== PRASARANA ===== --}}
<div class="section">
    <div class="section-head">Prasarana Olahraga <span class="count">{{ $stats['prasarana']['total'] }} entri &middot; {{ $stats['prasarana']['valid'] }} valid</span></div>
    @if($prasarana->isEmpty())
        <div class="empty">Belum ada prasarana yang dilaporkan.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Fasilitas</th>
                <th style="width:78px">Kategori</th>
                <th>Lokasi</th>
                <th style="width:42px">Status</th>
                <th style="width:50px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($prasarana as $i => $p)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $p->nama_fasilitas }}</td>
                    <td>{{ $p->kategori_olahraga_label }}</td>
                    <td>{{ collect([$p->desa, $p->kecamatan, $p->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
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
    <div class="section-head">Klub / Komunitas <span class="count">{{ $stats['clubs']['total'] }} entri &middot; {{ $stats['clubs']['valid'] }} valid</span></div>
    @if($clubs->isEmpty())
        <div class="empty">Belum ada klub/komunitas yang didaftarkan.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Klub</th>
                <th style="width:80px">Cabang</th>
                <th>Lokasi</th>
                <th style="width:42px">Status</th>
                <th style="width:50px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($clubs as $i => $c)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $c->nama_club }}</td>
                    <td>{{ $c->jenisOlahraga?->nama ?? '-' }}</td>
                    <td>{{ collect([$c->desa, $c->kecamatan, $c->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
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
    <div class="section-head">Event Olahraga <span class="count">{{ $stats['events']['total'] }} entri &middot; {{ $stats['events']['valid'] }} valid</span></div>
    @if($events->isEmpty())
        <div class="empty">Belum ada event yang dilaporkan.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Event</th>
                <th style="width:62px">Tingkat</th>
                <th>Lokasi</th>
                <th style="width:50px">Mulai</th>
                <th style="width:42px">Status</th>
            </tr></thead>
            <tbody>
            @foreach($events as $i => $e)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $e->nama_event }}</td>
                    <td>{{ $e->tingkat ?? '-' }}</td>
                    <td>{{ collect([$e->desa, $e->kecamatan, $e->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
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
    <div class="section-head">Partisipasi Kegiatan <span class="count">{{ $stats['partisipasi']['total'] }} entri &middot; {{ $stats['partisipasi']['valid'] }} valid</span></div>
    @if($partisipasi->isEmpty())
        <div class="empty">Belum ada catatan partisipasi.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Lokasi Observasi</th>
                <th>Wilayah</th>
                <th class="num" style="width:46px">Est. Org</th>
                <th class="num" style="width:42px">Hadir</th>
                <th style="width:42px">Status</th>
                <th style="width:50px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($partisipasi as $i => $p)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $p->lokasi_observasi ?? '-' }}</td>
                    <td>{{ collect([$p->desa, $p->kecamatan, $p->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                    <td class="num">{{ number_format($p->estimasi_jumlah_orang ?? 0) }}</td>
                    <td class="num">{{ $p->kehadiran_count }}</td>
                    <td><span class="{{ $pill($p->status_validasi) }}">{{ $pillText($p->status_validasi) }}</span></td>
                    <td>{{ $p->tanggal_observasi ? \Carbon\Carbon::parse($p->tanggal_observasi)->format('d/m/y') : $p->created_at->format('d/m/y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ===== KAMPUNG OLAHRAGA ===== --}}
<div class="section">
    <div class="section-head">Kampung Olahraga <span class="count">{{ $stats['kampung']['total'] }} entri &middot; {{ $stats['kampung']['valid'] }} valid</span></div>
    @if($kampung->isEmpty())
        <div class="empty">Belum ada Kampung Olahraga yang didaftarkan.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th>Nama Kampung</th>
                <th>Lokasi</th>
                <th class="num" style="width:50px">Check-in</th>
                <th style="width:42px">Status</th>
                <th style="width:50px">Tanggal</th>
            </tr></thead>
            <tbody>
            @foreach($kampung as $i => $k)
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $k->nama_kampung }}</td>
                    <td>{{ collect([$k->desa, $k->kecamatan, $k->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
                    <td class="num">{{ $k->checkins_count }}</td>
                    <td><span class="{{ $pill($k->status_validasi) }}">{{ $pillText($k->status_validasi) }}</span></td>
                    <td>{{ $k->created_at->format('d/m/y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ===== RIWAYAT POIN ===== --}}
<div class="section">
    <div class="section-head">Riwayat Transaksi Poin <span class="count">{{ $transaksi->count() }} transaksi &middot; {{ number_format($poinValid) }} poin valid</span></div>
    @if($transaksi->isEmpty())
        <div class="empty">Belum ada transaksi poin.</div>
    @else
        <table class="data">
            <thead><tr>
                <th class="col-no">#</th>
                <th style="width:50px">Tanggal</th>
                <th style="width:96px">Aktivitas</th>
                <th>Entitas</th>
                <th class="num" style="width:34px">Poin</th>
                <th style="width:48px">Status</th>
            </tr></thead>
            <tbody>
            @foreach($transaksi as $i => $tx)
                @php $target = $targetNama[$tx->id] ?? null; @endphp
                <tr>
                    <td class="col-no">{{ $i + 1 }}</td>
                    <td>{{ $tx->created_at->format('d/m/y') }}</td>
                    <td>{{ $target['label'] ?? ucfirst(str_replace('_', ' ', $tx->related_type)) }} <span class="muted">({{ $tx->jenis_aksi }})</span></td>
                    <td>{{ $target['nama'] ?? '-' }}</td>
                    <td class="num">+{{ $tx->poin }}</td>
                    <td><span class="{{ $tx->status === 'valid' ? 'pill pill-green' : 'pill pill-gray' }}">{{ $tx->status === 'valid' ? 'Valid' : 'Batal' }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ===== LENCANA ===== --}}
<div class="section">
    <div class="section-head">Lencana Pencapaian <span class="count">{{ $badges->count() }} diraih</span></div>
    @if($badges->isEmpty())
        <div class="empty">Relawan ini belum meraih lencana.</div>
    @else
        <div class="badge-grid">
            @foreach($badges as $b)
                <div class="badge-item">
                    <div class="b-name">{{ $b->nama }}</div>
                    <div class="b-state">
                        {{ $b->pivot?->earned_at ? $idDate($b->pivot->earned_at, 'D MMM YYYY') : 'Diraih' }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="note">Total {{ \App\Models\Badge::count() }} lencana tersedia dalam sistem.</div>
    @endif
</div>

@include('pdf.partials.footer-script')

</body>
</html>
