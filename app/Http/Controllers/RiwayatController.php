<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    // getRiwayat(idMahasiswa, jenis) -> UI-009
    public function getRiwayat(Request $request)
    {
        abort_unless(Auth::user()->role === 'Mahasiswa', 403);

        $jenis = $request->input('jenis') === 'izin' ? 'izin' : 'presensi';

        $daftar = $jenis === 'izin'
            ? $this->ambilRiwayatIzin(Auth::id())
            : $this->ambilRiwayatPresensi(Auth::id());

        $detail = $request->filled('id') ? $this->getDetailRiwayat($request->id, $jenis, $daftar) : null;

        return view('mahasiswa.riwayat', compact('jenis', 'daftar', 'detail'));
    }

    // ambilRiwayatPresensi(idMahasiswa)
    private function ambilRiwayatPresensi($idMahasiswa)
    {
        return Presensi::with('sesi.perkuliahan')
            ->where('idMahasiswa', $idMahasiswa)
            ->orderByDesc('waktu')->get()
            ->each(function ($p) {
                // nomor pertemuan = urutan sesi pada perkuliahan tersebut
                $p->pertemuan = SesiPresensi::where('idPerkuliahan', $p->sesi->idPerkuliahan)
                                            ->where('idSesi', '<=', $p->idSesi)->count();
            });
    }

    // ambilRiwayatIzin(idMahasiswa)
    private function ambilRiwayatIzin($idMahasiswa)
    {
        return PengajuanIzin::with('perkuliahan')
            ->where('idMahasiswa', $idMahasiswa)
            ->orderByDesc('idIzin')->get();
    }

    // getDetailRiwayat(idRiwayat, jenis): hanya milik mahasiswa yang login
    private function getDetailRiwayat($id, $jenis, $daftar)
    {
        return $daftar->firstWhere($jenis === 'izin' ? 'idIzin' : 'idPresensi', $id);
    }
}