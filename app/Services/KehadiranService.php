<?php

namespace App\Services;

use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KehadiranService
{
    // daftar sesi sebuah perkuliahan; urutannya = nomor pertemuan (P1, P2, ...)
    public function daftarPertemuan($idPerkuliahan)
    {
        return SesiPresensi::where('idPerkuliahan', $idPerkuliahan)
            ->orderBy('idSesi')->get()->values()
            ->each(fn ($sesi, $i) => $sesi->pertemuan = $i + 1);
    }

    // mahasiswa yang mengikuti perkuliahan
    public function daftarMahasiswa($idPerkuliahan)
    {
        $ids = DB::table('peserta_kuliah')
                 ->where('idPerkuliahan', $idPerkuliahan)->pluck('idMahasiswa');

        return User::whereIn('idUser', $ids)->orderBy('nama')->get();
    }

    // matriks status akhir: $matriks[idMahasiswa][nomorPertemuan] = ['status' => ..., 'presensi' => ...]
    public function matriks($idPerkuliahan): array
    {
        $pertemuan = $this->daftarPertemuan($idPerkuliahan);
        $mahasiswa = $this->daftarMahasiswa($idPerkuliahan);

        $presensi = Presensi::whereIn('idSesi', $pertemuan->pluck('idSesi'))->get()
            ->keyBy(fn ($p) => $p->idMahasiswa . '-' . $p->idSesi);

        $izin = PengajuanIzin::where('idPerkuliahan', $idPerkuliahan)
            ->where('status', 'Disetujui')->get()
            ->keyBy(fn ($z) => $z->idMahasiswa . '-' . $z->pertemuan);

        $hasil = [];
        foreach ($mahasiswa as $m) {
            foreach ($pertemuan as $s) {
                $p = $presensi->get($m->idUser . '-' . $s->idSesi);

                if ($p) {
                    $status = $p->status;                       // termasuk hasil koreksi dosen
                } elseif ($izin->has($m->idUser . '-' . $s->pertemuan)) {
                    $status = 'Izin';                           // izin disetujui
                } elseif ($s->masihBerlaku()) {
                    $status = 'Belum';                          // sesi masih berlangsung
                } else {
                    $status = 'Alpa';
                }

                $hasil[$m->idUser][$s->pertemuan] = ['status' => $status, 'presensi' => $p];
            }
        }

        return [$pertemuan, $mahasiswa, $hasil];
    }
}