{{--
    Stylesheet bersama untuk semua laporan PDF (dompdf 3.x).
    Di-include di dalam blok <style> tiap template.
    Kunci perbaikan: @page { margin } -> margin halaman nyata di SETIAP halaman.
    Footer + nomor halaman digambar via partial 'pdf.partials.footer-script'
    (butuh opsi isPhpEnabled = true).
--}}

/*
    PENTING: JANGAN memakai  * { margin: 0 }  di PDF dompdf.
    Selektor universal ikut menimpa margin frame root sehingga aturan
    @page { margin } jadi tidak berlaku (halaman jadi tanpa margin sama sekali).
    Cukup reset box-sizing + elemen tertentu saja.
*/
* { box-sizing: border-box; }

@page {
    margin: 1.5cm 1.4cm 2.1cm 1.4cm;   /* atas | kanan | bawah | kiri */
}

body { margin: 0; padding: 0; }
h1, h2, h3, h4, p, table, ul, ol, figure { margin: 0; padding: 0; }

body {
    font-family: "DejaVu Sans", Arial, sans-serif;
    font-size: 10px;
    line-height: 1.45;
    color: #334155;
}

/* ============ LETTERHEAD (halaman pertama) ============ */
.letterhead {
    display: table;
    width: 100%;
    border-bottom: 2px solid #1e3a8a;
    padding-bottom: 9px;
    margin-bottom: 14px;
}
.letterhead .lh-brand { display: table-cell; vertical-align: middle; }
.letterhead .lh-meta  { display: table-cell; vertical-align: middle; text-align: right; width: 210px; }
.letterhead .lh-logo  { height: 58px; width: auto; }
.brand-mark {
    display: inline-block;
    background: #1e3a8a;
    color: #fff;
    font-weight: bold;
    font-size: 13px;
    letter-spacing: 1px;
    padding: 5px 11px;
    border-radius: 5px;
}
.brand-tag { font-size: 8px; color: #64748b; margin-top: 4px; letter-spacing: 0.2px; }
.lh-meta .m-label { font-size: 7.5px; text-transform: uppercase; letter-spacing: 0.6px; color: #94a3b8; }
.lh-meta .m-value { font-size: 10px; color: #1e293b; font-weight: bold; margin-top: 2px; }

/* ============ JUDUL LAPORAN ============ */
.report-band {
    background: #1e3a8a;
    color: #fff;
    border-radius: 8px;
    padding: 13px 17px;
    margin-bottom: 15px;
}
.report-band h1 { font-size: 15.5px; font-weight: bold; }
.report-band p  { font-size: 9px; color: #c7d2fe; margin-top: 3px; }
.report-band .band-badge {
    display: inline-block;
    margin-top: 8px;
    background: rgba(255,255,255,0.16);
    border: 1px solid rgba(255,255,255,0.28);
    color: #fff;
    font-size: 8px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 3px 9px;
    border-radius: 20px;
}

/* ============ KARTU IDENTITAS ============ */
.identity {
    display: table;
    width: 100%;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    padding: 12px 16px;
    margin-bottom: 15px;
}
.identity .id-main { display: table-cell; vertical-align: middle; }
.identity .id-side { display: table-cell; vertical-align: middle; text-align: right; width: 150px; }
.identity .id-name { font-size: 14px; font-weight: bold; color: #0f172a; }
.identity .id-sub  { font-size: 9px; color: #64748b; margin-top: 2px; }
.identity .id-chip {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    font-size: 7.5px;
    font-weight: bold;
    letter-spacing: 0.4px;
    padding: 2px 8px;
    border-radius: 20px;
}
.identity .id-rank       { font-size: 21px; font-weight: bold; color: #1e293b; line-height: 1; }
.identity .id-rank-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.4px; margin-top: 3px; }

/* ============ BARIS KPI ============ */
.kpi-row { display: table; width: 100%; border-spacing: 7px; margin: -7px 0 11px -7px; }
.kpi {
    display: table-cell;
    width: 25%;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    padding: 10px 12px;
}
.kpi.accent { background: #eff6ff; border-color: #bfdbfe; }
.kpi-num   { font-size: 20px; font-weight: bold; color: #1e3a8a; line-height: 1; }
.kpi-label { font-size: 7.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-top: 5px; }
.kpi-sub   { font-size: 8px; color: #15803d; font-weight: bold; margin-top: 3px; }

/* ============ SECTION ============ */
.section { margin-bottom: 15px; }
.section-head {
    border-left: 4px solid #2563eb;
    background: #eff6ff;
    padding: 6px 10px;
    font-size: 10.5px;
    font-weight: bold;
    color: #1e3a8a;
}
.section-head .count { float: right; font-size: 8px; color: #3b82f6; font-weight: normal; padding-top: 2px; }

/* ============ TABEL DATA ============ */
table.data { width: 100%; border-collapse: collapse; font-size: 8.5px; margin-top: 6px; }
table.data thead th {
    background: #1e3a8a;
    color: #fff;
    text-align: left;
    padding: 5px 7px;
    font-size: 7.5px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
table.data tbody td { padding: 5px 7px; border-bottom: 1px solid #eef2f7; vertical-align: top; }
table.data tbody tr:nth-child(even) td { background: #f8fafc; }
table.data .col-no { width: 22px; text-align: center; color: #94a3b8; }
table.data .num { text-align: center; }
table.data thead { display: table-header-group; }
table.data tr { page-break-inside: avoid; }

/* ============ PILL / STATUS ============ */
.pill { display: inline-block; padding: 1px 7px; border-radius: 20px; font-size: 7.5px; font-weight: bold; }
.pill-green { background: #dcfce7; color: #166534; }
.pill-amber { background: #fef3c7; color: #92400e; }
.pill-red   { background: #fee2e2; color: #991b1b; }
.pill-blue  { background: #dbeafe; color: #1e40af; }
.pill-gray  { background: #e2e8f0; color: #475569; }

/* ============ BAR KATEGORI ============ */
.catbar { margin-top: 6px; }
.catbar-row  { display: table; width: 100%; margin-bottom: 5px; }
.catbar-name { display: table-cell; width: 118px; font-size: 8.5px; color: #475569; vertical-align: middle; }
.catbar-track{ display: table-cell; vertical-align: middle; }
.catbar-fill { height: 9px; background: #2563eb; border-radius: 3px; }
.catbar-val  { display: table-cell; width: 55px; text-align: right; font-size: 8.5px; font-weight: bold; color: #1e293b; vertical-align: middle; }

/* ============ LENCANA ============ */
.badge-grid { display: table; width: 100%; border-spacing: 6px; margin-left: -6px; }
.badge-item {
    display: table-cell;
    width: 25%;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    padding: 9px 10px;
    text-align: center;
}
.badge-item .b-name  { font-size: 8.5px; font-weight: bold; color: #1e293b; }
.badge-item .b-state { font-size: 7.5px; margin-top: 3px; color: #15803d; font-weight: bold; }

/* ============ UMUM ============ */
.muted  { color: #94a3b8; }
.empty  { text-align: center; color: #94a3b8; font-style: italic; padding: 12px; font-size: 8.5px; border: 1px dashed #e2e8f0; border-top: none; }
.note   { font-size: 8px; color: #94a3b8; margin-top: 5px; }
.info-line { font-size: 8.5px; color: #64748b; margin: -3px 0 13px; }
.info-line strong { color: #1e293b; }
