<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LupaPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\DataPerkuliahanController;
use App\Http\Controllers\DataPenggunaController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\SesiPresensiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\AjukanIzinController;
use App\Http\Controllers\PengajuanIzinController;
use App\Http\Controllers\KoreksiPresensiController;
use App\Http\Controllers\RekapKehadiranController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\CrisisCenterController;
use App\Http\Controllers\PenangananLaporanController;
use App\Http\Controllers\RekapStatistikController;

/*
|--------------------------------------------------------------------------
| Halaman publik (tanpa login)
|--------------------------------------------------------------------------
*/
Route::view('/', 'landing')->name('landing');

Route::get('/registrasi', [RegistrasiController::class, 'tampilkanHalamanRegistrasi'])->name('registrasi');
Route::post('/registrasi', [RegistrasiController::class, 'submitRegistrasi'])->name('registrasi.submit');

Route::get('/login', [LoginController::class, 'tampilkanHalamanLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// lupa password (harus di luar blok auth)
Route::get('/lupa-password', [LupaPasswordController::class, 'tampilkanFormLupa'])->name('password.request');
Route::post('/lupa-password', [LupaPasswordController::class, 'kirimTautan'])->name('password.email');
Route::get('/reset-password/{token}', [LupaPasswordController::class, 'tampilkanFormReset'])->name('password.reset');
Route::post('/reset-password', [LupaPasswordController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Halaman yang butuh login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // dashboard
    Route::get('/dashboard/mahasiswa', [DashboardController::class, 'mahasiswa'])->name('dashboard.mahasiswa');
    Route::get('/dashboard/dosen', [DashboardController::class, 'dosen'])->name('dashboard.dosen');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

    // profil
    Route::get('/profil', [ProfilController::class, 'getProfil'])->name('profil');
    Route::post('/profil', [ProfilController::class, 'updateProfil'])->name('profil.update');
    Route::get('/foto/{id}', [ProfilController::class, 'foto'])->name('profil.foto');

    // jadwal (semua peran)
    Route::get('/jadwal', [JadwalController::class, 'getJadwal'])->name('jadwal');

    // notifikasi dan lapor kendala (mahasiswa dan dosen)
    Route::get('/notifikasi', [NotifikasiController::class, 'getNotifikasi'])->name('notifikasi');
    Route::get('/crisis-center', [CrisisCenterController::class, 'index'])->name('crisis.pengguna');
    Route::post('/crisis-center', [CrisisCenterController::class, 'kirimLaporan'])->name('crisis.kirim');

    // berkas bukti izin (hak akses dicek di controller)
    Route::get('/izin/{id}/bukti', [PengajuanIzinController::class, 'bukti'])->name('izin.bukti');

    // ---------------- dosen ----------------
    Route::get('/dosen/presensi', [SesiPresensiController::class, 'getDaftarPerkuliahan'])->name('presensi.dosen');
    Route::post('/dosen/presensi', [SesiPresensiController::class, 'buatSesi'])->name('presensi.buat');
    Route::post('/dosen/presensi/{id}/tutup', [SesiPresensiController::class, 'tutupSesi'])->name('presensi.tutup');
    Route::get('/dosen/presensi/{id}/hadir', [SesiPresensiController::class, 'daftarHadir'])->name('presensi.hadir');

    Route::get('/dosen/izin', [PengajuanIzinController::class, 'getDaftarPengajuan'])->name('izin.dosen');
    Route::post('/dosen/izin/{id}', [PengajuanIzinController::class, 'prosesKeputusan'])->name('izin.proses');

    Route::get('/dosen/data-presensi', [KoreksiPresensiController::class, 'getDataPresensi'])->name('koreksi.dosen');
    Route::post('/dosen/data-presensi', [KoreksiPresensiController::class, 'koreksiPresensi'])->name('koreksi.simpan');

    Route::get('/dosen/rekap', [RekapKehadiranController::class, 'getRekapKehadiran'])->name('rekap.dosen');
    Route::get('/dosen/rekap/ekspor', [RekapKehadiranController::class, 'ekspor'])->name('rekap.ekspor');

    // ---------------- mahasiswa ----------------
    Route::get('/mahasiswa/presensi', [PresensiController::class, 'index'])->name('presensi.mahasiswa');
    Route::post('/mahasiswa/presensi', [PresensiController::class, 'kirimPresensi'])->name('presensi.kirim');

    Route::get('/mahasiswa/izin', [AjukanIzinController::class, 'getDataPerkuliahan'])->name('izin.mahasiswa');
    Route::post('/mahasiswa/izin', [AjukanIzinController::class, 'kirimPengajuan'])->name('izin.kirim');
    Route::post('/mahasiswa/izin/{id}/ubah', [AjukanIzinController::class, 'ubahPengajuan'])->name('izin.ubah');
    Route::post('/mahasiswa/izin/{id}/batal', [AjukanIzinController::class, 'batalkanPengajuan'])->name('izin.batal');

    Route::get('/mahasiswa/riwayat', [RiwayatController::class, 'getRiwayat'])->name('riwayat');

    // ---------------- admin ----------------
    Route::get('/admin/perkuliahan', [DataPerkuliahanController::class, 'getDaftarPerkuliahan'])->name('perkuliahan.daftar');
    Route::post('/admin/perkuliahan/{id}', [DataPerkuliahanController::class, 'prosesPerubahan'])->name('perkuliahan.proses');

    Route::get('/admin/pengguna', [DataPenggunaController::class, 'getDaftarPengguna'])->name('pengguna.daftar');
    Route::post('/admin/pengguna/{id}', [DataPenggunaController::class, 'perbaruiDataPengguna'])->name('pengguna.perbarui');
    Route::post('/admin/pengguna/{id}/hapus', [DataPenggunaController::class, 'hapusDataPengguna'])->name('pengguna.hapus');

    Route::get('/admin/crisis-center', [PenangananLaporanController::class, 'getDaftarLaporan'])->name('crisis.admin');
    Route::post('/admin/crisis-center/{id}/tindak-lanjut', [PenangananLaporanController::class, 'simpanTindakLanjut'])->name('crisis.tindaklanjut');
    Route::post('/admin/crisis-center/{id}/tidak-valid', [PenangananLaporanController::class, 'prosesLaporanTidakValid'])->name('crisis.tidakvalid');

    Route::get('/admin/statistik', [RekapStatistikController::class, 'getRekapStatistik'])->name('statistik.admin');
    Route::get('/admin/statistik/ekspor', [RekapStatistikController::class, 'ekspor'])->name('statistik.ekspor');
});