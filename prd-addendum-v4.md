# PRD Addendum v4.0 — Kampung Olahraga v2: Fasil-Level QR, Klub/Komunitas, Google Auth, Dual Leaderboard

> **Dokumen ini adalah kelanjutan dari `prd-addendum-v3.md`.**
> Ditulis pasca-redesign fitur Kampung Olahraga (20 Juli 2026), berdasarkan masukan
> langsung dari peneliti setelah implementasi awal fitur (19 Juli 2026) dievaluasi.

---

## 1. Latar Belakang & Rasional

Implementasi awal Kampung Olahraga (19 Juli 2026) memodelkan satu Kampung Olahraga
sebagai satu entitas dengan **satu QR code**, dan skor kelayakan Kemenpora dihitung
dari total check-in di QR tersebut. Evaluasi menemukan konsep ini tidak merepresentasikan
kenyataan lapangan: sebuah Kampung Olahraga adalah "rumah" yang **berisi banyak fasilitas**
(lapangan, meja tenis meja, dsb.) yang sebagian besar **sudah pernah dilaporkan** relawan
lewat menu Prasarana yang sudah ada — sehingga QR tunggal per-kampung tidak bisa
membedakan aktivitas per-fasilitas, dan data Prasarana yang sudah dikumpulkan tidak
termanfaatkan oleh fitur Kampung Olahraga.

Redesign ini menggeser QR check-in ke level **fasil** (Prasarana yang didaftarkan ke
sebuah Kampung Olahraga), sekaligus mengintegrasikan Klub/Komunitas sebagai atribut
peserta check-in, menambahkan login Google, dan memperluas leaderboard.

---

## 2. Perubahan Konsep Utama

### 2.1 Fasil sebagai Unit QR

- Prasarana (fasilitas yang sudah dilaporkan relawan) kini bisa **didaftarkan/ditarik**
  ke sebuah Kampung Olahraga — satu fasil hanya bisa dimiliki oleh satu kampung pada
  satu waktu (`prasarana.kampung_olahraga_id`).
- QR code (`prasarana.qr_token`) dibuat per-fasil saat didaftarkan, bukan lagi per-kampung.
  QR hanya aktif jika Kampung Olahraga induknya berstatus **tervalidasi** — melepas fasil
  atau membatalkan verifikasi kampung otomatis menonaktifkan QR tersebut.
- Kandidat fasil yang bisa didaftarkan ke sebuah kampung difilter berdasarkan kesamaan
  wilayah (kabupaten + kecamatan + desa, dicocokkan case-insensitive) dengan kampung
  tersebut — mencegah fasil dari desa lain "nyasar" ke kampung yang salah.

### 2.2 Klub/Komunitas Terintegrasi

- Menu "Club" di-rename menjadi **"Klub/Komunitas"** di seluruh navigasi (sidebar,
  nav publik, nav legacy) — perubahan label saja, bukan pemisahan skema baru.
- Klub/Komunitas mendapat kolom `jenis_olahraga_id` (opsional) agar sistem tahu jenis
  olahraga yang diwakili klub tersebut.
- Klub/Komunitas dapat didaftarkan ke satu atau lebih Kampung Olahraga (relasi
  many-to-many via tabel `kampung_klub`), difilter dengan aturan wilayah yang sama
  seperti fasil.
- Saat check-in, peserta memilih klub/komunitas dari daftar yang **sudah terdaftar di
  kampung tempat fasil itu berada** (atau memilih "Belum bergabung"). Jenis olahraga
  otomatis terisi dari data klub bila dipilih; jika "Belum bergabung", peserta memilih
  atau menambahkan jenis olahraga secara manual (mekanisme lama tetap dipakai).

### 2.3 Syarat Kemenpora Tetap di Level Kampung

Skor `KomponenSyarat` (tingkatan syarat minimal pengakuan Kemenpora, dikelola admin di
menu Komponen Syarat) **tetap dihitung dari akumulasi check-in seluruh fasil dalam satu
kampung**, bukan per-fasil — sesuai konsep "rumah berisi banyak fasil" yang dinilai
sebagai satu kesatuan program.

### 2.4 Leaderboard Ganda Baru

Halaman Leaderboard kini punya 3 tab:
1. **Relawan** (sudah ada) — poin kontribusi data (Prasarana/Club/Event/Partisipasi),
   tidak berubah.
2. **Kampung Olahraga** (baru) — ranking berdasarkan skor `KomponenSyarat`.
3. **Klub/Komunitas** (baru) — ranking berdasarkan total check-in peserta yang memilih
   klub/komunitas tersebut, **lintas kampung** (satu klub bisa terdaftar di banyak
   kampung, poinnya diakumulasi dari check-in di manapun).

Sebagai bagian dari redesign ini, verifikasi Kampung Olahraga oleh admin kini juga
mengkreditkan poin ke relawan pelapor lewat `GamificationService` (kode aktivitas
`kampung_baru`, +30 poin, aturan `1x_per_entitas`) — sebelumnya skor Kampung Olahraga
sama sekali terpisah dari sistem poin relawan.

### 2.5 Partisipasi Diarsipkan

Menu "Partisipasi" dihapus dari seluruh navigasi karena fungsinya digantikan oleh
check-in QR Kampung Olahraga. Data, model, controller, dan route Partisipasi **tidak
dihapus** — tetap bisa diakses lewat URL langsung dan tetap dipakai `ExportController`
serta laporan PDF profil relawan, demi menjaga integritas data historis penelitian.
Statistik publik "Total Partisipasi" di halaman landing kini dihitung dari jumlah
check-in QR Kampung Olahraga yang tervalidasi, bukan lagi dari estimasi jumlah peserta
Partisipasi.

### 2.6 Login/Register via Google

Ditambahkan sebagai **opsi tambahan**, bukan pengganti — form email/password Breeze
tetap ada. Menggunakan `laravel/socialite`. Akun baru dari Google otomatis diberi role
`relawan`; jika email sudah terdaftar, akun yang ada ditautkan ke Google (bukan
duplikat). Wilayah (desa/kecamatan/kabupaten/provinsi) tidak dikumpulkan saat sign-up
via Google — konsisten dengan alur registrasi email/password yang sudah ada, yang juga
tidak mengumpulkan wilayah di form register (diisi belakangan lewat halaman profil).

---

## 3. Skema Data Baru

| Tabel/Kolom | Keterangan |
|---|---|
| `users.google_id`, `users.avatar` | Identitas OAuth Google |
| `prasarana.kampung_olahraga_id` | Fasil ini bagian dari kampung mana (nullable) |
| `prasarana.qr_token` | QR check-in milik fasil ini (nullable, unique) |
| `clubs.jenis_olahraga_id` | Jenis olahraga yang diwakili klub/komunitas ini |
| `kampung_klub` (pivot) | Klub/komunitas mana saja terdaftar di kampung mana |
| `checkin_kampung.prasarana_id` | Fasil spesifik tempat check-in terjadi |
| `checkin_kampung.club_id` | Klub/komunitas yang dipilih peserta saat check-in |

---

## 4. Dampak ke Dokumen Sebelumnya

- `prd-addendum-v3.md` §"Fitur Kampung Olahraga" (jika ada) harus dibaca bersama
  dokumen ini — desain QR-per-kampung di sana sudah digantikan oleh QR-per-fasil.
- `PROGRESS.md` §4.8 mencatat checklist implementasi redesign ini.
