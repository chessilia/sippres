<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    // getJadwal(idPengguna) -> UI-006
    public function getJadwal(Request $request)
    {
        $daftarJadwal = $this->ambilJadwal();

        // hari yang dipilih (bawaan: hari ini)
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hari     = $request->input('hari', $namaHari[now()->dayOfWeek]);

        return view('jadwal.index', [
            'daftarJadwal' => $daftarJadwal,
            'jadwalHari'   => $daftarJadwal->where('hari', $hari),
            'hari'         => $hari,
            'detail'       => $this->getDetailPerkuliahan($request->id, $daftarJadwal),
        ]);
    }

    // getDetailPerkuliahan(idPerkuliahan)
    private function getDetailPerkuliahan($id, $daftarJadwal)
    {
        // hanya boleh melihat detail yang ada di jadwalnya sendiri
        return $id ? $daftarJadwal->firstWhere('idPerkuliahan', $id) : null;
    }

    // Cek apakah sesi presensi sudah ditutup / melewati waktu berakhir
    private function sesiSudahSelesai($sesi): bool
    {
        if (isset($sesi->status) && in_array($sesi->status, ['Ditutup', 'Selesai', 'Nonaktif'], true)) {
            return true;
        }

        if (!empty($sesi->waktuSelesai)) {
            return Carbon::parse($sesi->waktuSelesai)->isPast();
        }

        if (!empty($sesi->batasWaktu)) {
            return Carbon::parse($sesi->batasWaktu)->isPast();
        }

        return false;
    }

    // ambilJadwal(idPengguna): jadwal sesuai peran pengguna + statistik pertemuan, peserta & kehadiran
    private function ambilJadwal()
    {
        $user  = Auth::user();
        $query = Perkuliahan::with('dosen');

        if ($user->role === 'Dosen') {
            $query->where('idDosen', $user->idUser);
        } elseif ($user->role === 'Mahasiswa') {
            $ikut = DB::table('peserta_kuliah')
                      ->where('idMahasiswa', $user->idUser)
                      ->pluck('idPerkuliahan');
            $query->whereIn('idPerkuliahan', $ikut);
        }

        $daftar    = $query->orderBy('jamMulai')->get();
        $tabelUser = (new User())->getTable();

        // Lengkapi tiap perkuliahan dengan total pertemuan, daftar & persentase peserta (dosen), serta rekap kehadiran (mahasiswa)
        return $daftar->each(function ($p) use ($user, $tabelUser) {
            $semuaSesi = SesiPresensi::where('idPerkuliahan', $p->idPerkuliahan)
                ->orderBy('idSesi')
                ->get();

            $p->totalPertemuan = $semuaSesi->count();

            if ($user->role === 'Dosen') {
                $idSesiList = $semuaSesi->pluck('idSesi');

                $semuaPresensi = Presensi::whereIn('idSesi', $idSesiList)
                    ->get()
                    ->groupBy('idMahasiswa');

                $peserta = DB::table('peserta_kuliah')
                    ->join($tabelUser, 'peserta_kuliah.idMahasiswa', '=', $tabelUser . '.idUser')
                    ->where('peserta_kuliah.idPerkuliahan', $p->idPerkuliahan)
                    ->select($tabelUser . '.*')
                    ->orderBy($tabelUser . '.nama')
                    ->get()
                    ->map(function ($mhs) use ($semuaSesi, $semuaPresensi) {
                        $rekamMhs = ($semuaPresensi->get($mhs->idUser) ?? collect())->keyBy('idSesi');

                        $hadir = 0;
                        $izin  = 0;
                        $sakit = 0;
                        $alpa  = 0;
                        $sesiTerhitung = 0;

                        foreach ($semuaSesi as $sesi) {
                            $record = $rekamMhs->get($sesi->idSesi);
                            if ($record) {
                                $sesiTerhitung++;
                                if ($record->status === 'Hadir') $hadir++;
                                elseif ($record->status === 'Izin') $izin++;
                                elseif ($record->status === 'Sakit') $sakit++;
                                elseif ($record->status === 'Alpa') $alpa++;
                            } elseif ($this->sesiSudahSelesai($sesi)) {
                                $sesiTerhitung++;
                                $alpa++;
                            }
                        }

                        $mhs->hadir = $hadir;
                        $mhs->izin  = $izin;
                        $mhs->sakit = $sakit;
                        $mhs->alpa  = $alpa;
                        $mhs->sesiTerhitung = $sesiTerhitung;
                        $mhs->persentaseHadir = $sesiTerhitung > 0
                            ? round(($hadir / $sesiTerhitung) * 100)
                            : null;

                        return $mhs;
                    });

                $p->daftarPeserta = $peserta;
                $p->jumlahPeserta = $peserta->count();
            } elseif ($user->role === 'Mahasiswa') {
                $idSesiList = $semuaSesi->pluck('idSesi');
                $rekamPresensi = Presensi::where('idMahasiswa', $user->idUser)
                    ->whereIn('idSesi', $idSesiList)
                    ->get()
                    ->keyBy('idSesi');

                $hadir = 0;
                $izin  = 0;
                $sakit = 0;
                $alpa  = 0;

                foreach ($semuaSesi as $sesi) {
                    $record = $rekamPresensi->get($sesi->idSesi);
                    if ($record) {
                        if ($record->status === 'Hadir') $hadir++;
                        elseif ($record->status === 'Izin') $izin++;
                        elseif ($record->status === 'Sakit') $sakit++;
                        elseif ($record->status === 'Alpa') $alpa++;
                    } elseif ($this->sesiSudahSelesai($sesi)) {
                        $alpa++;
                    }
                }

                $p->rekapKehadiran = [
                    'Hadir' => $hadir,
                    'Izin'  => $izin,
                    'Sakit' => $sakit,
                    'Alpa'  => $alpa,
                ];
            }
        });
    }
}