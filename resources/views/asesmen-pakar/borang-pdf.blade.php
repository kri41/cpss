<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 1.7cm 1.6cm; }
    * { box-sizing: border-box; }
    body { font-family: "DejaVu Serif", "Times New Roman", serif; font-size: 10.5px; color: #000; line-height: 1.5; }
    h1 { font-size: 13px; text-align: center; margin: 0; letter-spacing: 0.5px; }
    .sub { text-align: center; font-size: 9px; margin: 2px 0 10px; }
    .kode { text-align: right; font-size: 8.5px; margin-bottom: 4px; }
    hr { border: none; border-top: 1.5px solid #000; margin: 6px 0 12px; }

    table { width: 100%; border-collapse: collapse; }
    .sec { font-weight: bold; font-size: 10.5px; margin: 12px 0 5px; text-transform: uppercase; }

    /* identitas */
    .id td { padding: 2px 4px; vertical-align: top; }
    .id .lbl { width: 130px; }
    .id .sep { width: 10px; }

    .isian { border-bottom: 1px dotted #000; display: inline-block; min-width: 60px; }
    .isian-lg { border-bottom: 1px dotted #000; display: block; height: 15px; margin-top: 3px; }

    /* penilaian */
    .grid { border: 1px solid #000; }
    .grid th, .grid td { border: 1px solid #000; padding: 4px 5px; font-size: 9.5px; vertical-align: top; }
    .grid th { background: #fff; font-weight: bold; text-align: left; }
    .skala { text-align: center; letter-spacing: 3px; white-space: nowrap; font-size: 11px; }
    .ket { height: 18px; }

    .legend { font-size: 8.5px; margin-top: 3px; }
    .kotak { display: inline-block; width: 9px; height: 9px; border: 1px solid #000; vertical-align: middle; margin-right: 3px; }

    .kategori { width: 100%; }
    .kategori td { width: 25%; font-size: 9.5px; padding: 3px 2px; }
    .ttd { margin-top: 18px; width: 100%; }
    .ttd td { width: 50%; text-align: center; vertical-align: top; font-size: 9.5px; }
    .lembar { page-break-after: always; }
    .lembar:last-child { page-break-after: auto; }
</style>
</head>
<body>

@foreach($rows as $p)
<div class="lembar">
    <div class="kode">Borang: AUD-PRAS / Hal. {{ $loop->iteration }} dari {{ $rows->count() }}</div>
    <h1>LEMBAR AUDIT PRASARANA OLAHRAGA</h1>
    <div class="sub">Diisi oleh asesor di lokasi. Lembar ini kemudian ditranskripsi ke sistem oleh admin.</div>
    <hr>

    <div class="sec">A. Identitas Fasilitas <span style="font-weight:normal;font-size:8.5px">(sudah terisi)</span></div>
    <table class="id">
        <tr><td class="lbl">Nomor / Kode</td><td class="sep">:</td><td>#{{ $p->id }}</td></tr>
        <tr><td class="lbl">Nama Fasilitas</td><td class="sep">:</td><td>{{ $p->nama_fasilitas }}</td></tr>
        <tr><td class="lbl">Kategori Olahraga</td><td class="sep">:</td><td>{{ $p->jenisOlahraga->pluck('nama')->implode(', ') ?: '—' }}</td></tr>
        <tr><td class="lbl">Alamat</td><td class="sep">:</td><td>{{ $p->alamat ?: '—' }}</td></tr>
        <tr><td class="lbl">Desa / Kec. / Kab.</td><td class="sep">:</td><td>{{ collect([$p->desa, $p->kecamatan, $p->kabupaten])->filter()->implode(' / ') ?: '—' }}</td></tr>
        <tr><td class="lbl">Koordinat</td><td class="sep">:</td><td>{{ ($p->latitude && $p->longitude) ? $p->latitude.', '.$p->longitude : '—' }}</td></tr>
    </table>

    <div class="sec">B. Identitas Asesor &amp; Waktu Kerja <span style="font-weight:normal;font-size:8.5px">(diisi tangan)</span></div>
    <table class="id">
        <tr><td class="lbl">Nama Asesor</td><td class="sep">:</td><td><span class="isian" style="min-width:280px">&nbsp;</span></td></tr>
        <tr><td class="lbl">Instansi / Jabatan</td><td class="sep">:</td><td><span class="isian" style="min-width:280px">&nbsp;</span></td></tr>
        <tr><td class="lbl">Tanggal Asesmen</td><td class="sep">:</td><td><span class="isian" style="min-width:140px">&nbsp;</span> &nbsp;&nbsp; Cuaca: <span class="isian" style="min-width:110px">&nbsp;</span></td></tr>
        <tr><td class="lbl">Jam mulai menilai</td><td class="sep">:</td><td>pukul <span class="isian" style="min-width:70px">&nbsp;</span> WIB</td></tr>
        <tr><td class="lbl">Jam selesai menilai</td><td class="sep">:</td><td>pukul <span class="isian" style="min-width:70px">&nbsp;</span> WIB</td></tr>
        <tr><td class="lbl">Lama menyusun laporan</td><td class="sep">:</td><td><span class="isian" style="min-width:70px">&nbsp;</span> menit &nbsp;<span style="font-size:8.5px">(waktu menuliskan/merapikan lembar ini setelah dari lapangan)</span></td></tr>
    </table>

    <div class="sec">C. Penilaian Kondisi Komponen (lingkari skala)</div>
    <table class="grid">
        <tr><th style="width:26%">Komponen</th><th style="width:26%">Skala Kondisi</th><th>Keterangan / Temuan</th></tr>
        @foreach($kondisi as $key => $label)
            <tr>
                <td>{{ $label }}</td>
                <td class="skala">1&nbsp;&nbsp;2&nbsp;&nbsp;3&nbsp;&nbsp;4&nbsp;&nbsp;5</td>
                <td class="ket">&nbsp;</td>
            </tr>
        @endforeach
    </table>
    <div class="legend">Skala: 1 = {{ $ratingLabels[1] }} &nbsp;|&nbsp; 2 = {{ $ratingLabels[2] }} &nbsp;|&nbsp; 3 = {{ $ratingLabels[3] }} &nbsp;|&nbsp; 4 = {{ $ratingLabels[4] }} &nbsp;|&nbsp; 5 = {{ $ratingLabels[5] }}. &nbsp; Kosongkan bila komponen tidak ada.</div>

    <div class="sec">D. Aksesibilitas &amp; Kelengkapan (beri tanda &times;)</div>
    <table class="grid">
        <tr><th style="width:32%">Aspek</th><th style="width:34%">Ketersediaan</th><th>Catatan</th></tr>
        @foreach($akses as $key => $label)
            <tr>
                <td>{{ $label }}</td>
                <td><span class="kotak"></span> Ya &nbsp;&nbsp; <span class="kotak"></span> Tidak &nbsp;&nbsp; <span class="kotak"></span> Tidak dinilai</td>
                <td class="ket">&nbsp;</td>
            </tr>
        @endforeach
    </table>

    <div class="sec">E. Kategori Olahraga menurut Asesor (beri tanda &times;)</div>
    <table class="kategori">
        @foreach($jenisOlahraga->chunk(4) as $baris)
            <tr>
                @foreach($baris as $j)
                    <td><span class="kotak"></span> {{ $j->nama }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>

    <div class="sec">F. Catatan Umum &amp; Rekomendasi</div>
    <span class="isian-lg">&nbsp;</span>
    <span class="isian-lg">&nbsp;</span>
    <span class="isian-lg">&nbsp;</span>
    <span class="isian-lg">&nbsp;</span>

    <table class="ttd">
        <tr>
            <td>Diperiksa,<br><br><br><br>( ______________________ )</td>
            <td>{{ $p->kabupaten ?: '..................' }}, ____ / ____ / 20____<br>Asesor,<br><br><br><br>( ______________________ )</td>
        </tr>
    </table>
</div>
@endforeach

</body>
</html>
