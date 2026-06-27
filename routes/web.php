<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PartisipasiController;
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
        'totalPrasarana' => \App\Models\Prasarana::count(),
        'totalClubs' => \App\Models\Club::count(),
        'totalEvents' => \App\Models\Event::count(),
        'totalPartisipasi' => \App\Models\Partisipasi::sum('estimasi_jumlah_orang') ?? 0,
    ];

    $latestPrasarana = \App\Models\Prasarana::latest()->take(6)->get();
    $upcomingEvents = \App\Models\Event::akanDatang()->take(4)->get();
    $activeClubs = \App\Models\Club::aktif()->with('prasarana')->take(4)->get();

    return view('welcome', compact('stats', 'latestPrasarana', 'upcomingEvents', 'activeClubs'));
});

/* ============================================================
   API WILAYAH
   ============================================================ */
Route::get('/api/provinces', [WilayahController::class, 'getProvinces']);
Route::get('/api/kabupaten/{province_id}', [WilayahController::class, 'getKabupaten']);
Route::get('/api/kecamatan/{regency_id}', [WilayahController::class, 'getKecamatan']);
Route::get('/api/desa/{district_id}', [WilayahController::class, 'getDesa']);
Route::get('/api/kehadiran/autocomplete-nama', [\App\Http\Controllers\KehadiranController::class, 'autocompleteNama']);

/* ============================================================
   AKSES PUBLIK (Tanpa Login)
   ============================================================ */
Route::get('/prasarana', [PrasaranaController::class, 'index'])->name('prasarana.index');
Route::get('/prasarana/{prasarana}', [PrasaranaController::class, 'show'])->name('prasarana.show')->whereNumber('prasarana');

Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show')->whereNumber('club');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show')->whereNumber('event');

Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender.index');

/* ============================================================
   PROTECTED ROUTES
   ============================================================ */
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
    });

    // Audit Log Routes (Admin & Super Admin)
    Route::middleware(['App\Http\Middleware\CheckRole:admin'])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // Daftar Relawan (All authenticated users)
    Route::get('/relawan', [RelawanController::class, 'index'])->name('relawan.index');

    // Notifications
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

/* ============================================================
   QR PARTISIPASI PUBLIK (Tanpa Login)
   ============================================================ */
Route::get('/partisipasi/{partisipasi}/qr', [PartisipasiController::class, 'showQr'])->name('partisipasi.qr.show');
Route::get('/partisipasi/{partisipasi}/daftar', [PartisipasiController::class, 'daftarPublik'])->name('partisipasi.daftar');
Route::post('/partisipasi/{partisipasi}/daftar', [PartisipasiController::class, 'daftarPublik']);

require __DIR__.'/auth.php';
