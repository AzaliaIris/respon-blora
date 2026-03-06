<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
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
    // Route::prefix('laporan')->group(function () { ... });

    // ── Dashboard/Rekap — Admin, Koordinator, Pimpinan ──
    // Route::middleware('role:admin,koordinator,pimpinan')->prefix('dashboard')->group(...)

});