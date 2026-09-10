<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @include('pdf.partials.styles')
    table.data td { font-size: 8px; }
    .kotak { display: inline-block; width: 9px; height: 9px; border: 1px solid #94a3b8; vertical-align: middle; }
</style>
</head>
<body>

@php $pdfFooterLeft = 'Daftar Lokasi Sasaran Asesor (blind — tanpa data kondisi)'; @endphp

@include('pdf.partials.letterhead', ['docType' => 'Lembar Lokasi Sasaran Asesor'])

<div class="report-band">
    <h1>Daftar Lokasi Sasaran &mdash; Asesmen Pakar</h1>
    <p>Dokumen kerja lapangan untuk asesor. Tidak memuat data kondisi/kelengkapan (asesmen dilakukan blind).</p>
    <span class="band-badge">
        {{ $filter['kabupaten'] ?: 'Semua Kab/Kota' }}
        @if($filter['kecamatan']) &middot; {{ $filter['kecamatan'] }} @endif
        &middot; {{ $rows->count() }} lokasi
    </span>
</div>

<div class="info-line">
    Asesor: __________________________  &nbsp;&nbsp; Tanggal: ______________  &nbsp;&nbsp;
    Kolom "Sudah dinilai" dicentang setelah lembar audit terpisah diisi untuk lokasi tsb.
</div>

<table class="data">
    <thead><tr>
        <th class="col-no">#</th>
        <th style="width:32px">ID</th>
        <th>Nama Fasilitas</th>
        <th style="width:80px">Kategori</th>
        <th style="width:120px">Koordinat</th>
        <th>Wilayah</th>
        <th style="width:52px">Dinilai</th>
    </tr></thead>
    <tbody>
    @foreach($rows as $i => $p)
        <tr>
            <td class="col-no">{{ $i + 1 }}</td>
            <td>#{{ $p->id }}</td>
            <td>{{ $p->nama_fasilitas }}</td>
            <td>{{ $p->jenisOlahraga->pluck('nama')->implode(', ') ?: '-' }}</td>
            <td>{{ ($p->latitude && $p->longitude) ? $p->latitude.', '.$p->longitude : '-' }}</td>
            <td>{{ collect([$p->desa, $p->kecamatan, $p->kabupaten])->filter()->implode(', ') ?: '-' }}</td>
            <td style="text-align:center"><span class="kotak"></span></td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="note">
    Catat pada lembar audit terpisah: kondisi tiap komponen (skala 1&ndash;5), aksesibilitas &amp; kelengkapan (ya/tidak),
    kategori olahraga, serta <strong>waktu mulai</strong> &amp; <strong>waktu selesai</strong> penilaian dan
    <strong>lama menyusun laporan</strong>. Data itu lalu ditranskripsi ke sistem oleh admin.
</div>

@include('pdf.partials.footer-script')

</body>
</html>
