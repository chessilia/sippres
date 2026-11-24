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
use App\Http\Controllers\KoreksiPresensiController;
use App\Http\Controllers\RekapKehadiranController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\CrisisCenterController;
use App\Http\Controllers\PenangananLaporanController;
use App\Http\Controllers\DataPenggunaController;
use App\Http\Controllers\RekapStatistikController;

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
    // dosen: koreksi presensi dan rekap kehadiran
    Route::get('/dosen/data-presensi', [KoreksiPresensiController::class, 'getDataPresensi'])->name('koreksi.dosen');
    Route::post('/dosen/data-presensi', [KoreksiPresensiController::class, 'koreksiPresensi'])->name('koreksi.simpan');
    Route::get('/dosen/rekap', [RekapKehadiranController::class, 'getRekapKehadiran'])->name('rekap.dosen');
    Route::get('/dosen/rekap/ekspor', [RekapKehadiranController::class, 'ekspor'])->name('rekap.ekspor');

    // mahasiswa: riwayat
    Route::get('/mahasiswa/riwayat', [RiwayatController::class, 'getRiwayat'])->name('riwayat');
        // notifikasi (mahasiswa dan dosen)
    Route::get('/notifikasi', [NotifikasiController::class, 'getNotifikasi'])->name('notifikasi');

    // lapor kendala (mahasiswa dan dosen)
    Route::get('/crisis-center', [CrisisCenterController::class, 'index'])->name('crisis.pengguna');
    Route::post('/crisis-center', [CrisisCenterController::class, 'kirimLaporan'])->name('crisis.kirim');

    // admin: menangani laporan
    Route::get('/admin/crisis-center', [PenangananLaporanController::class, 'getDaftarLaporan'])->name('crisis.admin');
    Route::post('/admin/crisis-center/{id}/tindak-lanjut', [PenangananLaporanController::class, 'simpanTindakLanjut'])->name('crisis.tindaklanjut');
    Route::post('/admin/crisis-center/{id}/tidak-valid', [PenangananLaporanController::class, 'prosesLaporanTidakValid'])->name('crisis.tidakvalid');
        // admin: data pengguna
    Route::get('/admin/pengguna', [DataPenggunaController::class, 'getDaftarPengguna'])->name('pengguna.daftar');
    Route::post('/admin/pengguna/{id}', [DataPenggunaController::class, 'perbaruiDataPengguna'])->name('pengguna.perbarui');
    Route::post('/admin/pengguna/{id}/hapus', [DataPenggunaController::class, 'hapusDataPengguna'])->name('pengguna.hapus');

    // admin: rekap dan statistik
    Route::get('/admin/statistik', [RekapStatistikController::class, 'getRekapStatistik'])->name('statistik.admin');
    Route::get('/admin/statistik/ekspor', [RekapStatistikController::class, 'ekspor'])->name('statistik.ekspor');
});