<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Event;
use App\Models\JenisOlahraga;
use App\Models\Kehadiran;
use App\Models\Partisipasi;
use App\Models\PointTransaction;
use App\Models\Prasarana;
use App\Models\User;
use App\Services\GamificationService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedPrasarana();
        $this->seedClubs();
        $this->seedEvents();
        $this->seedPartisipasiDanKehadiran();
        $this->seedPointTransactions();
        $this->call(AuditLogSeeder::class);
    }

    private function seedPrasarana(): void
    {
        $relawanIds = User::where('role', 'relawan')->pluck('id')->toArray();

        $data = [
            [
                'nama_fasilitas' => 'Lapangan Sepak Bola Desa Sumber Agung',
                '_jenis_olahraga' => ['Sepak Bola'],
                'alamat' => 'Jl. Raya Sumber Agung No. 45',
                'desa' => 'Desa Sumber Agung',
                'kecamatan' => 'Kec. Glagah',
                'kabupaten' => 'Kab. Banyuwangi',
                'latitude' => -8.2156,
                'longitude' => 114.3678,
                'kondisi_lantai' => 4,
                'kondisi_ring' => 3,
                'kondisi_net' => 3,
                'kondisi_gawang' => 4,
                'kondisi_lapangan' => 4,
                'kondisi_ventilasi' => 5,
                'kondisi_pencahayaan' => 4,
                'kondisi_kamar_mandi' => 3,
                'akses_disabilitas' => true,
                'akses_parkir' => true,
                'akses_transportasi' => true,
                'fasilitas_ruang_ganti' => true,
                'fasilitas_tribun' => false,
                'keterangan' => 'Lapangan utama desa, sering dipakai latihan klub lokal.',
                'status_validasi' => 'validated',
            ],
            [
                'nama_fasilitas' => 'Gor Tamansari',
                '_jenis_olahraga' => ['Badminton'],
                'alamat' => 'Jl. Sudirman No. 12, Tamansari',
                'desa' => 'Desa Tamansari',
                'kecamatan' => 'Kec. Banyuwangi',
                'kabupaten' => 'Kab. Banyuwangi',
                'latitude' => -8.1987,
                'longitude' => 114.3823,
                'kondisi_lantai' => 5,
                'kondisi_ring' => 1,
                'kondisi_net' => 1,
                'kondisi_gawang' => 1,
                'kondisi_lapangan' => 5,
                'kondisi_ventilasi' => 4,
                'kondisi_pencahayaan' => 5,
                'kondisi_kamar_mandi' => 4,
                'akses_disabilitas' => false,
                'akses_parkir' => true,
                'akses_transportasi' => true,
                'fasilitas_ruang_ganti' => true,
                'fasilitas_tribun' => true,
                'keterangan' => 'Gedung olahraga indoor dengan 4 lapangan badminton.',
                'status_validasi' => 'validated',
            ],
            [
                'nama_fasilitas' => 'Lapangan Voli Jajag',
                '_jenis_olahraga' => ['Bola Voli'],
                'alamat' => 'Dusun Krajan, Jajag',
                'desa' => 'Desa Jajag',
                'kecamatan' => 'Kec. Gambiran',
                'kabupaten' => 'Kab. Banyuwangi',
                'latitude' => -8.3421,
                'longitude' => 114.2156,
                'kondisi_lantai' => 3,
                'kondisi_ring' => 2,
                'kondisi_net' => 2,
                'kondisi_gawang' => 1,
                'kondisi_lapangan' => 3,
                'kondisi_ventilasi' => 5,
                'kondisi_pencahayaan' => 3,
                'kondisi_kamar_mandi' => 2,
                'akses_disabilitas' => false,
                'akses_parkir' => false,
                'akses_transportasi' => false,
                'fasilitas_ruang_ganti' => false,
                'fasilitas_tribun' => false,
                'keterangan' => 'Lapangan terbuka tanpa tribun, perlu perbaikan net.',
                'status_validasi' => 'validated',
            ],
            [
                'nama_fasilitas' => 'Stadion Mini Gumukmas',
                '_jenis_olahraga' => ['Atletik/Lari'],
                'alamat' => 'Jl. Ahmad Yani, Gumukmas',
                'desa' => 'Desa Gumukmas',
                'kecamatan' => 'Kec. Gumukmas',
                'kabupaten' => 'Kab. Jember',
                'latitude' => -8.2987,
                'longitude' => 113.4567,
                'kondisi_lantai' => 4,
                'kondisi_ring' => 1,
                'kondisi_net' => 1,
                'kondisi_gawang' => 3,
                'kondisi_lapangan' => 4,
                'kondisi_ventilasi' => 5,
                'kondisi_pencahayaan' => 4,
                'kondisi_kamar_mandi' => 3,
                'akses_disabilitas' => true,
                'akses_parkir' => true,
                'akses_transportasi' => true,
                'fasilitas_ruang_ganti' => true,
                'fasilitas_tribun' => true,
                'keterangan' => 'Stadion mini dengan lintasan atletik dan lapangan sepak bola.',
                'status_validasi' => 'validated',
            ],
            [
                'nama_fasilitas' => 'Lapangan Basket Tempurejo',
                '_jenis_olahraga' => ['Bola Basket'],
                'alamat' => 'Komplek SMPN 3 Tempurejo',
                'desa' => 'Desa Tempurejo',
                'kecamatan' => 'Kec. Tempurejo',
                'kabupaten' => 'Kab. Jember',
                'latitude' => -8.4123,
                'longitude' => 113.7890,
                'kondisi_lantai' => 3,
                'kondisi_ring' => 3,
                'kondisi_net' => 1,
                'kondisi_gawang' => 1,
                'kondisi_lapangan' => 3,
                'kondisi_ventilasi' => 5,
                'kondisi_pencahayaan' => 3,
                'kondisi_kamar_mandi' => 2,
                'akses_disabilitas' => false,
                'akses_parkir' => true,
                'akses_transportasi' => false,
                'fasilitas_ruang_ganti' => false,
                'fasilitas_tribun' => false,
                'keterangan' => 'Lapangan basket outdoor, kondisi ring perlu perhatian.',
                'status_validasi' => 'pending',
            ],
        ];

        foreach ($data as $item) {
            $jenisOlahragaNama = $item['_jenis_olahraga'];
            unset($item['_jenis_olahraga']);
            $item['user_id'] = $relawanIds[array_rand($relawanIds)];

            $prasarana = Prasarana::create($item);
            $jenisOlahragaIds = collect($jenisOlahragaNama)
                ->map(fn($nama) => JenisOlahraga::firstOrCreate(['nama' => $nama], ['aktif' => true])->id);
            $prasarana->jenisOlahraga()->sync($jenisOlahragaIds);
        }

        $this->command->info('Seeded 5 prasarana.');
    }

    private function seedClubs(): void
    {
        $relawanIds = User::where('role', 'relawan')->pluck('id')->toArray();
        $prasaranaIds = Prasarana::pluck('id')->toArray();

        $clubs = [
            [
                'nama_club' => 'Persisaga Sumber Agung',
                'deskripsi' => 'Klub sepak bola warga Desa Sumber Agung.',
                'ketua_club' => 'Pak Kades Sumber Agung',
                'narahubung' => 'Budi Santoso',
                'no_telepon' => '081234567890',
                'email' => 'persisaga@example.com',
                'alamat' => 'Balai Desa Sumber Agung',
                'desa' => 'Desa Sumber Agung',
                'kecamatan' => 'Kec. Glagah',
                'kabupaten' => 'Kab. Banyuwangi',
                'tanggal_berdiri' => '2019-03-15',
                'status_validasi' => 'validated',
            ],
            [
                'nama_club' => 'Voli Putri Tamansari',
                'deskripsi' => 'Klub voli putri remaja dan dewasa.',
                'ketua_club' => 'Ibu Lurah Tamansari',
                'narahubung' => 'Siti Aminah',
                'no_telepon' => '082345678901',
                'email' => 'volitamansari@example.com',
                'alamat' => 'Gor Tamansari',
                'desa' => 'Desa Tamansari',
                'kecamatan' => 'Kec. Banyuwangi',
                'kabupaten' => 'Kab. Banyuwangi',
                'tanggal_berdiri' => '2021-07-20',
                'status_validasi' => 'validated',
            ],
            [
                'nama_club' => 'Atletik Jember Muda',
                'deskripsi' => 'Klub atletik pemula dari Kabupaten Jember.',
                'ketua_club' => 'Coach Dedi',
                'narahubung' => 'Dedi Kurniawan',
                'no_telepon' => '083456789012',
                'email' => 'atletikjember@example.com',
                'alamat' => 'Stadion Mini Gumukmas',
                'desa' => 'Desa Gumukmas',
                'kecamatan' => 'Kec. Gumukmas',
                'kabupaten' => 'Kab. Jember',
                'tanggal_berdiri' => '2020-01-10',
                'status_validasi' => 'pending',
            ],
        ];

        foreach ($clubs as $i => $club) {
            $club['user_id'] = $relawanIds[$i % count($relawanIds)];
            $club['prasarana_id'] = $prasaranaIds[$i % count($prasaranaIds)];
            $club['aktif'] = true;
            Club::create($club);
        }

        $this->command->info('Seeded 3 clubs.');
    }

    private function seedEvents(): void
    {
        $relawanIds = User::where('role', 'relawan')->pluck('id')->toArray();

        $events = [
            [
                'nama_event' => 'Fun Run Banyuwangi Sehat 2026',
                'tingkat' => 'Kabupaten/Kota',
                'tanggal_mulai' => Carbon::now()->addDays(10),
                'tanggal_selesai' => Carbon::now()->addDays(10),
                'deskripsi_kegiatan' => 'Lari santai 5K untuk masyarakat Kabupaten Banyuwangi.',
                'desa' => 'Desa Tamansari',
                'kecamatan' => 'Kec. Banyuwangi',
                'kabupaten' => 'Kab. Banyuwangi',
                'status_validasi' => 'validated',
            ],
            [
                'nama_event' => 'Turnamen Voli Antar-Desa',
                'tingkat' => 'Kecamatan',
                'tanggal_mulai' => Carbon::now()->addDays(25),
                'tanggal_selesai' => Carbon::now()->addDays(27),
                'deskripsi_kegiatan' => 'Turnamen voli antar desa se-Kecamatan Glagah.',
                'desa' => 'Desa Sumber Agung',
                'kecamatan' => 'Kec. Glagah',
                'kabupaten' => 'Kab. Banyuwangi',
                'status_validasi' => 'validated',
            ],
            [
                'nama_event' => 'Senam Lansia Jember',
                'tingkat' => 'Desa/Kelurahan',
                'tanggal_mulai' => Carbon::now()->subDays(5),
                'tanggal_selesai' => null,
                'deskripsi_kegiatan' => 'Senam rutin untuk lansia setiap Jumat pagi.',
                'desa' => 'Desa Gumukmas',
                'kecamatan' => 'Kec. Gumukmas',
                'kabupaten' => 'Kab. Jember',
                'status_validasi' => 'pending',
            ],
            [
                'nama_event' => 'Pelatihan Pelatih Akar Rumput',
                'tingkat' => 'Kabupaten/Kota',
                'tanggal_mulai' => Carbon::now()->addDays(45),
                'tanggal_selesai' => Carbon::now()->addDays(47),
                'deskripsi_kegiatan' => 'Pelatihan untuk pelatih olahraga tingkat akar rumput.',
                'desa' => 'Desa Jajag',
                'kecamatan' => 'Kec. Gambiran',
                'kabupaten' => 'Kab. Banyuwangi',
                'status_validasi' => 'validated',
            ],
        ];

        foreach ($events as $event) {
            $event['user_id'] = $relawanIds[array_rand($relawanIds)];
            Event::create($event);
        }

        $this->command->info('Seeded 4 events.');
    }

    private function seedPartisipasiDanKehadiran(): void
    {
        $relawanIds = User::where('role', 'relawan')->pluck('id')->toArray();

        $partisipasiList = [
            [
                'lokasi_observasi' => 'Lapangan Sepak Bola Sumber Agung',
                'desa' => 'Desa Sumber Agung',
                'kecamatan' => 'Kec. Glagah',
                'kabupaten' => 'Kab. Banyuwangi',
                'tanggal_observasi' => Carbon::now()->subDays(2),
                'estimasi_jumlah_orang' => 25,
                'mayoritas_usia' => 'Dewasa',
                'status_validasi' => 'validated',
            ],
            [
                'lokasi_observasi' => 'Gor Tamansari',
                'desa' => 'Desa Tamansari',
                'kecamatan' => 'Kec. Banyuwangi',
                'kabupaten' => 'Kab. Banyuwangi',
                'tanggal_observasi' => Carbon::now()->subDays(5),
                'estimasi_jumlah_orang' => 12,
                'mayoritas_usia' => 'Anak/Pelajar',
                'status_validasi' => 'validated',
            ],
            [
                'lokasi_observasi' => 'Stadion Mini Gumukmas',
                'desa' => 'Desa Gumukmas',
                'kecamatan' => 'Kec. Gumukmas',
                'kabupaten' => 'Kab. Jember',
                'tanggal_observasi' => Carbon::now()->subDays(8),
                'estimasi_jumlah_orang' => 30,
                'mayoritas_usia' => 'Lansia',
                'status_validasi' => 'validated',
            ],
            [
                'lokasi_observasi' => 'Lapangan Voli Jajag',
                'desa' => 'Desa Jajag',
                'kecamatan' => 'Kec. Gambiran',
                'kabupaten' => 'Kab. Banyuwangi',
                'tanggal_observasi' => Carbon::now()->subDays(12),
                'estimasi_jumlah_orang' => 8,
                'mayoritas_usia' => 'Dewasa',
                'status_validasi' => 'pending',
            ],
            [
                'lokasi_observasi' => 'Lapangan Basket Tempurejo',
                'desa' => 'Desa Tempurejo',
                'kecamatan' => 'Kec. Tempurejo',
                'kabupaten' => 'Kab. Jember',
                'tanggal_observasi' => Carbon::now()->subDays(15),
                'estimasi_jumlah_orang' => 15,
                'mayoritas_usia' => 'Anak/Pelajar',
                'status_validasi' => 'validated',
            ],
        ];

        $kehadiranData = [
            ['nama_peserta' => 'Agus Wijaya', 'jenis_kelamin' => 'L', 'usia' => 28, 'kelompok_usia' => 'Dewasa', 'status' => 'Hadir'],
            ['nama_peserta' => 'Maya Sari', 'jenis_kelamin' => 'P', 'usia' => 24, 'kelompok_usia' => 'Dewasa', 'status' => 'Hadir'],
            ['nama_peserta' => 'Pak Tarno', 'jenis_kelamin' => 'L', 'usia' => 62, 'kelompok_usia' => 'Lansia', 'status' => 'Hadir'],
            ['nama_peserta' => 'Ani Susanti', 'jenis_kelamin' => 'P', 'usia' => 35, 'kelompok_usia' => 'Dewasa', 'status' => 'Izin'],
            ['nama_peserta' => 'Bambang', 'jenis_kelamin' => 'L', 'usia' => 10, 'kelompok_usia' => 'Anak', 'status' => 'Hadir'],
        ];

        foreach ($partisipasiList as $item) {
            $item['user_id'] = $relawanIds[array_rand($relawanIds)];
            $partisipasi = Partisipasi::create($item);

            // Tambahkan 3-5 kehadiran per partisipasi
            $jumlahKehadiran = rand(3, 5);
            for ($i = 0; $i < $jumlahKehadiran; $i++) {
                $k = $kehadiranData[array_rand($kehadiranData)];
                Kehadiran::create([
                    'partisipasi_id' => $partisipasi->id,
                    'nama_peserta' => $k['nama_peserta'] . ' ' . ($i + 1),
                    'jenis_kelamin' => $k['jenis_kelamin'],
                    'usia' => $k['usia'],
                    'kelompok_usia' => $k['kelompok_usia'],
                    'status' => $k['status'],
                    'created_by' => $item['user_id'],
                ]);
            }
        }

        $this->command->info('Seeded 5 partisipasi dengan kehadiran individu.');
    }

    /**
     * Buat transaksi poin dummy untuk entitas yang sudah tervalidasi.
     */
    private function seedPointTransactions(): void
    {
        // Prasarana validated -> prasarana_baru (50 poin)
        foreach (Prasarana::where('status_validasi', 'validated')->get() as $p) {
            PointTransaction::create([
                'user_id'      => $p->user_id,
                'related_type' => 'prasarana',
                'related_id'   => $p->id,
                'jenis_aksi'   => 'baru',
                'poin'         => 50,
                'status'       => 'valid',
                'created_at'   => $p->created_at,
            ]);
        }

        // Clubs validated -> club_baru (40 poin)
        foreach (Club::where('status_validasi', 'validated')->get() as $c) {
            PointTransaction::create([
                'user_id'      => $c->user_id,
                'related_type' => 'club',
                'related_id'   => $c->id,
                'jenis_aksi'   => 'baru',
                'poin'         => 40,
                'status'       => 'valid',
                'created_at'   => $c->created_at,
            ]);
        }

        // Events validated -> event_baru (20 poin)
        foreach (Event::where('status_validasi', 'validated')->get() as $e) {
            PointTransaction::create([
                'user_id'      => $e->user_id,
                'related_type' => 'event',
                'related_id'   => $e->id,
                'jenis_aksi'   => 'baru',
                'poin'         => 20,
                'status'       => 'valid',
                'created_at'   => $e->created_at,
            ]);
        }

        // Partisipasi validated -> partisipasi_valid (3 poin)
        foreach (Partisipasi::where('status_validasi', 'validated')->get() as $part) {
            PointTransaction::create([
                'user_id'      => $part->user_id,
                'related_type' => 'partisipasi',
                'related_id'   => $part->id,
                'jenis_aksi'   => 'baru',
                'poin'         => 3,
                'status'       => 'valid',
                'created_at'   => $part->created_at,
            ]);
        }

        // Update total_poin semua user relawan dan cek lencana
        $relawanIds = User::where('role', 'relawan')->pluck('id');
        foreach ($relawanIds as $uid) {
            GamificationService::updateTotalPoin($uid);
            GamificationService::cekDanBerikanLencana($uid);
        }

        $this->command->info('Seeded point transactions & badges.');
    }
}
