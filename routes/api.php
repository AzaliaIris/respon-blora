<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════
// PUBLIC ROUTES — Tidak butuh token
// ═══════════════════════════════════════
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ═══════════════════════════════════════
// PROTECTED ROUTES — Semua butuh JWT
// Middleware 'auth:api' + 'active.user'
// ═══════════════════════════════════════
Route::middleware(['auth:api', 'active.user'])->group(function () {

    // ── Auth endpoints ──
    Route::prefix('auth')->group(function () {
        Route::post('/logout',  [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me',       [AuthController::class, 'me']);
    });

    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // ── User Management (hanya Admin) ──
    Route::middleware('role:admin')->prefix('users')->group(function () {
        Route::get('/',           [UserController::class, 'index']);
        Route::post('/',          [UserController::class, 'store']);
        Route::get('/{id}',       [UserController::class, 'show']);
        Route::put('/{id}',       [UserController::class, 'update']);
        Route::patch('/{id}/toggle-active', [UserController::class, 'toggleActive']);
        Route::delete('/{id}',    [UserController::class, 'destroy']);
    });

    // ── Laporan — akan diisi di Step berikutnya ──
    // ── Laporan ──
    Route::prefix('laporan')->group(function () {
        // ── Ekspor CSV — Admin & Pimpinan saja ──
        Route::middleware('role:admin,pimpinan')
            ->get('/ekspor', [DashboardController::class, 'ekspor']);

        // Semua role bisa lihat & buat laporan
        Route::get('/',    [LaporanController::class, 'index']);
        Route::post('/',   [LaporanController::class, 'store']);
        Route::get('/{id}',[LaporanController::class, 'show']);

        // Tindak lanjut: petugas, koordinator, admin
        Route::post('/{id}/tindak-lanjut', [LaporanController::class, 'tindakLanjut'])
             ->middleware('role:petugas,koordinator,admin');

        // Verifikasi: hanya koordinator & admin
        Route::patch('/{id}/verifikasi', [LaporanController::class, 'verifikasi'])
             ->middleware('role:koordinator,admin');
    });

    // ── Dashboard — Admin, Koordinator, Pimpinan ──
    Route::prefix('dashboard')
         ->group(function () {
             Route::get('/ringkasan',          [DashboardController::class, 'ringkasan']);
             Route::get('/tren-mingguan',      [DashboardController::class, 'trenMingguan']);
             Route::get('/per-kecamatan',      [DashboardController::class, 'perKecamatan']);
             Route::get('/per-kendala',        [DashboardController::class, 'perKendala']);
             Route::get('/tingkat-selesai',    [DashboardController::class, 'tingkatSelesai']);
             Route::get('/aktivitas-petugas',  [DashboardController::class, 'aktivitasPetugas']);
         });

});