{{--
    Kop surat PDF: logo Dataraga (kiri) + jenis dokumen & tanggal (kanan).
    Param opsional: $docType (string). Logo di-embed base64 supaya tidak
    bergantung pada symlink storage / chroot dompdf.
--}}
@php
    $__logoPath = public_path('img/dataraga-logo.png');
    $__logo = is_file($__logoPath)
        ? 'data:image/png;base64,'.base64_encode(file_get_contents($__logoPath))
        : null;
@endphp
<div class="letterhead">
    <div class="lh-brand">
        @if($__logo)
            <img src="{{ $__logo }}" alt="Dataraga" class="lh-logo">
        @else
            <span class="brand-mark">DATARAGA</span>
        @endif
    </div>
    <div class="lh-meta">
        <div class="m-label">{{ $docType ?? 'Laporan Resmi' }}</div>
        <div class="m-value">{{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
    </div>
</div>
