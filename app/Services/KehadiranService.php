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
        // rata-rata kehadiran (%) seluruh mahasiswa pada sekumpulan perkuliahan; null jika belum ada data
    public function rataKehadiran($daftarPerkuliahan): ?int
    {
        $hadir = 0;
        $total = 0;

        foreach ($daftarPerkuliahan as $p) {
            [$pertemuan, , $matriks] = $this->matriks($p->idPerkuliahan);

            foreach ($matriks as $baris) {
                foreach ($pertemuan as $s) {
                    if ($s->masihBerlaku()) {
                        continue;                       // sesi yang masih berlangsung belum dihitung
                    }
                    $total++;
                    if ($baris[$s->pertemuan]['status'] === 'Hadir') {
                        $hadir++;
                    }
                }
            }
        }

        return $total ? (int) round($hadir / $total * 100) : null;
    }

    // jumlah Hadir/Izin/Sakit/Alpa seorang mahasiswa pada semua perkuliahan yang diikuti
    public function ringkasanMahasiswa($idMahasiswa): array
    {
        $hasil = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];

        $ikut = DB::table('peserta_kuliah')->where('idMahasiswa', $idMahasiswa)->pluck('idPerkuliahan');

        foreach ($ikut as $idPerkuliahan) {
            [$pertemuan, , $matriks] = $this->matriks($idPerkuliahan);

            foreach ($pertemuan as $s) {
                if ($s->masihBerlaku()) {
                    continue;
                }
                $status = $matriks[$idMahasiswa][$s->pertemuan]['status'] ?? null;
                if ($status && isset($hasil[$status])) {
                    $hasil[$status]++;
                }
            }
        }

        return $hasil;
    }
}