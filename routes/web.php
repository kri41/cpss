<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\GeoJsonController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\KampungController;
use App\Http\Controllers\KomponenSyaratController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PartisipasiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PrasaranaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TalentaController;
use App\Http\Controllers\TenagaAhliController;
use App\Http\Controllers\RelawanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $stats = [
        'totalPrasarana' => \App\Models\Prasarana::validated()->count(),
        'totalClubs' => \App\Models\Club::validated()->where('aktif', true)->count(),
        'totalEvents' => \App\Models\Event::validated()->count(),
        'totalPartisipasi' => \App\Models\CheckinKampung::whereHas('kampung', fn($q) => $q->where('status_validasi', 'validated'))->count(),
    ];

    $latestPrasarana = \App\Models\Prasarana::validated()->latest()->take(3)->get();
    $upcomingEvents = \App\Models\Event::validated()->akanDatang()->take(3)->get();
    $activeClubs = \App\Models\Club::validated()->aktif()->with('prasarana')->take(3)->get();

    return view('welcome', compact('stats', 'latestPrasarana', 'upcomingEvents', 'activeClubs'));
});

/* ============================================================
   API WILAYAH
   ============================================================ */
Route::get('/api/provinces', [WilayahController::class, 'getProvinces']);
Route::get('/api/geojson/indonesia-provinces', [GeoJsonController::class, 'provinces']);
Route::get('/api/kabupaten/{province_id}', [WilayahController::class, 'getKabupaten']);
Route::get('/api/kecamatan/{regency_id}', [WilayahController::class, 'getKecamatan']);
Route::get('/api/desa/{district_id}', [WilayahController::class, 'getDesa']);
Route::get('/api/kehadiran/autocomplete-nama', [\App\Http\Controllers\KehadiranController::class, 'autocompleteNama']);

/* ============================================================
   AKSES PUBLIK (Tanpa Login)
   ============================================================ */
Route::get('/prasarana', [PrasaranaController::class, 'index'])->name('prasarana.index');
Route::get('/prasarana/{prasarana}', [PrasaranaController::class, 'show'])->name('prasarana.show')->where('prasarana', '(?!create$)[^/]+');

Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show')->where('club', '(?!create$)[^/]+');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/peta', [EventController::class, 'peta'])->name('events.peta');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show')->where('event', '(?!create$)[^/]+');

Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender.index');

/* ============================================================
   PROTECTED ROUTES
   ============================================================ */
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/laporan-pdf', [DashboardController::class, 'laporanPdf'])->name('dashboard.laporan-pdf');

    // Dashboard views (index with sidebar)
    Route::get('/dashboard/events', [EventController::class, 'index'])->name('dashboard.events');
    Route::get('/dashboard/events/peta', [EventController::class, 'peta'])->name('dashboard.events.peta');
    Route::get('/dashboard/prasarana', [PrasaranaController::class, 'index'])->name('dashboard.prasarana');
    Route::get('/dashboard/clubs', [ClubController::class, 'index'])->name('dashboard.clubs');
    Route::get('/dashboard/kalender', [KalenderController::class, 'index'])->name('dashboard.kalender');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profil Relawan
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/profil/laporan/{jenis}', [ProfilController::class, 'laporan'])->name('profil.laporan')
        ->where('jenis', 'prasarana|events|clubs|partisipasi');

    // Leaderboard & Gamification (All authenticated users)
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/my-points', [LeaderboardController::class, 'myPoints'])->name('leaderboard.my-points');

    // Prasarana Routes (Admin & Relawan) — create/store/edit/update/destroy only
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('prasarana', PrasaranaController::class)->except(['index', 'show']);
        Route::patch('/prasarana/{prasarana}/validate', [PrasaranaController::class, 'validatePrasarana'])->name('prasarana.validate');
        Route::patch('/prasarana/{prasarana}/cancel-validate', [PrasaranaController::class, 'cancelValidatePrasarana'])->name('prasarana.cancel-validate');
    });

    // Partisipasi Routes (Admin & Relawan)
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('partisipasi', PartisipasiController::class);

        // Kehadiran (Presensi Sederhana)
        Route::post('/partisipasi/{partisipasi}/kehadiran', [PartisipasiController::class, 'storeKehadiran'])
            ->name('partisipasi.kehadiran.store');
        Route::patch('/kehadiran/{kehadiran}', [PartisipasiController::class, 'updateKehadiran'])
            ->name('partisipasi.kehadiran.update');
        Route::delete('/kehadiran/{kehadiran}', [PartisipasiController::class, 'destroyKehadiran'])
            ->name('partisipasi.kehadiran.destroy');

        Route::patch('/partisipasi/{partisipasi}/validate', [PartisipasiController::class, 'validatePartisipasi'])->name('partisipasi.validate');
        Route::patch('/partisipasi/{partisipasi}/cancel-validate', [PartisipasiController::class, 'cancelValidatePartisipasi'])->name('partisipasi.cancel-validate');
    });

    // Events Routes (Admin & Relawan) — create/store/edit/update/destroy only
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('events', EventController::class)->except(['index', 'show']);
        Route::patch('/events/{event}/validate', [EventController::class, 'validateEvent'])->name('events.validate');
        Route::patch('/events/{event}/cancel-validate', [EventController::class, 'cancelValidateEvent'])->name('events.cancel-validate');
    });

    // Clubs Routes (Admin & Relawan) — create/store/edit/update/destroy only
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('clubs', ClubController::class)->except(['index', 'show']);
        Route::patch('/clubs/{club}/validate', [ClubController::class, 'validateClub'])->name('clubs.validate');
        Route::patch('/clubs/{club}/cancel-validate', [ClubController::class, 'cancelValidateClub'])->name('clubs.cancel-validate');
    });

    // Talenta Routes (Admin only)
    Route::middleware(['App\Http\Middleware\CheckRole:admin'])->group(function () {
        Route::resource('talenta', TalentaController::class);
        Route::resource('tenaga-ahli', TenagaAhliController::class);
    });

    // User Management Routes (Super Admin only)
    Route::middleware(['App\Http\Middleware\CheckRole:super_admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('/users/import/form',     [UserController::class, 'importForm'])->name('users.import.form');
        Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
        Route::post('/users/import/preview', [UserController::class, 'importPreview'])->name('users.import.preview');
        Route::get('/users/import/confirm',  [UserController::class, 'importConfirm'])->name('users.import.confirm');
        Route::post('/users/import/confirm', [UserController::class, 'importConfirmStore'])->name('users.import.confirm.store');
    });

    // Audit Log Routes (Admin & Super Admin)
    Route::middleware(['App\Http\Middleware\CheckRole:admin'])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // Export Routes (Admin & Super Admin)
    Route::middleware(['App\Http\Middleware\CheckRole:admin'])->prefix('export')->name('export.')->group(function () {
        Route::get('/prasarana',   [ExportController::class, 'prasarana'])->name('prasarana');
        Route::get('/clubs',       [ExportController::class, 'clubs'])->name('clubs');
        Route::get('/events',      [ExportController::class, 'events'])->name('events');
        Route::get('/partisipasi', [ExportController::class, 'partisipasi'])->name('partisipasi');
        Route::get('/leaderboard', [ExportController::class, 'leaderboard'])->name('leaderboard');
    });

    // Daftar Relawan (All authenticated users)
    Route::get('/relawan', [RelawanController::class, 'index'])->name('relawan.index');

    // Notifications
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Kampung Olahraga (Admin & Relawan)
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('kampung', KampungController::class);
        Route::patch('/kampung/{kampung}/validate',        [KampungController::class, 'validate'])->name('kampung.validate');
        Route::patch('/kampung/{kampung}/reject',          [KampungController::class, 'reject'])->name('kampung.reject');
        Route::patch('/kampung/{kampung}/cancel-validate', [KampungController::class, 'cancelValidate'])->name('kampung.cancel-validate');

        Route::post('/kampung/{kampung}/fasil',                [KampungController::class, 'attachFasil'])->name('kampung.fasil.attach');
        Route::delete('/kampung/{kampung}/fasil/{prasarana}',  [KampungController::class, 'detachFasil'])->name('kampung.fasil.detach');
        Route::post('/kampung/{kampung}/klub',                 [KampungController::class, 'attachKlub'])->name('kampung.klub.attach');
        Route::delete('/kampung/{kampung}/klub/{club}',        [KampungController::class, 'detachKlub'])->name('kampung.klub.detach');
    });

    // Komponen Syarat (Admin only)
    Route::middleware(['App\Http\Middleware\CheckRole:admin'])->group(function () {
        Route::get('/komponen-syarat', [KomponenSyaratController::class, 'index'])->name('komponen-syarat.index');
        Route::post('/komponen-syarat', [KomponenSyaratController::class, 'store'])->name('komponen-syarat.store');
        Route::put('/komponen-syarat/{komponenSyarat}', [KomponenSyaratController::class, 'update'])->name('komponen-syarat.update');
        Route::delete('/komponen-syarat/{komponenSyarat}', [KomponenSyaratController::class, 'destroy'])->name('komponen-syarat.destroy');
    });
});

/* ============================================================
   QR PARTISIPASI PUBLIK (Tanpa Login)
   ============================================================ */
Route::get('/partisipasi/{partisipasi}/qr', [PartisipasiController::class, 'showQr'])->name('partisipasi.qr.show');
Route::get('/partisipasi/{partisipasi}/daftar', [PartisipasiController::class, 'daftarPublik'])->name('partisipasi.daftar');
Route::post('/partisipasi/{partisipasi}/daftar', [PartisipasiController::class, 'daftarPublik']);

/* ============================================================
   KAMPUNG OLAHRAGA — QR CHECK-IN PUBLIK (Tanpa Login)
   ============================================================ */
Route::get('/qr/{token}',        [KampungController::class, 'checkinForm'])->name('kampung.checkin.form');
Route::post('/qr/{token}',       [KampungController::class, 'checkinStore'])->name('kampung.checkin.store');
Route::get('/qr/{token}/sukses', [KampungController::class, 'checkinSukses'])->name('kampung.checkin.sukses');
Route::get('/api/jenis-olahraga',[KampungController::class, 'apiJenisOlahraga'])->name('api.jenis-olahraga');

require __DIR__.'/auth.php';
