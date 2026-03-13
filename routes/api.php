<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\LaporanMitraController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════
// PUBLIC ROUTES — Tidak butuh token
// ═══════════════════════════════════════
Route::prefix('auth')->middleware('throttle:10,5')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

Route::post('/laporan/mitra', [LaporanMitraController::class, 'store'])
     ->middleware('throttle:5,10'); // backup throttle Laravel

Route::get('/publik/ketua-tim', function () {
    $petugas = \App\Models\User::where('role', 'petugas')
        ->whereIn('posisi', ['pml', 'subject_matter'])
        ->where('is_active', true)
        ->select('id', 'name', 'posisi', 'wilayah_tugas')
        ->orderBy('name')
        ->get();
    return response()->json(['success' => true, 'data' => $petugas]);
});

Route::get('/publik/petugas-posisi', function (Request $request) {
    $posisi = $request->query('posisi');
    if (!in_array($posisi, ['pml', 'subject_matter'])) {
        return response()->json(['success' => false, 'message' => 'Posisi tidak valid'], 422);
    }
    $data = \App\Models\User::where('role', 'petugas')
        ->where('posisi', $posisi)
        ->where('is_active', true)
        ->select('id', 'name', 'wilayah_tugas')
        ->orderBy('name')
        ->get();
    return response()->json(['success' => true, 'data' => $data]);
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

    Route::prefix('notifikasi')->group(function () {
        Route::get('/',              [NotifikasiController::class, 'index']);
        Route::patch('/{id}/read',   [NotifikasiController::class, 'markRead']);
        Route::patch('/read-all',    [NotifikasiController::class, 'markAllRead']);
    });

});