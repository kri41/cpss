# Log Pengembangan Sistem — CPSS (Dataraga)

> **Lampiran Disertasi** — Catatan kronologis pengembangan prototipe sistem
> *Cloud Participatory Sport Sensing System* (CPSS) untuk pengujian hipotesis
> penerimaan dan penggunaan teknologi pada Tenaga Penggerak Olahraga Nasional (TPON).

| | |
|---|---|
| **Nama sistem** | CPSS — *Cloud Participatory Sport Sensing System* Keolahragaan Daerah |
| **Nama aplikasi / merek** | **Dataraga** — *"Kamu Gerak, Indonesia Tahu"* |
| **Peneliti / pengembang** | Muhammad Dzul Fikri |
| **Alamat produksi** | https://dataraga.my.id |
| **Repositori** | Git (branch utama `main`), 91 *commit* |
| **Periode pengembangan** | 22 Maret 2026 – 29 Agustus 2026 (± 5 bulan, 10 iterasi) |
| **Lokasi sampel penelitian** | Kabupaten Banyuwangi & Kabupaten Jember, Jawa Timur |
| **Landasan teori operasional** | UTAUT (*Unified Theory of Acceptance and Use of Technology*); NMIPS (*Non-Monetary Incentives for Participatory Sensing*); *Self-Determination Theory* |
| **Dokumen ini disusun** | 10 September 2026 (dari riwayat *version control* dan dokumen PRD) |

---

## 1. Ringkasan

Prototipe CPSS dikembangkan secara **iteratif–inkremental** dengan seluruh
perubahan direkam pada sistem kendali versi (Git). Titik awal adalah sebuah MVP
ber-*Role Based Access Control* (22 Maret 2026). Setelah analisis kebutuhan
*baseline* terhadap **476 responden TPON** (survei Mei–Juni 2026), pengembangan
diarahkan pada empat fitur yang menjadi **prasyarat empiris pengujian hipotesis**
(Struktur Wilayah, Akses Publik, Presensi Sederhana, dan Modul Gamifikasi), lalu
dilanjutkan dengan serangkaian iterasi penyempurnaan antarmuka, modul lanjutan
(Kampung Olahraga, Usulan Perubahan, Forum), pelaporan (PDF & *live report*), dan
tata kelola akun (verifikasi surel, pengaturan surel keluar).

Total keluaran hingga akhir periode: **91 *commit*, 47 migrasi basis data,
22 model, 38 *controller*, 98 berkas tampilan, 2 kelas layanan (*service*)**, serta
satu *dataset* wilayah administratif Indonesia (91.638 baris: provinsi →
kabupaten/kota → kecamatan → desa/kelurahan).

---

## 2. Metodologi Pengembangan

- **Pendekatan:** iteratif–inkremental. Setiap iterasi menghasilkan fungsi yang
  dapat diuji, di-*deploy*, dan menerima umpan balik peneliti sebelum iterasi
  berikutnya.
- **Kendali versi:** Git. Pesan *commit* memakai konvensi *Conventional Commits*
  (`feat:`, `fix:`, `refactor:`, `style:`, `chore:`, `docs:`) sehingga riwayat
  dapat ditelusuri per jenis perubahan.
- **Basis keputusan fitur:** setiap penambahan fitur ditimbang terhadap
  pertanyaan *"apakah ini prasyarat pengujian H1/H2?"*. Bila bukan, fitur
  digolongkan sebagai pengembangan lanjutan (Fase 2) atau bahan Bab V (Saran).
- **Dokumen pendamping** (di dalam repositori): `prd.md` (PRD v2.1),
  `prd-addendum-v3.md`, `prd-addendum-v4.md`, `PROGRESS.md` (checklist
  implementasi), `SESSION_LOG.md` (catatan sesi kerja).
- **Lingkungan:** pengembangan lokal (Laragon, Windows) dengan basis data MySQL/
  MariaDB; produksi di *shared hosting* (cPanel) dengan alur rilis
  `git pull` → `php artisan migrate` → `php artisan optimize:clear`.

---

## 3. Arsitektur & Tumpukan Teknologi (kondisi akhir)

**Pola arsitektur:** klien–server berbasis web (monolitik Laravel + Blade),
dengan lapisan validasi hak akses di *middleware* sebelum permintaan diproses.

```
Relawan / Admin / Publik
        │  HTTPS
        ▼
  Laravel Middleware  ──►  Auth & Role Validation (CheckRole)
        │                  Audit Log Service   (mutasi data)
        │                  Gamification Service (transaksi poin & lencana)
        ▼
  Basis Data (MySQL/MariaDB)
```

| Lapisan | Teknologi |
|---|---|
| *Framework* | Laravel (PHP 8.3+); proyek dimulai pada Laravel 11 dan dimutakhirkan mengikuti rilis (hingga Laravel 13) |
| Autentikasi | Laravel Breeze (surel/kata sandi) + Laravel Socialite (Google OAuth, opsional) + verifikasi surel wajib untuk pendaftar baru |
| Tampilan | Blade + Tailwind CSS (via Vite), Alpine.js, Chart.js |
| Peta | Leaflet (peta sebaran & *choropleth*) |
| PDF | `barryvdh/laravel-dompdf` (kop surat + logo + nomor halaman) |
| QR | `simplesoftwareio/simple-qrcode` (presensi & check-in Kampung Olahraga) |
| Basis data | MySQL (produksi) / MariaDB (lokal); SQLite dipakai sebagian pengujian |
| PWA | *Service worker* + *manifest* (dukungan pemasangan di Android & iOS) |
| Uji & gaya kode | Pest, Laravel Pint |

**Modul aplikasi (kondisi akhir):** Dashboard, Prasarana, Klub/Komunitas, Event,
Partisipasi (+Presensi), Kampung Olahraga (+check-in QR), Talenta, Tenaga Ahli,
Kalender, Leaderboard & Poin, Lencana, Forum/Diskusi, Pengumuman, Usulan
Perubahan, Audit Log, Manajemen Pengguna (+import massal), **Laporan Relawan**
(+ *Live Report* publik), dan **Pengaturan** (surel keluar & templat surel).

---

## 4. Tabel Ringkas Iterasi Pengembangan

| # | Periode | Fokus iterasi | *Commit* penanda |
|---|---|---|---|
| 0 | 22–23 Mar 2026 | Rilis perdana prototipe MVP (RBAC + Dashboard) | `5745428`, `6f5d211` |
| 1 | 19 Jun 2026 | Modul inti pelaporan + *middleware* + Audit Log | `de46493` |
| 2 | 20 Jun 2026 | **Fase 1 Prototipe Disertasi** — 4 fitur prasyarat pengujian | `a84c6e4` … `fb59cdb` |
| 3 | 27 Jun 2026 | **Fase 1.5** — penyempurnaan pra-uji pakar (gamifikasi, kalender, notifikasi, filter publik) | `f43a4df` … `163aefe` |
| 4 | 28–29 Jun 2026 | *Landing page* & tata letak halaman publik; modal verifikasi data | `db6094e` … `2d6c262` |
| 5 | 17 Jul 2026 | **Fase 2** — PWA, ekspor CSV, multi-foto, peta *choropleth*, import massal | `132e767` … `61afedd` |
| 6 | 18 Jul 2026 | Redesain antarmuka + maskot; laporan PDF per kategori & terpusat | `8fb647c` … `b7bc9ed` |
| 7 | 20–21 Jul 2026 | **Kampung Olahraga** (v1 → v2), *seed* wilayah 91.638 baris, Usulan Perubahan, Google OAuth | `5db91b1` … `a2f5410` |
| 8 | 22 Jul 2026 | Alur validasi data (status *rejected*, "Butuh Perbaikan", CSV gabungan) | `c09fd17` … `edcb62f` |
| 9 | 26–27 Jul 2026 | Fitur kolaborasi: Pengumuman, *flyer* Event, Papan Diskusi/Forum | `403ce8a` … `9c413ad` |
| 10 | 29 Agu 2026 | Pelaporan lanjutan (Laporan Relawan, PDF berlogo, *Live Report*), verifikasi surel + anti-bot, menu Pengaturan | `ffe2cbe` … `bccf516` |

---

## 5. Log Rinci per Iterasi

### Iterasi 0 — Prototipe MVP (22–23 Maret 2026)
- Kerangka aplikasi Laravel + Breeze; *Role Based Access Control* tiga tingkat
  (Super Admin, Admin, Relawan); Dashboard awal.
- Halaman *login* dan pendaftaran.
- **Keluaran:** ± 16.800 baris kode awal (114 berkas).

### Iterasi 1 — Modul Inti Pelaporan (19 Juni 2026)
- Modul: Clubs, Events, Prasarana, Talenta, Tenaga Ahli, Partisipasi.
- **Audit Log** otomatis pada setiap mutasi data (aktor, waktu, tabel/baris,
  aksi, nilai sebelum–sesudah).
- **Middleware `CheckRole`** — validasi hak akses di tingkat server sebelum
  permintaan diproses (mencegah manipulasi akses via URL).
- Dokumen PRD direvisi ke **v2.1** berdasarkan tiga dokumen resmi survei
  **476 responden TPON**.

### Iterasi 2 — Fase 1 Prototipe Disertasi (20 Juni 2026)
Empat fitur yang dinyatakan sebagai **prasyarat teknis dan empiris pengujian
hipotesis H1/H2**:

1. **Modul Gamifikasi** — mengoperasionalkan pilar NMIPS + *Self-Determination
   Theory*: sistem poin per entri tervalidasi, *leaderboard* (mingguan/bulanan/
   total), dan lencana pencapaian otomatis. Diimplementasikan sebagai
   `GamificationService` dengan aturan poin per kode aktivitas dan mekanisme
   pembatalan poin bila data dinyatakan tidak valid.
2. **Struktur Wilayah Administratif** — setiap entri data dan setiap akun relawan
   terasosiasi ke Desa/Kelurahan → Kecamatan → Kabupaten/Kota (prasyarat
   agregasi data untuk pengujian H1 pada lokasi sampel).
3. **Akses Publik Tanpa Login** — rute `index`/`show` Prasarana, Clubs, Events
   dapat diakses tanpa autentikasi (menguji *Facilitating Conditions* pada UTAUT).
4. **Presensi Sederhana** pada modul Partisipasi — pencatatan kehadiran individu
   sebagai pelengkap estimasi jumlah; merupakan permintaan terbuka terbanyak
   ke-4 dari lapangan (92 kemunculan) sekaligus pemicu poin observasi berulang.

- Ditambahkan pula **kontrol akses berbasis wilayah** (kepemilikan data + status
  validasi + kondisi penyuntingan), serta 8 akun *dummy* (1 Super Admin,
  2 Admin, 5 Relawan) dan *seeder* Audit Log untuk data uji.

### Iterasi 3 — Fase 1.5: Penyempurnaan Pra-Uji Pakar (27 Juni 2026)
- **Star rating interaktif** kondisi Prasarana pada formulir & halaman detail.
- **Filter & pencarian** pada halaman publik; pengunjung hanya melihat data
  berstatus *validated*.
- **Notifikasi bergaya kotak masuk** untuk perolehan poin & lencana
  (`<x-poin-toast />` untuk umpan balik singkat).
- **Kalender terintegrasi** (`/kalender`, publik) — event & jadwal latihan klub.
- **Leaderboard, halaman *My Points*, modul Kehadiran**, serta **API Wilayah**
  (dropdown bertingkat provinsi→desa).
- **QR presensi** (token + halaman daftar publik) dan halaman **Daftar Relawan**.

### Iterasi 4 — Landing Page & Tata Letak Publik (28–29 Juni 2026)
- Redesain *landing page* menjadi satu layar (tanpa *scroll* di desktop) dengan
  bilah statistik langsung di *hero*.
- Tata letak *navbar* konsisten untuk seluruh halaman publik; redesain halaman
  detail (Prasarana, Events, Clubs) ke `layouts.public`.
- Pemisahan tampilan `index` untuk pengguna terautentikasi vs. pengunjung.
- **Modal verifikasi & pembatalan verifikasi** di dashboard dengan pratinjau data.

### Iterasi 5 — Fase 2: PWA, Ekspor, Peta (17 Juli 2026)
- **PWA** — *service worker* + *manifest*, dapat dipasang sebagai aplikasi.
- **Ekspor CSV** dan unggah **multi-foto** untuk Prasarana.
- **Peta *choropleth*** (Leaflet) sebaran data per wilayah.
- **Import massal pengguna via CSV** dengan alur dua langkah
  (unggah → validasi/pratinjau → konfirmasi), termasuk deteksi otomatis
  pemisah kolom (koma vs titik-koma) untuk kompatibilitas Excel Indonesia.
- `ProductionSeeder` untuk penyiapan minimal di server; kueri notifikasi
  dibungkus *try–catch* agar tidak menyebabkan galat 500 saat tabel belum ada.

### Iterasi 6 — Redesain Antarmuka & Maskot (18 Juli 2026)
- Redesain *landing page* dan halaman autentikasi dengan **karakter maskot**.
- Tema visual: latar *gradient* biru→putih, *sidebar*/navbar *glassmorphism*.
- Perbaikan responsif (form filter & kartu statistik pada perangkat seluler).
- **Laporan PDF per kategori kontribusi relawan** (Prasarana, Events, Clubs,
  Partisipasi) di halaman profil.
- **Laporan PDF terpusat** — konsolidasi tombol ekspor ke satu titik di dashboard.

### Iterasi 7 — Kampung Olahraga & Modul Tata Kelola Data (20–21 Juli 2026)
- **Kampung Olahraga v1** (20 Jul): entitas "kampung" + check-in QR + pemetaan
  syarat Kemenpora (`KomponenSyarat`).
- **Kampung Olahraga v2** (21 Jul, redesain menyeluruh):
  - QR menjadi **per-fasilitas** (Prasarana yang didaftarkan ke kampung), bukan
    per-kampung — memanfaatkan data prasarana yang sudah dilaporkan.
  - **Klub/Komunitas** dapat didaftarkan ke kampung (relasi *many-to-many*),
    muncul sebagai pilihan saat check-in; jenis olahraga terisi otomatis.
  - Penambahan **RT/RW**, dua tab *leaderboard* baru (Kampung & Klub), dan
    integrasi ke `GamificationService` (`kampung_baru`, +30 poin saat verifikasi).
  - **Google OAuth** ditambahkan sebagai opsi login tambahan.
- **Data *seed* wilayah administratif Indonesia** — 91.638 baris
  (provinsi/kabupaten/kecamatan/desa).
- **Usulan Perubahan** — mekanisme pengajuan koreksi untuk data yang sudah
  tervalidasi (menjaga integritas tanpa menutup jalur perbaikan).
- Prasarana: kategori olahraga menjadi **multi-pilih** (relasi pivot).
- Perbaikan RBAC: dashboard relawan dibatasi per wilayah; tombol Verifikasi
  disembunyikan dari relawan.

### Iterasi 8 — Alur Validasi & Operasional (22 Juli 2026)
- Status validasi diperluas dengan nilai **`rejected`** (Prasarana/Clubs/Events/
  Partisipasi) dan aksi **"Butuh Perbaikan"**.
- **Unduh CSV gabungan** lintas modul dari dashboard; pengurutan daftar.
- Tombol **Hapus** untuk Admin & Super Admin.

### Iterasi 9 — Fitur Kolaborasi & Komunikasi (26–27 Juli 2026)
- **Pengumuman Dashboard** — kanal informasi dari admin ke relawan.
- **Unggah *flyer*** untuk Event.
- **Papan Diskusi per Provinsi** → dikembangkan menjadi **Forum** (digabung
  dengan Daftar Relawan, tata letak 70/30).
- Perbaikan: 5 *endpoint* Ekspor CSV yang sebelumnya selalu galat 500; bel
  notifikasi yang tidak tampil di sebagian besar halaman.

### Iterasi 10 — Pelaporan Lanjutan, Verifikasi Akun & Pengaturan (29 Agustus 2026)
- **Menu Laporan Relawan** (untuk Admin/Super Admin): daftar relawan berperingkat
  dengan ringkasan poin & kontribusi, halaman detail per relawan, **PDF per
  relawan**, dan **rekap PDF** seluruh relawan. Logika pelaporan disatukan pada
  `App\Services\RelawanReportService`.
- **Perombakan seluruh laporan PDF:** kop surat berlogo Dataraga, perbaikan
  margin halaman, nomor halaman, pelokalan tanggal (Bahasa Indonesia, zona
  waktu WIB). *Stylesheet* PDF disatukan pada partial bersama.
- ***Live Report* publik** — tautan unik per relawan (`/r/{token}`, token acak
  48 karakter yang tidak dapat ditebak) yang menampilkan laporan kinerja secara
  *live* tanpa perlu login, dapat dibagikan dan diganti (invalidasi) oleh admin.
- **Verifikasi surel wajib untuk pendaftar baru** (`MustVerifyEmail`) sebagai
  penyaring bot; seluruh akun lama otomatis dianggap terverifikasi melalui
  migrasi agar tidak terkunci. Ditambah *honeypot* dan pembatasan laju
  (*throttle*) pada formulir pendaftaran.
- **Menu Pengaturan** (Super Admin) — konfigurasi **server surel keluar (SMTP)**
  dan **isi templat surel** (verifikasi & selamat datang) langsung dari
  antarmuka, dengan kata sandi SMTP tersimpan terenkripsi dan tombol "Kirim
  Surel Uji".
- Perbaikan ketahanan: halaman tidak menghasilkan galat 500 bila migrasi belum
  dijalankan; perapian kartu identitas relawan (web & PDF).

---

## 6. Pemetaan Fitur → Kerangka Pengujian Penelitian

| Fitur sistem | Konstruk / kebutuhan yang dilayani |
|---|---|
| Struktur Wilayah Administratif | Prasyarat **agregasi data** untuk pengujian **H1** pada lokasi sampel yang sama dengan tim pakar |
| Akses Publik Tanpa Login | *Facilitating Conditions* (**UTAUT**) — biaya implementasi rendah, dampak besar pada persepsi kemudahan akses ekosistem |
| Sistem Poin, Leaderboard, Lencana | **NMIPS** + *Self-Determination Theory* — insentif non-moneter terhadap *Behavioral Intention* dan *Use Behavior* |
| Notifikasi poin / *toast* | Umpan balik segera (*immediate feedback*) — komponen kompetensi pada SDT |
| Presensi Sederhana | Permintaan lapangan prioritas ke-4 (92 kemunculan) + pemicu **poin observasi berulang** |
| Audit Log + Middleware Role | Kebutuhan **Keamanan Tingkat Lanjut** — integritas & akuntabilitas data kebijakan |
| Verifikasi surel + anti-bot | Integritas **populasi akun/responden** selama pelaksanaan uji lapangan |
| Laporan Relawan & *Live Report* | Transparansi kontribusi (dukungan *Performance Expectancy* & pengakuan sosial); instrumen pemantauan bagi peneliti/admin |
| Usulan Perubahan | Menjaga *single source of truth* tanpa menutup jalur koreksi data lapangan |

---

## 7. Statistik Pengembangan

| Metrik | Nilai |
|---|---|
| Rentang waktu | 22 Mar 2026 – 29 Agu 2026 (± 22 minggu) |
| Jumlah *commit* | 91 |
| Iterasi besar | 10 |
| Migrasi basis data | 47 |
| Model Eloquent | 22 |
| *Controller* | 38 |
| Berkas tampilan (Blade) | 98 |
| Kelas layanan (*Service*) | 2 (`GamificationService`, `RelawanReportService`) |
| Baris data *seed* wilayah | 91.638 |
| *Commit* terbesar (baris) | `5745428` rilis MVP (± 16.832 baris) & `33df522` *seed* wilayah (91.638 baris) |

---

## 8. Catatan Teknis & Pelajaran (*Lessons Learned*)

- **PDF (dompdf):** selektor CSS universal `* { margin: 0 }` ikut menimpa margin
  *frame* halaman sehingga aturan `@page { margin }` tidak berlaku dan halaman
  PDF tercetak tanpa margin. Solusi: reset hanya `box-sizing` + margin `body`,
  bukan selektor universal.
- **Kompatibilitas berkas Indonesia:** Excel di Indonesia mengekspor CSV dengan
  pemisah titik-koma; import massal harus mendeteksi pemisah secara otomatis.
- **Ketahanan rilis di *shared hosting*:** kode yang menyentuh tabel/kolom baru
  dibungkus penanganan galat agar situs tidak *down* total bila urutan
  `git pull` → `migrate` belum tuntas.
- **Migrasi non-merusak untuk fitur autentikasi:** saat mengaktifkan verifikasi
  surel pada sistem yang sudah berjalan, seluruh akun lama harus ditandai
  terverifikasi lewat migrasi agar tidak ada pengguna yang terkunci.
- **Lokalisasi:** `Carbon`/`isoFormat` perlu diset ke *locale* `id` secara
  eksplisit dan konversi zona waktu ke `Asia/Jakarta` untuk label "WIB".

---

## 9. Lampiran — Rekap Seluruh *Commit*

| Tgl | *Commit* | Ringkasan |
|---|---|---|
| 2026-03-22 | `5745428` | feat: rilis perdana prototipe CPSS MVP dengan RBAC dan Dashboard |
| 2026-03-23 | `6f5d211` | feat: update login dan regis |
| 2026-06-19 | `de46493` | feat: tambah modul clubs, events, prasarana, talenta, tenaga ahli, partisipasi, audit logs, users dan middleware |
| 2026-06-19 | `10c3afb` | docs: update PRD and session log with baseline survey analysis and feature gap prioritization |
| 2026-06-20 | `ff89663` | docs: revisi PRD v2.1 dan SESSION_LOG berdasarkan 3 dokumen resmi survei 476 responden TPON |
| 2026-06-20 | `a84c6e4` | feat: implementasi Fase 1 Prototipe Disertasi (Gamifikasi, Wilayah, Akses Publik, Presensi) |
| 2026-06-20 | `ab949fe` | feat: Fase 1 Prototipe + uji fungsional & akun dummy |
| 2026-06-20 | `6963ad7` | fix(ui): public access layout, hide edit/delete for guests, add wilayah & kehadiran display |
| 2026-06-20 | `93a9989` | docs: update session log with UI/UX review results |
| 2026-06-20 | `ffe54f1` | fix(layout): support both component dan @yield('content') (legacy views) |
| 2026-06-20 | `37e4ee6` | fix(clubs): align stats card with filtered list for non-super-admin users |
| 2026-06-20 | `df15685` | feat(seeders): add AuditLogSeeder to backfill audit logs for dummy data |
| 2026-06-20 | `d73eac6` | chore(seeders): integrate AuditLogSeeder into DummyDataSeeder |
| 2026-06-20 | `fb59cdb` | feat(access-control): wilayah-based ownership, validation status, and conditional editing |
| 2026-06-20 | `0c8a200` | fix(user): add missing class closing brace |
| 2026-06-27 | `f43a4df` | feat(prasarana): star rating interaktif di form create/edit & show |
| 2026-06-27 | `680b44b` | feat(publik): filter search & hanya tampilkan data validated untuk guest |
| 2026-06-27 | `e3f5665` | feat(notifikasi): inbox-style notification untuk poin & lencana |
| 2026-06-27 | `5ff2dba` | feat(kalender): halaman kalender event & jadwal latihan club |
| 2026-06-27 | `3b2f893` | feat(gamifikasi): leaderboard, my-points, kehadiran & model updates |
| 2026-06-27 | `033bb9a` | feat(wilayah): struktur administrasi, dropdown cascading, api wilayah |
| 2026-06-27 | `7151d3f` | feat(presensi): qr token, daftar publik, daftar relawan |
| 2026-06-27 | `c5b3f27` | feat(ui): dashboard, welcome, form updates talenta & users |
| 2026-06-27 | `a9e6d73` | chore(seeders): dummy data, audit log, session log & progress docs |
| 2026-06-27 | `163aefe` | docs: update PROGRESS.md dengan fitur filter, notifikasi & kalender |
| 2026-06-27 | `db6094e` | feat(landing): redesign minimalis — navbar menu + hero card stats |
| 2026-06-28 | `acfa389` | feat(landing): fullscreen 1 halaman tanpa scroll desktop, navbar minimal |
| 2026-06-28 | `cb98bc2` | docs: update PROGRESS.md landing page |
| 2026-06-28 | `c0bc41b` | fix(landing): tambah footer untuk mobile |
| 2026-06-28 | `451abfc` | fix(landing): tambah footer desktop dengan flex layout, konten lebih compact |
| 2026-06-28 | `5fe5429` | feat(landing): stats bar di hero, hilangkan tombol masuk, maximize PC layout |
| 2026-06-28 | `f68abf8` | feat(public-layout): navbar layout for public pages, redesign prasarana index |
| 2026-06-28 | `41bf31d` | fix(public-layout): navbar h-14 konsisten, stats di atas filter |
| 2026-06-28 | `66a96c4` | feat(public-layout): clubs & kalender pakai navbar, stats di atas filter |
| 2026-06-28 | `90374a1` | fix(kalender): full width, full height tanpa scroll, konsisten navbar |
| 2026-06-28 | `1b1aeea` | style(kalender): tambah padding kanan-kiri-bawah |
| 2026-06-28 | `7c19a40` | fix(kalender): query OR event, layout grid rows dinamis, cell overflow |
| 2026-06-28 | `f50cf83` | refactor(public): redesign detail/show pages (prasarana, events, clubs) ke layouts.public |
| 2026-06-29 | `d45bf50` | feat(dashboard): split index views untuk auth vs guest |
| 2026-06-29 | `0bb8f9e` | feat(dashboard): tambah modal verifikasi & batalkan verifikasi di index dashboard |
| 2026-06-29 | `2d6c262` | feat(dashboard): tambah preview data di modal verifikasi & batalkan verifikasi |
| 2026-07-17 | `132e767` | feat: Fase 1.5 selesai + Fase 2 (PWA, export CSV, multi foto) + peta choropleth |
| 2026-07-17 | `d709655` | chore: tambah public/build (Vite compiled assets untuk production) |
| 2026-07-17 | `3b1a3f9` | feat(users): tambah import bulk via CSV & revisi narasi welcome |
| 2026-07-17 | `c3479f9` | fix(layout): perbaiki sticky header stats & filter yang hilang saat scroll |
| 2026-07-17 | `51f069d` | fix(layout): hapus overflow dari wrapper agar sticky bekerja di viewport |
| 2026-07-18 | `8fb647c` | feat(ui): redesign landing page & auth dengan karakter maskot |
| 2026-07-18 | `872fd09` | feat(ui): landing fullscreen 1-screen dan karakter mascot di halaman publik |
| 2026-07-18 | `f5dca10` | feat(ui): teks hero baru, gradient background semua halaman, mobile fix |
| 2026-07-18 | `90cc1b3` | fix(ui): gradient biru tua ke putih sesuai palet landing page |
| 2026-07-18 | `27d1f36` | feat(ui): sidebar & navbar glass transparan di atas gradient biru |
| 2026-07-18 | `728fc6b` | fix(responsive): filter form & stats mobile — select 2-per-row, padding compact |
| 2026-07-18 | `ebdef35` | chore: rebuild Vite assets dengan class Tailwind baru |
| 2026-07-18 | `cb92f61` | fix(pwa): tambah dukungan iOS & perbaiki ukuran icon manifest |
| 2026-07-18 | `f76fce6` | feat(seeder): tambah ProductionSeeder untuk setup minimal di server |
| 2026-07-18 | `e3bac41` | fix(layout): wrap notifikasi query dalam try-catch agar tidak 500 |
| 2026-07-18 | `bfcbdd7` | fix(filter): tambah sm:min-w-0 agar select tidak stack di desktop |
| 2026-07-18 | `ea8b1a8` | feat(users): bulk import 2-step preview — upload CSV → validasi → konfirmasi |
| 2026-07-18 | `b8f8b92` | fix(users): tampilkan flash error di form import + longgarkan validasi MIME CSV |
| 2026-07-18 | `61afedd` | fix(users): auto-detect delimiter CSV (comma vs semicolon) |
| 2026-07-18 | `6ec2fa9` | feat(profil): laporan PDF per kategori kontribusi relawan |
| 2026-07-18 | `d886918` | feat: tombol Tambah untuk relawan, export data sendiri, redesain kalender |
| 2026-07-18 | `b7bc9ed` | feat(dashboard): laporan PDF terpusat — pindah ke dashboard |
| 2026-07-20 | `5db91b1` | feat(kampung): tambah fitur Kampung Olahraga + check-in QR |
| 2026-07-21 | `8f75e3a` | feat(kampung): redesain Kampung Olahraga v2 — QR per-fasil, klub/komunitas, RT/RW, Google auth |
| 2026-07-21 | `bab52b4` | fix(gamifikasi): ganti nama lencana "Penjaga Sarpras" jadi "Duta Sarpras" |
| 2026-07-21 | `15f53c3` | fix(rbac): batasi dashboard relawan per wilayah + sembunyikan tombol Verifikasi |
| 2026-07-21 | `e29afbb` | fix(layout): tambah @stack('styles') di layouts.app |
| 2026-07-21 | `c710d03` | fix(routes): "/prasarana/create" dkk 404 karena tertangkap route show |
| 2026-07-21 | `33df522` | fix(wilayah): tambah data seed provinsi/kabupaten/kecamatan/desa (91.599 baris) |
| 2026-07-21 | `cb43493` | feat(perubahan): tambah fitur Usulan Perubahan untuk data tervalidasi |
| 2026-07-21 | `974f65b` | fix(perubahan): koreksi mekanisme Usulan Perubahan sesuai aturan yang benar |
| 2026-07-21 | `a2f5410` | feat(prasarana): hapus field Club/Komunitas, jadikan Kategori Olahraga multi-pilih |
| 2026-07-22 | `c09fd17` | feat(dashboard,validasi): unduh CSV gabungan, urutan listing, aksi Butuh Perbaikan |
| 2026-07-22 | `d32b210` | fix(validasi): perbaiki tombol Verifikasi/Butuh Perbaikan & link Kembali salah arah |
| 2026-07-22 | `611d04a` | fix(validasi): tambah 'rejected' ke enum status_validasi |
| 2026-07-22 | `3f32cc2` | fix(build): rebuild asset Vite/Tailwind |
| 2026-07-22 | `edcb62f` | feat(prasarana,events): tombol Hapus untuk Admin & Super Admin |
| 2026-07-26 | `403ce8a` | fix(perubahan): modal Tolak tidak berfungsi (di luar scope Alpine x-data) |
| 2026-07-26 | `40b8b04` | feat(pengumuman): tambah fitur Pengumuman Dashboard |
| 2026-07-27 | `82f0c16` | fix(layout): bel notifikasi tidak muncul di sebagian besar halaman |
| 2026-07-27 | `d732e14` | feat(events): tambah upload flyer untuk Event |
| 2026-07-27 | `e7ec6c7` | fix(export): perbaiki 5 endpoint Export CSV yang selalu error 500 |
| 2026-07-27 | `8871d4c` | feat(diskusi): tambah Papan Diskusi per Provinsi |
| 2026-07-27 | `9c413ad` | feat(forum): gabung menu Daftar Relawan ke Forum, layout 70/30 |
| 2026-08-29 | `ffe2cbe` | feat(laporan): menu Laporan Relawan + rombak semua PDF laporan |
| 2026-08-29 | `03eb53d` | feat(pdf): pakai logo Dataraga di kop semua laporan PDF |
| 2026-08-29 | `3dfe422` | feat: Live Report publik, verifikasi email pendaftar, menu Pengaturan |
| 2026-08-29 | `08baa4d` | feat(laporan): tombol "Salin Link Live" di tiap baris daftar relawan |
| 2026-08-29 | `dfe0226` | fix(laporan): halaman tidak 500 bila migrasi public_report_token belum jalan |
| 2026-08-29 | `bccf516` | style(laporan): rapikan kartu identitas relawan (web & PDF) |

---

*Berkas ini dihasilkan dari riwayat kendali versi Git repositori CPSS dan dokumen
PRD internal. Untuk detail teknis tiap perubahan, rujuk pesan dan diff commit
terkait.*
