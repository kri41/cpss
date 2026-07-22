# CPSS Progress Tracker

> **Dokumen ini adalah CHECKLIST UTAMA** untuk melacak progress pengembangan CPSS (Cloud Participatory Sport Sensing).
> Update dokumen ini setiap kali selesai mengerjakan fitur. Panggil file ini saat memulai sesi baru.

---

## Metadata

| Properti | Nilai |
|----------|-------|
| **Proyek** | CPSS - Disertasi Fase 1 Prototipe |
| **Versi Target** | v2.2+ (Post-Fase 1) |
| **Terakhir Update** | 21 Juli 2026 |
| **Total Fitur Utama** | 19 |
| **Status Keseluruhan** | Fase 1 + Filter/Notif/Kalender Done, Kampung Olahraga v2 Done, Google Auth Live di Produksi |

---

## Legenda

- [x] = **SUDAH SELESAI** — Fitur sudah diimplementasi dan diuji.
- [~] = **SEDANG DIKERJAKAN** — Fitur sedang dalam proses.
- [ ] = **BELUM DIKERJAKAN** — Fitur masih dalam daftar backlog.

---

## 1. FONDASI SISTEM (Mei 2026)

### 1.1 Autentikasi & Autorisasi
- [x] Sistem Login / Register dengan Laravel Breeze
- [x] Role-Based Access Control (RBAC): Super Admin, Admin, Relawan
- [x] Middleware `CheckRole` untuk proteksi route
- [x] Ownership & wilayah-based editing (`canEdit()`, `canValidate()`)
- [x] **DIHAPUS** ~~Fitur MFA~~ (dihapus berdasarkan kebutuhan)

### 1.2 Layout & UI Dasar
- [x] Sidebar layout responsif (gradient indigo-purple)
- [x] Mobile hamburger menu
- [x] Dark mode support
- [x] Dashboard interaktif dengan Chart.js (Line, Doughnut, Bar)
- [x] Statistic cards (6 kartu utama)
- [x] Event timeline & recent activity log

### 1.3 Audit & Logging
- [x] Audit log untuk semua operasi CRUD
- [x] Middleware `AuditLogger`
- [x] Seeder backfill audit log untuk dummy data

---

## 2. MODUL DATA UTAMA (Mei - Juni 2026)

### 2.1 Prasarana
- [x] CRUD Prasarana (Create, Read, Update, Delete)
- [x] Upload foto fasilitas
- [x] Rating kondisi 1-5 untuk 8 komponen:
  - Lantai, Ring, Net, Gawang, Lapangan, Ventilasi, Pencahayaan, Kamar Mandi
- [x] **Rating bintang interaktif di form create/edit** (SVG star, hover effect, color-coded)
- [x] **Display bintang visual di halaman show**
- [x] Akses & fasilitas tambahan (boolean checklist)
- [x] Status validasi (Pending / Validated)
- [x] Komentar validasi oleh admin
- [x] Model: average condition, status label, color coding
- [x] Scope filter (kondisi, disabilitas, club, rating range)
- [x] Integrasi Leaflet map (create, edit, show)
- [x] Kolom wilayah (desa, kecamatan, kabupaten, provinsi)

### 2.2 Club / Komunitas
- [x] CRUD Club (Create, Read, Update, Delete)
- [x] Relasi ke Prasarana (dropdown)
- [x] Jadwal latihan dinamis (multiple hari & jam)
- [x] Logo upload dengan preview
- [x] Status aktif/nonaktif & tanggal berdiri
- [x] Kolom wilayah (desa, kecamatan, kabupaten, provinsi)

### 2.3 Event
- [x] CRUD Event
- [x] Kolom wilayah (desa, kecamatan, kabupaten, provinsi)

### 2.4 Partisipasi
- [x] CRUD Partisipasi
- [x] Estimasi jumlah peserta
- [x] Presensi sederhana (Kehadiran individu per partisipasi)
- [x] Tabel kehadiran di halaman show (nama, gender, usia, kelompok, status, catatan)
- [x] QR Token untuk kehadiran
- [x] Kolom wilayah (desa, kecamatan, kabupaten, provinsi)

### 2.5 Talenta & Tenaga Ahli
- [x] CRUD Talenta
- [x] CRUD Tenaga Ahli

---

## 3. FASE 1 PROTIPE — PRIORITAS PENELITIAN (Juni 2026)

### 3.1 Struktur Wilayah Administratif
- [x] Migration: tabel `wilayah` (provinsi, kabupaten, kecamatan, desa)
- [x] Migration: kolom wilayah pada users, prasarana, clubs, events, partisipasi
- [x] Component Blade: `<x-wilayah-dropdown />` (cascading dropdown)
- [x] Controller: `WilayahController` untuk API dropdown
- [x] Model: `JenisOlahraga` (master data)

### 3.2 Akses Publik Tanpa Login
- [x] Route publik: `/prasarana` (index & show)
- [x] Route publik: `/clubs` (index & show)
- [x] Route publik: `/events` (index & show)
- [x] Hide tombol tambah/edit/delete untuk guest
- [x] Sidebar aman untuk guest (wrap dengan `@auth`)
- [x] Controller fix: cek `auth()->check()` sebelum panggil method user

### 3.3 Presensi Sederhana (Kehadiran)
- [x] Migration: tabel `kehadiran`
- [x] Model: `Kehadiran` (nama_peserta, jenis_kelamin, usia, kelompok_usia, kategori_khusus, status)
- [x] Controller: `KehadiranController`
- [x] Relasi: Partisipasi hasMany Kehadiran
- [x] Migration: kolom `sumber` pada kehadiran
- [x] Migration: kolom `jenis_olahraga_id` & `rpe` pada kehadiran

### 3.4 Modul Gamifikasi (NMIPS)
- [x] Migration: tabel `point_transactions`, `badges`, `user_badges`
- [x] Migration: kolom `total_poin` pada users
- [x] Service: `GamificationService` (kalkulasi poin, leaderboard, lencana otomatis)
- [x] Controller: `LeaderboardController`
- [x] View: leaderboard (mingguan, bulanan, total)
- [x] View: `my-points` (riwayat transaksi & lencana pribadi)
- [x] Kategori poin:
  - Prasarana baru: +50 (1x per fasilitas)
  - Update prasarana: +15 (1x per fasilitas, min 30 hari)
  - Club baru: +40 (1x per klub)
  - Update club: +10 (1x per klub)
  - Event: +20 (tidak dibatasi)
  - Partisipasi valid: +3 (1x per lokasi+tanggal)
- [x] Lencana otomatis:
  - Sensor Warga Aktif (laporan pertama tervalidasi)
  - Penjaga Sarpras (>=5 prasarana unik tervalidasi)
  - Pemantau Konsisten (partisipasi >=4 minggu berbeda)
  - Pahlawan Data Olahraga (total >=500 poin ATAU kontribusi 4 kategori)
- [x] Pembatalan poin oleh Super Admin
- [x] Denormalisasi `total_poin` otomatis

---

## 4. UI/UX & TAMPILAN

### 4.1 Prasarana
- [x] Form create: star rating interaktif (hover, click, color-coded)
- [x] Form edit: star rating interaktif dengan nilai existing
- [x] Show page: display bintang SVG + label
- [x] Index: hide action buttons untuk guest
- [x] Show: hide edit button untuk guest
- [x] **Filter & Search: by nama, kabupaten, kecamatan, kategori olahraga**
- [x] **Guest hanya melihat data validated**

### 4.2 Club
- [x] Index: hide action buttons untuk guest
- [x] Show: info wilayah + hide edit untuk guest
- [x] **Filter & Search: by nama, kabupaten, kecamatan, status aktif**
- [x] **Guest hanya melihat data validated**

### 4.3 Event
- [x] Index: hide action buttons untuk guest
- [x] Show: info wilayah + hide edit untuk guest
- [x] **Filter & Search: by nama, kabupaten, kecamatan, tingkat**
- [x] **Guest hanya melihat data validated**

### 4.4 Partisipasi
- [x] Show: tabel daftar kehadiran individu
- [x] Form: wilayah dropdown

### 4.5 Leaderboard & Poin
- [x] Halaman leaderboard responsif
- [x] Halaman my-points (riwayat + lencana)
- [x] Menu sidebar untuk authenticated user

### 4.6 Landing Page (Welcome)
- [x] Navbar minimal (logo + auth saja, tanpa menu tengah)
- [x] **1 halaman fullscreen tanpa scroll di desktop**
- [x] Hero card kanan: statistik + 4 menu compact
- [x] Mobile tetap scrollable dengan menu cards
- [x] Login/Register modal tetap ada

### 4.7 Notifikasi Inbox-Style
- [x] Migration: tabel `user_notifications`
- [x] Model: `UserNotification` dengan type (poin, badge, validasi)
- [x] Controller: `NotificationController` (markAsRead, markAllAsRead)
- [x] Icon lonceng di header dengan badge unread count
- [x] Dropdown panel: list 5 notifikasi terbaru
- [x] Auto-generate notifikasi saat validasi admin (+poin)
- [x] Auto-generate notifikasi saat lencana baru didapat
- [x] Link "Tandai semua baca" & "Lihat Poin & Lencana"

### 4.7 Kalender Terintegrasi
- [x] Route publik: `/kalender`
- [x] Controller: `KalenderController`
- [x] View: grid bulanan (Senin-Minggu)
- [x] Data Event: ditampilkan per tanggal (multi-day support)
- [x] Data Jadwal Latihan Club: ditampilkan berulang per hari
- [x] Navigasi: previous/next month
- [x] Legend: Event (biru) & Jadwal Latihan (hijau)
- [x] Menu sidebar: Kalender

---

## 4.8 Kampung Olahraga v2 — Fasil-Level QR, Klub/Komunitas, Google Auth, Dual Leaderboard (20 Juli 2026)

Lihat `prd-addendum-v4.md` untuk detail lengkap konsep & rasional. Ringkasan implementasi:

- [x] Login/Register via Google (Laravel Socialite) — opsi tambahan, bukan pengganti email/password
- [x] Menu "Partisipasi" dihapus dari sidebar & nav legacy (data & route diarsipkan, tidak dihapus)
- [x] Menu "Klub" di-rename jadi "Klub/Komunitas" di semua nav
- [x] Prasarana (fasil) bisa didaftarkan ("ditarik") ke sebuah Kampung Olahraga — filter kandidat berdasarkan kesamaan wilayah (kabupaten/kecamatan/desa)
- [x] Klub/Komunitas bisa didaftarkan ke Kampung Olahraga (many-to-many, boleh lintas kampung) — filter kandidat sama
- [x] QR Code dipindah dari level Kampung ke level Fasil (`prasarana.qr_token`) — tiap fasil di dalam kampung punya QR check-in sendiri
- [x] Form check-in QR: nama, umur, klub/komunitas (atau "Belum bergabung"), jenis olahraga (auto-isi dari klub, atau pilih/tambah manual jika belum bergabung), foto terkompresi (~200KB)
- [x] Syarat minimal Kemenpora (Komponen Syarat) tetap dihitung di level total kampung (akumulasi seluruh fasil), bukan per-fasil
- [x] Leaderboard tambahan: tab "Kampung Olahraga" (ranking skor syarat Kemenpora) & tab "Klub/Komunitas" (ranking total check-in lintas kampung), di samping leaderboard relawan yang sudah ada
- [x] Kampung Olahraga kini terintegrasi ke `GamificationService` (`kampung_baru`, +30 poin ke relawan saat admin memverifikasi — sebelumnya terpisah dari sistem poin)

---

## 4.9 Perbaikan Lanjutan Pasca-Redesign (21 Juli 2026)

- [x] URL slug per-fasil, per-kampung, dsb menggunakan `kode unik` (bukan lagi nama+slug) — lihat `HasSlug` trait, kode acak 10 karakter, konsisten di semua model publik (Prasarana, Club, Event, KampungOlahraga, User)
- [x] Fix bug dashboard: klik "Detail" dari dashboard sempat melempar ke layout publik (nav landing), bukan tetap di sidebar dashboard — `prasarana.show`/`clubs.show`/`events.show` sekarang pilih layout sesuai status login
- [x] Fix dropdown wilayah tidak ter-prefill saat edit data lama + command `wilayah:normalize` untuk data legacy
- [x] Fix redirect `ClubController@update` yang keluar dari dashboard ke halaman publik
- [x] **RBAC dashboard**: relawan yang login di dashboard Prasarana/Klub-Komunitas/Events sekarang hanya melihat data di wilayahnya sendiri (kabupaten+kecamatan cocok dengan data user) — admin tetap melihat semua wilayah. Tombol "Verifikasi" yang sebelumnya ikut tampil untuk relawan (meski backend sudah menolak/403) kini disembunyikan, hanya admin yang melihatnya. Kampung Olahraga & Daftar Relawan/Leaderboard sengaja dibiarkan lintas-wilayah (direktori & kompetisi nasional).
- [x] Redesain halaman QR check-in: 2 kolom (foto+info fasil | form), tema biru konsisten, fit 1 layar di desktop, responsif di mobile; fix Alpine.js tidak ter-load (bug lama, bikin auto-isi klub & upload foto tidak berfungsi)
- [x] Redesain "Komponen Syarat Kemenpora" jadi roadmap bertingkat (bukan progress bar polos) — tetap tersirat sebagai syarat resmi Kemenpora RI
- [x] Kampung Olahraga bisa didaftarkan per RT/RW (skala kecil) — relawan lain di desa yang sama tidak lagi "kehabisan" kesempatan mendaftar & dapat poin
- [x] Rename lencana "Penjaga Sarpras" → "Duta Sarpras" (menghindari kesan merendahkan)
- [x] Google OAuth live di produksi (`dataraga.my.id`) — catatan deploy penting di shared hosting (Rumah Web/cPanel):
  - `composer` tidak ada di PATH → pakai `composer.phar` lokal (`php composer-setup.php` lalu `php composer.phar install`)
  - Redirect URI Google **wajib** domain publik asli atau literal `localhost` — custom domain lokal (`cpss.test` dsb) selalu ditolak Google, dipakai `http://localhost:8002/...` untuk trial lokal
  - Redirect URI produksi harus persis `/auth/google/callback` (bukan `/auth/google/redirect`) di Google Cloud Console
- [x] Fix route `/prasarana/create`, `/clubs/create`, `/events/create` 404 — tertangkap route `show` karena constraint numerik dilepas untuk kode unik; dikecualikan lewat regex
- [x] Fix data wilayah (provinsi/kabupaten/kecamatan/desa) ternyata tidak pernah ikut migration — cuma ada di DB lokal. Ditambahkan `database/data/wilayah_seed.sql` (91.599 baris, aman diimpor ulang) supaya server manapun bisa diisi datanya
- [x] Fix peta Leaflet berantakan/tumpah keluar kotak di halaman detail yang dibuka dari dashboard — `layouts.app` belum punya `@stack('styles')` sehingga CSS Leaflet tidak pernah termuat di layout itu (JS-nya tetap jalan lewat `@stack('scripts')` yang sudah ada, makanya peta muncul tapi tanpa containment)
- [x] **Usulan Perubahan (Change Request)**: aturan akses edit Prasarana/Klub-Komunitas/Event — (1) belum divalidasi admin → pemilik bisa edit langsung, (2) sudah divalidasi → pemilik sendiri pun harus ajukan permintaan edit dulu, (3) bukan pemilik → tidak bisa edit langsung sama sekali (baik sudah maupun belum divalidasi), harus mengajukan permintaan. Form pengajuan hanya berisi kolom alasan (tanpa usulan nilai field baru — tidak ada auto-apply perubahan data). Admin meninjau di menu "Usulan Perubahan": disetujui → `status_validasi` data dikembalikan ke `pending` sehingga aturan `canEdit()` yang sudah ada otomatis membuka akses edit untuk **pemilik asli data** (bukan pengaju), lalu admin perlu validasi ulang setelah pemilik selesai memperbaiki; notifikasi ke pemilik baru dikirim setelah disetujui (bukan saat pengajuan masuk). Ditolak → wajib catatan alasan, notifikasi ke pengaju.
- [x] **Rombak field Prasarana — Kategori Olahraga jadi multi-pilih, Club/Komunitas dihapus**: alur lama menyimpan "Club / Komunitas" sebagai teks bebas di form Prasarana (arah link yang salah) dan "Kategori Olahraga" sebagai satu nilai teks saja padahal satu fasilitas sering multifungsi. Field Club/Komunitas dihapus total dari form Prasarana — arah pendaftaran yang benar sudah ada lewat form Club (field "Prasarana yang Digunakan" pada Club sudah bisa memilih Prasarana yang sudah dibuat lebih dulu). Kategori Olahraga diganti jadi checkbox multi-pilih dari tabel referensi `jenis_olahraga` yang sama dipakai Club (bisa pilih lebih dari satu cabang olahraga per fasilitas), lewat pivot table baru `prasarana_jenis_olahraga`. Data `kategori_olahraga` lama otomatis dipindah lewat migration (dicocokkan ke `jenis_olahraga` yang sudah ada, atau dibuatkan entri baru kalau tidak cocok) sebelum kolom lama (`kategori_olahraga`, `club_komunitas`) dihapus.
- [x] **Dashboard: Unduh CSV gabungan** — tombol baru di sebelah "Download Laporan PDF" (`DashboardController::laporanCsv()`), mengekspor semua data yang dilaporkan relawan (Prasarana/Event/Klub/Partisipasi) dalam satu CSV dengan kolom Jenis/Nama/Detail/Lokasi/Status/Relawan, memakai scoping yang sama dengan laporan PDF (relawan hanya lihat datanya sendiri, admin lihat semua).
- [x] **Urutan listing Prasarana/Klub-Komunitas/Event: belum divalidasi di depan** — `index()` ketiga controller diberi `orderByRaw("status_validasi = 'validated'")` sebelum `latest()`, jadi data pending/butuh-perbaikan selalu tampil duluan, data yang sudah di-acc digeser ke belakang.
- [x] **Aksi "Butuh Perbaikan" (reject dengan alasan) untuk Prasarana/Klub-Komunitas/Event** — sebelumnya admin cuma bisa "Verifikasi" (approve) tanpa opsi menolak/minta perbaikan seperti yang sudah ada di Kampung Olahraga. Ditambahkan `rejectPrasarana`/`rejectClub`/`rejectEvent` (set `status_validasi='rejected'` + `komentar_validasi` wajib diisi, notifikasi ke pelapor) dengan tombol & modal baru di dashboard masing-masing; status rejected otomatis bisa diedit lagi oleh pemilik lewat aturan `canEdit()` yang sudah ada (hanya validated yang terkunci). Halaman detail (show) menampilkan badge "Butuh Perbaikan" + catatan admin. Kampung Olahraga (yang sudah punya alur ini) diperkuat: `catatan_admin` kini wajib diisi dan pemilik mendapat notifikasi saat ditolak.
- [x] **Tombol Edit Kampung Olahraga untuk Super Admin** — sebelumnya tombol Edit di halaman detail Kampung Olahraga hanya muncul untuk pemilik (dan hanya sebelum tervalidasi); admin sama sekali tidak punya tombol edit di UI meski backend (`authorizeAccess()`) sudah mengizinkan admin. Ditambahkan tombol Edit khusus untuk Super Admin di blok aksi admin.
- [x] **Tombol Hapus Prasarana/Event untuk Admin & Super Admin** — sebelumnya `destroy()` di kedua controller hanya mengizinkan pemilik (sebelum divalidasi) atau Super Admin lewat `canEdit()`; admin biasa tidak bisa hapus sama sekali dan tidak ada tombol Hapus di listing dashboard. Guard diubah jadi `isAdmin() || canEdit()`, ditambahkan tombol ikon Hapus (dengan konfirmasi) di baris aksi dashboard Prasarana & Event.

---

## 5. DATA DUMMY & TESTING

### 5.1 Akun Dummy (password: `password`)
- [x] 8 akun: 1 Super Admin, 2 Admin, 5 Relawan
- [x] Data wilayah berbeda per akun (Banyuwangi & Jember)

### 5.2 Data Lapangan Dummy
- [x] 5 Prasarana (3 Banyuwangi, 2 Jember)
- [x] 3 Clubs
- [x] 4 Events
- [x] 5 Partisipasi
- [x] 21 Kehadiran individu

### 5.3 Uji Fungsional
- [x] Struktur wilayah ✅
- [x] Akses publik (guest) ✅
- [x] Presensi sederhana ✅
- [x] Gamifikasi (poin, batas, pembatalan) ✅
- [x] Lencana otomatis ✅
- [x] Leaderboard ✅
- [x] UI/UX review (20 Juni 2026) ✅

---

## 6. BACKLOG & PENDING (Daftar Tugas Belum / Akan Dikerjakan)

### 6.1 Bugfix & Polish (Priority: High)
- [x] Commit semua uncommitted changes ke git (7 commits terpisah)
- [ ] Review perubahan `app.blade.php` — cek apakah layout masih konsisten
- [ ] Cek routes `web.php` — bersihkan route duplikat atau tidak terpakai
- [ ] Fix validasi form Prasarana: bintang rating harus nullable atau required?

### 6.2 Fitur Minor (Priority: Medium)
- [x] Halaman publik: filter & search untuk Prasarana/Club/Event
- [x] Notifikasi inbox-style untuk poin & lencana
- [x] Kalender terintegrasi event & jadwal latihan
- [ ] Export data (PDF/Excel) untuk admin
- [ ] Halaman profil relawan dengan statistik kontribusi
- [ ] Foto tambahan (multiple upload) untuk Prasarana

### 6.3 Validasi & Testing Lanjutan (Priority: Medium)
- [ ] Uji coba login dengan semua 8 akun dummy
- [ ] Uji coba guest access di semua route publik
- [ ] Uji coba gamifikasi: semua skenario batas poin
- [ ] Uji coba validasi admin: approve & cancel
- [ ] Cross-browser testing (Chrome, Firefox, Safari mobile)

### 6.4 Dokumentasi (Priority: Low)
- [ ] Update `prd.md` ke versi terbaru (v2.2)
- [ ] Buat panduan pengguna singkat untuk relawan
- [ ] Buat panduan admin untuk validasi data

### 6.5 Pengembangan Lanjutan (Outside Fase 1 Scope)
- [ ] Notifikasi in-app real-time
- [x] Verifikasi lokasi via QR Code scanner (Kampung Olahraga, per-fasil sejak v2)
- [ ] Upload video
- [ ] Forum/komunikasi komunitas
- [ ] PWA / mode offline
- [ ] Integrasi NIK / Satu Data Indonesia
- [ ] Backend big data (Kafka/Storm/HBase)

---

## 7. FILE YANG PERLU DIPERHATIKAN

### File Baru (Untracked)
```
app/Http/Controllers/KehadiranController.php
app/Http/Controllers/RelawanController.php
app/Http/Controllers/WilayahController.php
app/Models/JenisOlahraga.php
database/migrations/2026_06_20_080000_add_komentar_validasi_to_tables.php
database/migrations/2026_06_24_120600_create_wilayah_table.php
database/migrations/2026_06_24_120811_add_provinsi_to_users_and_partisipasi.php
database/migrations/2026_06_24_121159_add_qr_token_to_partisipasi.php
database/migrations/2026_06_24_124344_add_sumber_to_kehadiran.php
database/migrations/2026_06_24_140200_add_jenis_olahraga_and_rpe_to_kehadiran.php
database/migrations/2026_06_24_150000_create_jenis_olahraga_table.php
resources/views/components/wilayah-dropdown.blade.php
resources/views/partisipasi/daftar.blade.php
resources/views/partisipasi/qr.blade.php
resources/views/relawan/
```

### File Modified (Belum Commit)
```
SESSION_LOG.md
app/Http/Controllers/ClubController.php
app/Http/Controllers/EventController.php
app/Http/Controllers/LeaderboardController.php
app/Http/Controllers/PartisipasiController.php
app/Http/Controllers/PrasaranaController.php
app/Models/Club.php
app/Models/Event.php
app/Models/Kehadiran.php
app/Models/Partisipasi.php
app/Models/Prasarana.php
app/Models/User.php
app/Services/GamificationService.php
database/seeders/DatabaseSeeder.php
database/seeders/DummyDataSeeder.php
prd.md
resources/views/clubs/create.blade.php
resources/views/clubs/edit.blade.php
resources/views/clubs/index.blade.php
resources/views/dashboard.blade.php
resources/views/events/create.blade.php
resources/views/events/edit.blade.php
resources/views/events/index.blade.php
resources/views/layouts/app.blade.php
resources/views/leaderboard/index.blade.php
resources/views/leaderboard/my-points.blade.php
resources/views/partisipasi/create.blade.php
resources/views/partisipasi/edit.blade.php
resources/views/partisipasi/index.blade.php
resources/views/partisipasi/show.blade.php
resources/views/prasarana/create.blade.php
resources/views/prasarana/edit.blade.php
resources/views/prasarana/index.blade.php
resources/views/prasarana/show.blade.php
resources/views/talenta/create.blade.php
resources/views/talenta/edit.blade.php
resources/views/talenta/index.blade.php
resources/views/talenta/show.blade.php
resources/views/tenaga-ahli/index.blade.php
resources/views/users/index.blade.php
resources/views/welcome.blade.php
routes/web.php
```

---

## 8. PERINTAH YANG PERLU DIJALANKAN

### Setup Awal / Fresh Install
```bash
# Install dependencies
composer install
npm install

# Environment
copy .env.example .env
php artisan key:generate

# Database
php artisan migrate:fresh --seed
```

### Update (Jika Sudah Ada Data)
```bash
php artisan migrate
php artisan db:seed --class=DummyDataSeeder
```

### Clear Cache (Jika UI aneh)
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Jalankan Server
```bash
php artisan serve --port=8001
npm run dev
```

---

## 9. AKUN DEFAULT

| Email | Password | Role | Wilayah |
|-------|----------|------|---------|
| superadmin@cpss.test | password | Super Admin | Sumber Sehat / Tegalsari / Banyuwangi |
| admin@cpss.test | password | Admin | Genteng Kulon / Genteng / Banyuwangi |
| admin.jember@cpss.test | password | Admin | Kalisat / Kalisat / Jember |
| relawan@cpss.test | password | Relawan | Sumber Agung / Glagah / Banyuwangi |
| siti.aminah@cpss.test | password | Relawan | Tamansari / Banyuwangi / Banyuwangi |
| ahmad.hidayat@cpss.test | password | Relawan | Jajag / Gambiran / Banyuwangi |
| rina.wulandari@cpss.test | password | Relawan | Gumukmas / Gumukmas / Jember |
| dedi.kurniawan@cpss.test | password | Relawan | Tempurejo / Tempurejo / Jember |

---

## 10. CATATAN RAPAT / DECISION LOG

| Tanggal | Keputusan |
|---------|-----------|
| 20 Juni 2026 | Fase 1 scope dikunci: Gamifikasi, Wilayah, Akses Publik, Presensi |
| 20 Juni 2026 | MFA dihapus karena kompleksitas tidak proporsional |
| 20 Juni 2026 | QR, Video, Forum, PWA dicadangkan ke Bab V (Saran) |
| 27 Juni 2026 | Form Prasarana diupdate dengan star rating interaktif |
| 27 Juni 2026 | Implementasi filter/search publik + notifikasi inbox + kalender |
| 27 Juni 2026 | Semua perubahan di-commit terpisah (7 commits) |
| 20 Juli 2026 | Kampung Olahraga di-redesign: QR per-fasil (bukan per-kampung), integrasi Klub/Komunitas, Google OAuth, leaderboard kampung & klub, poin kampung masuk GamificationService |
| 20 Juli 2026 | Partisipasi diarsipkan (bukan dihapus) — digantikan check-in QR Kampung Olahraga sebagai sumber data partisipasi resmi |
| 21 Juli 2026 | URL publik pindah dari id berurutan ke kode unik acak 10 karakter (anti tebak-tebakan/enumerasi) |
| 21 Juli 2026 | Dashboard relawan dibatasi per wilayah sendiri (kabupaten/kecamatan); tombol Verifikasi disembunyikan dari relawan, hanya admin |
| 21 Juli 2026 | Kampung Olahraga boleh didaftarkan per RT/RW agar tidak terbatas satu per desa |
| 21 Juli 2026 | Google OAuth berhasil live di produksi (dataraga.my.id) setelah fix composer.phar + redirect URI di hosting Rumah Web |
| 21 Juli 2026 | Data wilayah (91k baris) ternyata tidak pernah ter-migrate, hanya ada di lokal — ditambahkan sebagai file seed SQL di repo |
| 21 Juli 2026 | Tambah fitur Usulan Perubahan: data belum divalidasi → pemilik bisa edit langsung; sudah divalidasi (termasuk oleh pemilik sendiri) atau bukan pemilik → wajib ajukan permintaan (+alasan); disetujui admin → status kembali pending & akses edit terbuka untuk pemilik asli (bukan pengaju) |
| 21 Juli 2026 | Field Club/Komunitas dihapus dari form Prasarana (arah link yang benar sudah ada di form Club); Kategori Olahraga di Prasarana diganti jadi multi-pilih dari tabel jenis_olahraga (satu fasilitas bisa multifungsi) |
| 22 Juli 2026 | Tambah Unduh CSV gabungan di dashboard; urutkan listing Prasarana/Klub/Event supaya yang belum di-acc tampil duluan; tambah aksi "Butuh Perbaikan" (reject + alasan wajib) untuk Prasarana/Klub/Event mengikuti pola Kampung Olahraga; tambah tombol Edit Kampung Olahraga untuk Super Admin |

---

> **Catatan:** Setiap kali pindah port/session, baca file ini terlebih dahulu untuk sinkronisasi status.
> Tandai `[~]` saat mulai mengerjakan, dan `[x]` saat selesai.
