<?php

namespace Database\Seeders;

use App\Models\Perkuliahan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // akun Admin
        User::firstOrCreate(['email' => 'admin@sippres.test'], [
            'username' => 'admin',
            'nama'     => 'Admin SIPPRES',
            'password' => 'password123',
            'role'     => 'Admin',
        ]);

        // akun Dosen contoh
        $dosen = User::firstOrCreate(['email' => 'dosen@sippres.test'], [
            'username' => 'pieter',
            'nama'     => 'Pak Pieter',
            'password' => 'password123',
            'role'     => 'Dosen',
        ]);

        // data perkuliahan contoh
        Perkuliahan::firstOrCreate(['kodeMatkul' => 'SI4201', 'kelas' => 'A'], [
            'namaMatkul' => 'Rekayasa Sistem Informasi',
            'idDosen'    => $dosen->idUser,
            'hari'       => 'Senin',
            'jamMulai'   => '08:00',
            'jamSelesai' => '10:30',
            'ruang'      => 'F3.10',
        ]);

        Perkuliahan::firstOrCreate(['kodeMatkul' => 'SI4202', 'kelas' => 'B'], [
            'namaMatkul' => 'Basis Data',
            'idDosen'    => $dosen->idUser,
            'hari'       => 'Rabu',
            'jamMulai'   => '13:00',
            'jamSelesai' => '15:30',
            'ruang'      => 'F3.11',
        ]);

        // tiga akun Mahasiswa contoh
        foreach ([1 => 'Kiel', 2 => 'Rani', 3 => 'Dito'] as $no => $nama) {
            User::firstOrCreate(['email' => "mhs{$no}@sippres.test"], [
                'username' => "mhs{$no}",
                'nama'     => $nama,
                'password' => 'password123',
                'role'     => 'Mahasiswa',
            ]);
        }

        // data uji: semua mahasiswa diikutkan ke semua perkuliahan
        foreach (User::where('role', 'Mahasiswa')->get() as $mhs) {
            foreach (Perkuliahan::all() as $kuliah) {
                DB::table('peserta_kuliah')->insertOrIgnore([
                    'idMahasiswa'   => $mhs->idUser,
                    'idPerkuliahan' => $kuliah->idPerkuliahan,
                ]);
            }
        }
    }
}