<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DataPerkuliahanController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\SesiPresensiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\AjukanIzinController;
use App\Http\Controllers\PengajuanIzinController;

Route::view('/', 'landing')->name('landing');

Route::get('/registrasi', [RegistrasiController::class, 'tampilkanHalamanRegistrasi'])->name('registrasi');
Route::post('/registrasi', [RegistrasiController::class, 'submitRegistrasi'])->name('registrasi.submit');

Route::get('/login', [LoginController::class, 'tampilkanHalamanLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/jadwal', [JadwalController::class, 'getJadwal'])->name('jadwal');

    Route::get('/dosen/presensi', [SesiPresensiController::class, 'getDaftarPerkuliahan'])->name('presensi.dosen');
    Route::post('/dosen/presensi', [SesiPresensiController::class, 'buatSesi'])->name('presensi.buat');
    Route::post('/dosen/presensi/{id}/tutup', [SesiPresensiController::class, 'tutupSesi'])->name('presensi.tutup');

    Route::get('/admin/perkuliahan', [DataPerkuliahanController::class, 'getDaftarPerkuliahan'])->name('perkuliahan.daftar');
    Route::post('/admin/perkuliahan/{id}', [DataPerkuliahanController::class, 'prosesPerubahan'])->name('perkuliahan.proses');
    Route::view('/dashboard/mahasiswa', 'dashboard.mahasiswa')->name('dashboard.mahasiswa');
    Route::view('/dashboard/dosen', 'dashboard.dosen')->name('dashboard.dosen');
    Route::view('/dashboard/admin', 'dashboard.admin')->name('dashboard.admin');
    Route::get('/mahasiswa/presensi', [PresensiController::class, 'index'])->name('presensi.mahasiswa');
    Route::post('/mahasiswa/presensi', [PresensiController::class, 'kirimPresensi'])->name('presensi.kirim');
    // mahasiswa: ajukan izin
    Route::get('/mahasiswa/izin', [AjukanIzinController::class, 'getDataPerkuliahan'])->name('izin.mahasiswa');
    Route::post('/mahasiswa/izin', [AjukanIzinController::class, 'kirimPengajuan'])->name('izin.kirim');
    Route::post('/mahasiswa/izin/{id}/ubah', [AjukanIzinController::class, 'ubahPengajuan'])->name('izin.ubah');
    Route::post('/mahasiswa/izin/{id}/batal', [AjukanIzinController::class, 'batalkanPengajuan'])->name('izin.batal');

    // dosen: proses pengajuan
    Route::get('/dosen/izin', [PengajuanIzinController::class, 'getDaftarPengajuan'])->name('izin.dosen');
    Route::post('/dosen/izin/{id}', [PengajuanIzinController::class, 'prosesKeputusan'])->name('izin.proses');

    // berkas bukti (dicek hak aksesnya di controller)
    Route::get('/izin/{id}/bukti', [PengajuanIzinController::class, 'bukti'])->name('izin.bukti');
});