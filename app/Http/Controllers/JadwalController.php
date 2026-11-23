<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use Illuminate\Http\Request;
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

    // ambilJadwal(idPengguna): jadwal sesuai peran pengguna
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

        return $query->orderBy('jamMulai')->get();
    }
}