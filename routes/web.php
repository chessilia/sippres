<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\LoginController;

Route::view('/', 'landing')->name('landing');

Route::get('/registrasi', [RegistrasiController::class, 'tampilkanHalamanRegistrasi'])->name('registrasi');
Route::post('/registrasi', [RegistrasiController::class, 'submitRegistrasi'])->name('registrasi.submit');

Route::get('/login', [LoginController::class, 'tampilkanHalamanLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::view('/dashboard/mahasiswa', 'dashboard.mahasiswa')->name('dashboard.mahasiswa');
    Route::view('/dashboard/dosen', 'dashboard.dosen')->name('dashboard.dosen');
    Route::view('/dashboard/admin', 'dashboard.admin')->name('dashboard.admin');
});