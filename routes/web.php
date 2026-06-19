<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
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

// Protected Routes with Auth and Verified
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Prasarana Routes (Admin & Relawan)
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('prasarana', PrasaranaController::class);
        Route::resource('partisipasi', PartisipasiController::class);
    });

    // Events Routes (Admin & Relawan)
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('events', EventController::class);
    });

    // Clubs Routes (Admin & Relawan)
    Route::middleware(['App\Http\Middleware\CheckRole:admin,relawan'])->group(function () {
        Route::resource('clubs', ClubController::class);
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
