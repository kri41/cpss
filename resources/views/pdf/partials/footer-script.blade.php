{{--
    Footer berulang + nomor halaman untuk semua PDF.
    Butuh opsi dompdf: isPhpEnabled = true (di-set di controller).
    Opsional: definisikan $pdfFooterLeft sebelum meng-include partial ini.
--}}
@php
    $__footerLeft = ($pdfFooterLeft ?? 'Dokumen dihasilkan otomatis oleh sistem Dataraga')
        . ' | ' . now()->timezone('Asia/Jakarta')->locale('id')->isoFormat('D MMM YYYY, HH:mm') . ' WIB';
    // var_export -> literal PHP string ber-quote tunggal (aman, tanpa interpolasi)
    $__footerLeftPhp = var_export($__footerLeft, true);
@endphp
<script type="text/php">
if (isset($pdf)) {
    $w = $pdf->get_width();
    $h = $pdf->get_height();
    $font = $fontMetrics->getFont("DejaVu Sans", "normal");
    $size = 7;
    $grey = array(0.55, 0.62, 0.71);

    // garis pemisah footer (di area margin bawah) - digambar di SETIAP halaman
    $pdf->page_line(39.7, $h - 47, $w - 39.7, $h - 47, array(0.88, 0.90, 0.93), 0.7);

    // teks kiri
    $pdf->page_text(39.7, $h - 39, {!! $__footerLeftPhp !!}, $font, $size, $grey);

    // nomor halaman kanan
    $pn = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
    $pnWidth = $fontMetrics->getTextWidth($pn, $font, $size);
    $pdf->page_text($w - 39.7 - $pnWidth, $h - 39, $pn, $font, $size, $grey);
}
</script>
