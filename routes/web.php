<?php

use Illuminate\Support\Facades\Route;

/*
|==========================================================================
| RESPON BLORA — Web Routes
|==========================================================================
*/

Route::get('/', fn () => redirect('/login'));

Route::get('/login',        fn () => view('login'));
Route::get('/dashboard',    fn () => view('dashboard'));
Route::get('/laporan',      fn () => view('laporan'));
Route::get('/form-laporan', fn () => view('form-laporan'));
Route::get('/monitoring',   fn () => view('monitoring'));
Route::get('/petugas',      fn () => view('petugas'));
Route::get('/users',        fn () => view('users'));
Route::get('/profil',       fn () => view('profil'));
Route::get('/laporan-mitra', fn () => view('form-mitra'));

Route::fallback(fn () => redirect('/login'));
