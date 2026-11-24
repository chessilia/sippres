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
        // 1. Akun Admin
        User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'username' => 'adam',
            'nama'     => 'Admin SIPPRES',
            'password' => 'password123',
            'role'     => 'Admin',
        ]);

        // 2. Akun Dosen
        $dosen = User::firstOrCreate(['email' => 'dosen@gmail.com'], [
            'username' => 'pieter',
            'nama'     => 'Pak Pieter',
            'password' => 'password123',
            'role'     => 'Dosen',
        ]);

        // 3. Data 5 Perkuliahan
        $daftarMatkul = [
            [
                'kodeMatkul' => 'SI4201',
                'kelas'      => 'A',
                'namaMatkul' => 'Rekayasa Sistem Informasi',
                'hari'       => 'Senin',
                'jamMulai'   => '08:00',
                'jamSelesai' => '10:30',
                'ruang'      => 'F3.10',
            ],
            [
                'kodeMatkul' => 'SI4202',
                'kelas'      => 'B',
                'namaMatkul' => 'Desain Basis Data',
                'hari'       => 'Rabu',
                'jamMulai'   => '13:00',
                'jamSelesai' => '15:30',
                'ruang'      => 'F3.11',
            ],
            [
                'kodeMatkul' => 'SI4203',
                'kelas'      => 'A',
                'namaMatkul' => 'Pemrograman Web Lanjut',
                'hari'       => 'Selasa',
                'jamMulai'   => '10:30',
                'jamSelesai' => '13:00',
                'ruang'      => 'G1.4',
            ],
            [
                'kodeMatkul' => 'SI4204',
                'kelas'      => 'C',
                'namaMatkul' => 'Analisis dan Desain Sistem',
                'hari'       => 'Kamis',
                'jamMulai'   => '08:00',
                'jamSelesai' => '10:30',
                'ruang'      => 'F2.05',
            ],
            [
                'kodeMatkul' => 'SI4205',
                'kelas'      => 'A',
                'namaMatkul' => 'Manajemen Proyek TI',
                'hari'       => 'Jumat',
                'jamMulai'   => '09:00',
                'jamSelesai' => '11:30',
                'ruang'      => 'F4.02',
            ],
        ];

        $perkuliahanList = [];
        foreach ($daftarMatkul as $matkul) {
            $perkuliahanList[] = Perkuliahan::firstOrCreate(
                [
                    'kodeMatkul' => $matkul['kodeMatkul'],
                    'kelas'      => $matkul['kelas'],
                ],
                [
                    'namaMatkul' => $matkul['namaMatkul'],
                    'idDosen'    => $dosen->idUser,
                    'hari'       => $matkul['hari'],
                    'jamMulai'   => $matkul['jamMulai'],
                    'jamSelesai' => $matkul['jamSelesai'],
                    'ruang'      => $matkul['ruang'],
                ]
            );
        }

        // 4. Akun 3 Mahasiswa & Otomatis Masuk ke 5 Perkuliahan
        $daftarMahasiswa = [
            1 => 'Kiel',
            2 => 'Juno',
            3 => 'Dito',
        ];

        foreach ($daftarMahasiswa as $no => $nama) {
            $mhs = User::firstOrCreate(
                ['email' => "mhs{$no}@gmail.com"],
                [
                    'username' => "mhs{$no}",
                    'nama'     => $nama,
                    'password' => 'password123',
                    'role'     => 'Mahasiswa',
                ]
            );

            foreach ($perkuliahanList as $kuliah) {
                DB::table('peserta_kuliah')->insertOrIgnore([
                    'idMahasiswa'   => $mhs->idUser,
                    'idPerkuliahan' => $kuliah->idPerkuliahan,
                ]);
            }
        }
    }
}