# PRD Addendum v3.0 — Fase 1.5 & Fase 2

> **Dokumen ini adalah kelanjutan dari `prd.md` (v2.1).**
> Ditulis berdasarkan gap analysis pasca-implementasi Fase 1 (per 17 Juli 2026).
> Fase 1 (4 fitur wajib) telah selesai diimplementasi. Dokumen ini mengatur:
> - **Fase 1.5** — Polish & hardening sebelum pengujian pakar (H1/H2)
> - **Fase 2** — Pengembangan lanjutan pasca-penelitian

---

## 1. Status Fase 1 — Selesai ✅

Seluruh 4 fitur prioritas pengembangan awal yang diwajibkan PRD v2.1 telah
diimplementasi dan lulus uji fungsional internal (20 Juni 2026):

| Fitur | Status |
|-------|--------|
| Struktur Wilayah Administratif (desa/kecamatan/kabupaten) | ✅ Selesai |
| Akses Publik Tanpa Login (Prasarana, Clubs, Events) | ✅ Selesai |
| Presensi Sederhana — Kehadiran Individu per Partisipasi | ✅ Selesai |
| Modul Gamifikasi — Poin, Leaderboard, Lencana | ✅ Selesai |

Fondasi teknis yang ada: RBAC, Audit Log, Dashboard Chart.js, manajemen
Prasarana/Club/Event/Partisipasi/Talenta/Tenaga Ahli, Leaflet Map,
QR Token kehadiran, 8 akun dummy + data sampel Banyuwangi & Jember.

---

## 2. Analisis Gap Pasca-Fase 1

### 2.1 Temuan Survei yang Belum Terakomodasi Sepenuhnya

Dari 476 responden TPON (Mei–Juni 2026), frekuensi kebutuhan berikut
muncul di atas ambang signifikan namun belum diwadahi secara penuh:

| Kebutuhan | Frekuensi Survei | Status Saat Ini |
|-----------|-----------------|-----------------|
| Fitur Jadwal/Kalender | 177 + 14 = **191** | ⚠️ Partial — hanya list Events & jadwal Club terpisah |
| Notifikasi | 18 + 66 = **84** | ❌ Belum ada |
| Filter & Search | Implisit di "kemudahan" (363) | ❌ Belum ada di halaman publik |
| Foto dokumentasi (multiple) | 44 | ❌ Hanya single upload |
| Dashboard/Grafik lebih lengkap | 40 + 50 = **90** | ⚠️ Ada, belum per-relawan |
| Export Data | — | ❌ Belum ada |

### 2.2 Kelompok Teknis yang Belum Diselesaikan

| Item | Kategori | Prioritas |
|------|----------|-----------|
| Filter/Search halaman publik | UX kritis | High |
| Konfirmasi poin via flash message | Umpan balik gamifikasi | High |
| Fix validasi star rating Prasarana | Bugfix | High |
| Testing formal semua skenario | QA pre-pakar | High |
| Commit semua uncommitted changes | Teknis | High |
| Halaman profil relawan + statistik | Completeness gamifikasi | Medium |
| Export data PDF/Excel untuk Admin | Kebutuhan kebijakan | Medium |
| Multiple upload foto Prasarana | Dokumentasi lapangan | Low |

### 2.3 Blind Spot Roadmap

**Fitur Jadwal/Kalender Terintegrasi** adalah satu-satunya kebutuhan
survei masuk top-5 frekuensi (191 kemunculan) yang **tidak masuk dalam
Fase 1 maupun daftar Fase 2 roadmap** PRD v2.1. Ini perlu keputusan
eksplisit: diimplementasikan sekarang atau didokumentasikan sebagai saran
di Bab V penelitian.

---

## 3. Fase 1.5 — Pre-Expert Testing Polish

**Scope:** Penyempurnaan yang harus selesai sebelum sesi pengujian pakar
(uji hipotesis H1/H2). Tidak menambah fitur besar baru.

### 3.1 Filter & Search Halaman Publik

**Justifikasi:** "Kemudahan penggunaan" adalah nilai tertinggi survei
(4,74/5 Likert; 363 kemunculan terbuka). Halaman publik tanpa filter
memaksa pengguna scroll semua data — bertentangan dengan temuan ini.

**Cakupan implementasi:**
- Halaman `prasarana/index` (publik): filter berdasarkan **kabupaten**,
  **kondisi** (Baik/Sedang/Buruk), dan **cabang olahraga**; kolom
  pencarian nama/alamat.
- Halaman `clubs/index` (publik): filter berdasarkan **kabupaten** dan
  **cabang olahraga**; pencarian nama klub.
- Halaman `events/index` (publik): filter berdasarkan **kabupaten** dan
  **tingkat event** (Desa/Kecamatan/Kabupaten); pencarian judul event.

**Pendekatan teknis:** Filter via query string (GET), diproses di
Controller dengan `when()` Eloquent — tidak perlu JavaScript khusus,
Alpine.js cukup untuk toggle panel filter.

**Contoh URL:** `/prasarana?kabupaten=Banyuwangi&kondisi=Baik`

**Acceptance criteria:**
- Filter dan search bisa dikombinasikan.
- Hasil kosong menampilkan pesan "Belum ada data yang sesuai filter."
- URL filter shareable (bookmarkable).

---

### 3.2 Konfirmasi Poin — Flash Notification Gamifikasi

**Justifikasi:** PRD Section 5.2 (Alur Gamifikasi) menyebutkan
"menampilkan notifikasi singkat di antarmuka (contoh: +50 poin — Laporan
Prasarana baru)" setelah entri berhasil disimpan. Saat ini flash message
hanya menampilkan konfirmasi CRUD generik, bukan informasi poin.

**Cakupan implementasi:**
- Setelah `store()` pada PrasaranaController, ClubController,
  EventController, PartisipasiController — jika poin dikreditkan,
  tambahkan key `poin_diperoleh` ke session flash.
- Komponen Blade `<x-poin-toast />` — banner hijau kecil non-blocking
  di sudut kanan atas, auto-dismiss 4 detik via Alpine.js.
- Format pesan: `"+{N} poin — {label_aksi}"`, misalnya
  "+50 poin — Prasarana baru berhasil dilaporkan 🎉"
- Apabila poin tidak dikreditkan (sudah pernah dapat poin di entitas yang
  sama), toast tidak ditampilkan — tidak perlu menjelaskan alasannya ke
  pengguna.

**Acceptance criteria:**
- Toast muncul setelah submit laporan yang menghasilkan poin.
- Toast tidak muncul untuk aksi yang tidak menghasilkan poin.
- Toast dapat ditutup manual sebelum auto-dismiss.

---

### 3.3 Halaman Profil Relawan

**Justifikasi:** Gamifikasi menurut Self-Determination Theory membutuhkan
umpan balik personal (mastery). `my-points` sudah ada untuk riwayat
transaksi. Halaman profil melengkapi dengan **gambaran identitas dan
kontribusi**. PRD Section 10.3 juga menyebutkan relawan harus bisa
melihat peringkat pribadinya di luar 10 besar.

**Cakupan implementasi:**

URL: `/profil` (hanya authenticated)

Konten:
| Blok | Isi |
|------|-----|
| Header | Nama, wilayah tugas, role, tanggal bergabung |
| Statistik Kontribusi | Total prasarana, klub, event, partisipasi yang pernah diinput |
| Poin & Peringkat | Total poin, peringkat saat ini (minggu/bulan), jumlah relawan aktif sebagai konteks |
| Lencana | Tampilan grid semua lencana yang dimiliki (earned) dan yang belum (locked/greyed-out) |
| Aktivitas Terakhir | 5 aktivitas terakhir (sama dengan riwayat di my-points, tapi diringkas) |

**Database:** Tidak perlu tabel baru. Query dari `point_transactions`,
`user_badges`, dan aggregate dari masing-masing modul.

**Acceptance criteria:**
- Halaman bisa diakses oleh Relawan dan Admin (melihat profil sendiri).
- Lencana terkunci ditampilkan grayscale dengan tooltip syarat perolehan.
- Peringkat personal tampil meski di luar 10 besar leaderboard.

---

### 3.4 Bugfix: Validasi Star Rating Prasarana

**Deskripsi bug:** Form create/edit Prasarana memiliki 8 komponen star
rating (lantai, ring, net, gawang, lapangan, ventilasi, pencahayaan,
kamar mandi). Belum jelas apakah nilainya **required** atau **nullable**.
Jika required, pengguna yang tidak mengisi akan gagal submit tanpa pesan
error yang jelas. Jika nullable, nilai default yang disimpan ke DB bisa 0
atau `null`, yang merusak perhitungan rata-rata kondisi.

**Keputusan:** Rating setiap komponen bersifat **nullable** (tidak semua
fasilitas memiliki ring atau gawang). Jika dikosongkan, disimpan sebagai
`null` dan tidak diikutkan dalam perhitungan rata-rata kondisi.
`average_condition` dihitung hanya dari komponen yang diisi.

**Cakupan fix:**
- Validasi Controller: ubah `required|integer|min:1|max:5` menjadi
  `nullable|integer|min:1|max:5` untuk semua 8 kolom rating.
- Model `Prasarana`: update method `getAverageConditionAttribute()` untuk
  mengabaikan nilai `null` saat menghitung rata-rata.
- View: tambahkan label "(Opsional)" di bawah setiap grup bintang pada
  form create/edit.

---

### 3.5 Testing Formal Pra-Pakar

Skenario berikut harus diuji dan didokumentasikan hasilnya sebelum
pengujian pakar:

#### A — Autentikasi & Akses
| # | Skenario | Expected |
|---|----------|----------|
| A1 | Login semua 8 akun dummy | Berhasil masuk sesuai role |
| A2 | Guest akses `/prasarana`, `/clubs`, `/events` | Halaman terbuka tanpa login |
| A3 | Guest coba akses `/prasarana/create` | Redirect ke halaman login |
| A4 | Relawan coba akses `/dashboard` (admin-only) | Redirect / 403 |
| A5 | Admin coba delete data milik admin lain | Forbidden |

#### B — Gamifikasi
| # | Skenario | Expected |
|---|----------|----------|
| B1 | Relawan buat Prasarana baru → poin +50 | PointTransaction terbuat, total_poin bertambah |
| B2 | Relawan buat Prasarana ke-2 → poin +50 lagi | Poin bertambah (entitas berbeda) |
| B3 | Relawan update Prasarana yang sama dalam 30 hari | Poin update TIDAK dikreditkan |
| B4 | Relawan update Prasarana yang sama setelah 30 hari | Poin update +15 dikreditkan |
| B5 | Relawan lapor partisipasi di lokasi+tanggal yang sama dua kali | Poin hanya sekali |
| B6 | Admin batalkan poin → total_poin berkurang | PointTransaction status = dibatalkan |
| B7 | Relawan capai 500 poin | Lencana "Pahlawan Data Olahraga" muncul |
| B8 | Relawan lapor 5 prasarana unik | Lencana "Penjaga Sarpras" muncul |

#### C — Validasi Data (Alur Verifikasi)
| # | Skenario | Expected |
|---|----------|----------|
| C1 | Admin validasi Prasarana pending | Status berubah validated, audit log terbuat |
| C2 | Admin tambah komentar validasi | Komentar tersimpan dan tampil di show |
| C3 | Super Admin batalkan poin setelah data tidak valid | Audit log mencatat aksi pembatalan |

#### D — Wilayah & Filter (Pasca-3.1)
| # | Skenario | Expected |
|---|----------|----------|
| D1 | Filter prasarana by kabupaten | Hanya tampil data kabupaten tersebut |
| D2 | Search nama klub | Hasil sesuai keyword |
| D3 | Filter + search kombinasi | Keduanya berjalan bersamaan |

#### E — Presensi
| # | Skenario | Expected |
|---|----------|----------|
| E1 | Tambah kehadiran individu ke partisipasi | Tersimpan, tampil di tabel show |
| E2 | Lihat QR kehadiran | QR token tampil dan bisa di-scan |

---

## 4. Kalender Terintegrasi — Keputusan & Spesifikasi

### 4.1 Konteks & Keputusan

Fitur Jadwal/Kalender muncul **191 kali** dalam survei terbuka (frekuensi
tertinggi ke-2 di "Fitur yang Paling Dibutuhkan"). Namun PRD v2.1 tidak
mencantumkannya dalam roadmap maupun daftar Pengembangan Lanjutan.

**Keputusan:** Fitur ini **diimplementasikan sebagai bagian Fase 1.5**
karena:
1. Biaya implementasi rendah — data Events dan jadwal Club sudah ada di DB.
2. Tidak memerlukan tabel baru.
3. Memperkuat justifikasi empiris pengujian UTAUT (Facilitating Conditions
   dan Performance Expectancy) karena langsung menjawab kebutuhan #2
   survei.
4. Dapat berfungsi sebagai **halaman publik** — memperluas akses tanpa
   login.

### 4.2 Spesifikasi Teknis

**URL:** `/kalender` (publik, tidak perlu login)

**Sumber data:**
- Tabel `events` — setiap event memiliki `tanggal_mulai` dan
  `tanggal_selesai` (atau `tanggal`).
- Tabel `jadwal_latihan` (relasi ke `clubs`) — berisi hari (`hari`) dan
  jam (`jam_mulai`, `jam_selesai`).

**Tampilan:**
- Kalender grid bulanan responsif (pure CSS + Alpine.js, tanpa library
  eksternal berat).
- Setiap hari yang memiliki event/jadwal ditandai titik warna.
- Klik hari → panel samping atau modal kecil menampilkan daftar
  event/jadwal di hari tersebut.
- Navigasi bulan (← Bulan Sebelumnya | Nama Bulan Tahun | Bulan Berikutnya →).
- Legend warna: Biru = Event, Hijau = Jadwal Latihan Klub.

**Filter opsional (v1):**
- Dropdown kabupaten (untuk menyesuaikan data dengan wilayah pengunjung).

**Schema tambahan (opsional jika belum ada):**

Cek apakah kolom `tanggal_mulai` dan `tanggal_selesai` sudah ada di tabel
`events`. Jika hanya ada kolom `tanggal` (single date), tambah migration:
```sql
ALTER TABLE events
  ADD COLUMN tanggal_mulai date AFTER tanggal,
  ADD COLUMN tanggal_selesai date AFTER tanggal_mulai;
```
Lakukan backfill: `tanggal_mulai = tanggal`, `tanggal_selesai = tanggal`.

**Controller:** `KalenderController@index`
- Query events di rentang bulan yang dipilih.
- Query jadwal_latihan (expand jadwal mingguan ke tanggal konkret dalam
  bulan yang dipilih).
- Return JSON ke view (dirender oleh Alpine.js) atau Blade biasa.

**Acceptance criteria:**
- Kalender bisa diakses tanpa login.
- Event dan jadwal klub tampil di hari yang tepat.
- Navigasi antar bulan berfungsi.
- Klik hari menampilkan detail.
- Mobile-friendly (grid 7 kolom, font kecil, touch-friendly).

---

## 5. Export Data Admin

### 5.1 Justifikasi

PRD Section 5.1 (Alur Utama) menyebutkan Admin menggunakan data sebagai
"bahan rapat kebijakan". Tanpa export, Admin harus screenshot tabel atau
copy-paste manual — ini bertentangan dengan tujuan sistem sebagai *single
source of truth*.

### 5.2 Cakupan

| Modul | Format | Kolom Minimal |
|-------|--------|---------------|
| Prasarana | PDF / Excel | Nama, Lokasi, Wilayah, Kondisi Rata-rata, Status, Tanggal Input |
| Partisipasi | PDF / Excel | Tanggal, Lokasi, Wilayah, Jenis Olahraga, Jumlah Estimasi, Kehadiran Individu |
| Clubs | PDF / Excel | Nama, Wilayah, Cabang Olahraga, Status Aktif, Jadwal Latihan |
| Events | PDF / Excel | Judul, Tanggal, Lokasi, Wilayah, Tingkat Event |
| Leaderboard | PDF | Peringkat, Nama Relawan, Wilayah, Total Poin (periode terpilih) |

### 5.3 Pendekatan Teknis

Gunakan package **Laravel Excel** (`maatwebsite/excel`) untuk Excel (.xlsx)
dan **DomPDF** (`barryvdh/laravel-dompdf`) untuk PDF.

- Route: `GET /prasarana/export?format=xlsx&kabupaten=...` (diproteksi
  middleware `auth` + role Admin/Super Admin).
- Controller method `export()` terpisah per modul.
- Filter yang sedang aktif di halaman index turut diaplikasikan ke export
  (apa yang terlihat = apa yang diexport).

### 5.4 Acceptance Criteria

- Export hanya tersedia untuk Admin dan Super Admin.
- File yang didownload dinamai otomatis: `prasarana_banyuwangi_2026-07.xlsx`.
- Data terfilter ikut teraplikasi di export.
- PDF memiliki header nama sistem, tanggal export, dan filter yang digunakan.

---

## 6. Multiple Upload Foto Prasarana

### 6.1 Justifikasi

Foto muncul 44 kali di survei sebagai fitur yang dibutuhkan. Saat ini
Prasarana hanya menyimpan satu foto. Kondisi lapangan lapangan seringkali
perlu didokumentasikan dari beberapa sudut (depan, dalam, kondisi kerusakan).

### 6.2 Spesifikasi

- Tambah tabel `prasarana_photos`:

```
| Kolom       | Tipe     | Keterangan            |
|-------------|----------|-----------------------|
| id          | PK       | Auto-increment        |
| prasarana_id| FK       | Relasi ke prasarana   |
| path        | string   | Path file di storage  |
| urutan      | integer  | Urutan tampil (1-5)   |
| created_at  | timestamp|                       |
```

- Maksimum 5 foto per prasarana (batasan penyimpanan).
- Foto pertama (urutan = 1) digunakan sebagai thumbnail di index.
- Form: multiple file input dengan preview sebelum upload.
- Existing single-photo column (`foto`) dipertahankan sebagai fallback
  sampai semua data termigrasi.

---

## 7. Fase 2 — Pengembangan Lanjutan (Pasca-Penelitian)

Fitur-fitur berikut didokumentasikan sebagai arah pengembangan pada
**Bab V (Saran)** laporan penelitian. Implementasi di luar lingkup
pengujian hipotesis H1/H2.

### 7.1 Notifikasi In-App

**Kebutuhan survei:** 66 kemunculan di "fitur paling dibutuhkan".

Mekanisme yang diusulkan:
- Tabel `notifications` (standar Laravel `notifiable`).
- Event-event pemicu: poin dikreditkan, data divalidasi admin, data
  dibatalkan admin, lencana baru diperoleh.
- Tampilan: badge counter di navbar + panel dropdown riwayat notifikasi.
- Opsional: integrasi WhatsApp API (via Fonnte/Wablas) untuk notifikasi
  di luar aplikasi.

### 7.2 Verifikasi Lokasi via QR Code (Titik Fasilitas)

**Kebutuhan survei:** 22 kemunculan "verifikasi QR".

Mekanisme yang diusulkan:
- Setiap Prasarana memiliki QR code statis yang dapat dicetak dan
  dipasang di lokasi fisik.
- Relawan scan QR → sistem mencatat bahwa relawan benar-benar berada
  di lokasi saat melaporkan.
- Menambah satu layer validitas data lapangan.

**Catatan:** QR Token kehadiran pada modul Partisipasi sudah
diimplementasikan (Juni 2026) dan bisa dijadikan referensi pola yang sama.

### 7.3 Unggah Video Pendek

**Kebutuhan survei:** 39 kemunculan.

- Maksimum durasi 30 detik, ukuran 50 MB.
- Disimpan di object storage (S3-compatible) untuk efisiensi.
- Tersedia di modul Prasarana dan Partisipasi sebagai pelengkap foto.

### 7.4 Forum / Kanal Komunikasi Komunitas

**Kebutuhan survei:** 34 + 33 = 67 kemunculan (komunitas/komunikasi).

- Kanal diskusi per wilayah (kabupaten) untuk relawan.
- Bukan media sosial penuh — hanya thread diskusi ringan.
- Moderasi oleh Admin wilayah.

### 7.5 Progressive Web App (PWA) — Mode Luring

**Kebutuhan survei:** 20 kemunculan "mode offline".

- Service Worker untuk caching halaman yang sering diakses.
- Form input data bisa diisi offline, disinkronisasi saat kembali online.
- Prioritas rendah: 93,3% responden sudah menyatakan akses internet memadai.

### 7.6 Integrasi NIK / Satu Data Indonesia

- Validasi kehadiran individu menggunakan NIK (e-KTP).
- Memerlukan izin dan integrasi Dukcapil — kompleksitas legal tinggi.
- Relevan untuk skala implementasi nasional pasca-penelitian.

### 7.7 Backend Big Data (Skala Nasional)

- Apache Kafka untuk streaming laporan real-time.
- Apache Storm untuk pemrosesan event.
- HBase / Cassandra untuk penyimpanan time-series skala besar.
- Relevan sebagai gambaran arsitektur konseptual pada Bab II Kajian Pustaka.

---

## 8. Database Schema Tambahan

### 8.1 Tabel Baru

#### `prasarana_photos`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | PK | Auto-increment |
| prasarana_id | FK → prasarana | Prasarana pemilik foto |
| path | string | Path relatif di storage |
| urutan | tinyint, default 1 | Urutan tampil (1=utama) |
| created_at, updated_at | timestamp | Standar Laravel |

### 8.2 Kolom Baru pada Tabel Existing

| Tabel | Kolom Baru | Tipe | Keterangan |
|-------|-----------|------|------------|
| events | tanggal_mulai | date, nullable | Tanggal awal event (untuk kalender) |
| events | tanggal_selesai | date, nullable | Tanggal akhir event (untuk kalender multi-hari) |

> **Catatan migrasi events:** Jika tabel sudah memiliki kolom `tanggal`
> (single date), backfill `tanggal_mulai = tanggal` dan biarkan
> `tanggal_selesai = null` untuk event satu hari.

---

## 9. Checklist Roadmap Lengkap

### Fase 1 — Selesai ✅
- [x] Struktur Wilayah Administratif
- [x] Akses Publik Tanpa Login (Prasarana, Clubs, Events)
- [x] Presensi Sederhana (Kehadiran Individu)
- [x] Modul Gamifikasi (Poin, Leaderboard, Lencana)

### Fase 1.5 — Pra-Pengujian Pakar
- [ ] **3.1** Filter & Search halaman publik (Prasarana, Clubs, Events)
- [ ] **3.2** Flash notification poin gamifikasi (`<x-poin-toast />`)
- [ ] **3.3** Halaman profil relawan (`/profil`)
- [ ] **3.4** Bugfix validasi star rating Prasarana (nullable rating)
- [ ] **3.5.A** Testing autentikasi & akses (5 skenario)
- [ ] **3.5.B** Testing gamifikasi (8 skenario)
- [ ] **3.5.C** Testing validasi data (3 skenario)
- [ ] **3.5.D** Testing filter & search (3 skenario)
- [ ] **3.5.E** Testing presensi (2 skenario)
- [ ] **4** Kalender Terintegrasi (`/kalender`, publik)
- [ ] **Git:** Commit semua uncommitted changes

### Fase 2 — Pasca-Penelitian (Bab V Saran)
- [ ] Export Data PDF/Excel untuk Admin
- [ ] Multiple Upload Foto Prasarana (maks 5 foto)
- [ ] Notifikasi In-App (poin, validasi, lencana)
- [ ] Verifikasi Lokasi via QR Code (titik fasilitas)
- [ ] Unggah Video Pendek (maks 30 detik / 50 MB)
- [ ] Forum/Kanal Komunikasi Komunitas
- [ ] Progressive Web App — Mode Luring
- [ ] Integrasi NIK / Satu Data Indonesia
- [ ] Backend Big Data (Kafka/Storm/HBase)

---

## 10. Gap Analysis — Diperbarui

### 10.1 Fitur yang SUDAH ADA ✅
| Fitur | Keterangan |
|-------|------------|
| RBAC (Super Admin, Admin, Relawan) | 3 level akses sesuai hierarki |
| Audit Log | Pencatatan mutasi data otomatis di semua modul |
| Dashboard Chart.js | Grafik tren partisipasi, prasarana, statistik kartu |
| Manajemen Prasarana | Rating bintang 1-5, Leaflet Map, single foto, status validasi |
| Manajemen Club/Komunitas | CRUD + jadwal latihan dinamis + logo |
| Manajemen Event | Multi-tingkat (Desa–Kabupaten) |
| Manajemen Partisipasi | Estimasi jumlah + presensi individu + QR token |
| Manajemen Talenta & Tenaga Ahli | Database bibit atlet & pelatih |
| Struktur Wilayah Administratif | Cascading dropdown desa/kecamatan/kabupaten |
| Akses Publik Tanpa Login | Prasarana, Clubs, Events (index & show) |
| Modul Gamifikasi | Poin 6 kategori, Leaderboard 3 periode, 4 lencana otomatis |
| Presensi Sederhana | Kehadiran individu per sesi partisipasi |
| Responsive Design | Tailwind + mobile-friendly |

### 10.2 Fitur Fase 1.5 — Perlu Dibangun ⚠️
| # | Fitur | Justifikasi |
|---|-------|-------------|
| 1 | Filter & Search halaman publik | Kemudahan akses — nilai survei 4,74/5 |
| 2 | Flash notification poin | Umpan balik gamifikasi (spec PRD v2.1 Sec. 5.2) |
| 3 | Halaman profil relawan | Completeness NMIPS — mastery feedback |
| 4 | Bugfix star rating nullable | Stabilitas form sebelum uji pakar |
| 5 | Kalender terintegrasi | Survei #2 tertinggi (191 kemunculan), blind spot roadmap |

### 10.3 Fitur Fase 2 — Di Luar Lingkup Penelitian
| Fitur | Frekuensi Survei | Alasan Postpone |
|-------|-----------------|-----------------|
| Export Data | — | Non-prasyarat H1/H2; berguna pasca-penelitian |
| Multiple foto Prasarana | 44 | Single foto cukup untuk validasi pakar |
| Notifikasi in-app | 84 | Non-prasyarat H1/H2; tampilan dashboard cukup sementara |
| QR Verifikasi Lokasi | 22 | GPS sudah cukup untuk skala pengembangan awal |
| Upload video | 39 | Foto memadai; video tambah beban penyimpanan |
| Forum komunitas | 67 | Engagement jangka panjang, di luar cakupan 2 bulan |
| PWA/Offline | 20 | 93,3% responden internet sudah memadai |
| Integrasi NIK | — | Kompleksitas legal tidak proporsional skala awal |
| Big Data Backend | — | Arsitektur konseptual nasional, bukan skala penelitian |

---

## 11. Rekomendasi untuk Bab V Disertasi

1. **Cantumkan Kalender Terintegrasi sebagai fitur yang diimplementasikan**
   (bukan saran) — frekuensi survei 191 terlalu tinggi untuk diabaikan,
   dan biaya implementasi rendah.

2. **Jadikan Export Data sebagai saran utama** — secara langsung
   mendukung pemanfaatan data untuk kebijakan daerah, yang merupakan misi
   utama CPSS.

3. **Jadikan Notifikasi In-App sebagai saran kedua** — 84 kemunculan
   survei + relevan terhadap dimensi Performance Expectancy (UTAUT).

4. **Dokumentasikan keterbatasan single foto** — reviewer mungkin
   mempertanyakan validitas data lapangan; jelaskan bahwa foto tunggal
   cukup untuk pengujian mekanisme, bukan untuk audit fasilitas penuh.

5. **Cantumkan PWA sebagai saran meski urgensi empiris rendah** —
   tetap relevan secara teoretis untuk skenario daerah dengan infrastruktur
   internet terbatas.

---

## 12. Referensi Dokumen

1. **`prd.md` (v2.1)** — PRD utama, terakhir diupdate 20 Juni 2026.
2. **`PROGRESS.md`** — Checklist implementasi aktual, terakhir 27 Juni 2026.
3. **Interpretasi_Hasil_Survei_TPON.docx** — Analisis tematik 476 responden.
4. **Kajian_Prioritas_Implementasi_CPSS.docx** — Kriteria penyaringan kebutuhan.
5. **PRD_Addendum_Gamifikasi_CPSS.docx** — Spesifikasi teknis gamifikasi.

---

*Document Version: 3.0*
*Dibuat: 17 Juli 2026*
*Berdasarkan: Gap Analysis Pasca-Fase 1 (analisis implementasi aktual vs. kebutuhan survei)*
