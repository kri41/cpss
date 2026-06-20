<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PartisipasiController;
use App\Http\Controllers\PrasaranaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TalentaController;
use App\Http\Controllers\TenagaAhliController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/* ============================================================
   AKSES PUBLIK (Tanpa Login)
   ============================================================ */
Route::get('/prasarana', [PrasaranaController::class, 'index'])->name('prasarana.index');
Route::get('/prasarana/{prasarana}', [PrasaranaController::class, 'show'])->name('prasarana.show');

Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

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
    });

    // Events Routes (Admin & Relawan) — create/store/edit/update/destroy only
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('events', EventController::class)->except(['index', 'show']);
        Route::patch('/events/{event}/validate', [EventController::class, 'validateEvent'])->name('events.validate');
    });

    // Clubs Routes (Admin & Relawan) — create/store/edit/update/destroy only
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('clubs', ClubController::class)->except(['index', 'show']);
        Route::patch('/clubs/{club}/validate', [ClubController::class, 'validateClub'])->name('clubs.validate');
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
});

require __DIR__.'/auth.php';
