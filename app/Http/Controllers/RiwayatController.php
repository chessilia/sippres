<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use App\Models\Perkuliahan;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    // getRiwayat(idMahasiswa, jenis) -> UI-009
    public function getRiwayat(Request $request)
    {
        abort_unless(Auth::user()->role === 'Mahasiswa', 403);

        $idMahasiswa = Auth::id();
        $jenis       = $request->input('jenis') === 'izin' ? 'izin' : 'presensi';
        $idMatkul    = $request->input('matkul');

        // Ambil semua mata kuliah yang diikuti mahasiswa untuk dropdown filter
        $ikutIds = DB::table('peserta_kuliah')
            ->where('idMahasiswa', $idMahasiswa)
            ->pluck('idPerkuliahan');

        $daftarMatkul = Perkuliahan::whereIn('idPerkuliahan', $ikutIds)
            ->orderBy('namaMatkul')
            ->get();

        $filterMatkul = $idMatkul ? $daftarMatkul->firstWhere('idPerkuliahan', $idMatkul) : null;

        $daftar = $jenis === 'izin'
            ? $this->ambilRiwayatIzin($idMahasiswa, $filterMatkul?->idPerkuliahan)
            : $this->ambilRiwayatPresensi($idMahasiswa, $filterMatkul?->idPerkuliahan);

        $detail = $request->filled('id') ? $this->getDetailRiwayat($request->id, $jenis, $daftar) : null;

        return view('mahasiswa.riwayat', compact('jenis', 'daftar', 'detail', 'daftarMatkul', 'filterMatkul'));
    }

    // ambilRiwayatPresensi(idMahasiswa, idPerkuliahan)
    private function ambilRiwayatPresensi($idMahasiswa, $idMatkul = null)
    {
        $query = Presensi::with('sesi.perkuliahan')
            ->where('idMahasiswa', $idMahasiswa);

        if ($idMatkul) {
            $query->whereHas('sesi', function ($q) use ($idMatkul) {
                $q->where('idPerkuliahan', $idMatkul);
            });
        }

        return $query->orderByDesc('waktu')->get()
            ->each(function ($p) {
                // nomor pertemuan = urutan sesi pada perkuliahan tersebut
                $p->pertemuan = SesiPresensi::where('idPerkuliahan', $p->sesi->idPerkuliahan)
                                            ->where('idSesi', '<=', $p->idSesi)->count();
            });
    }

    // ambilRiwayatIzin(idMahasiswa, idPerkuliahan)
    private function ambilRiwayatIzin($idMahasiswa, $idMatkul = null)
    {
        $query = PengajuanIzin::with('perkuliahan')
            ->where('idMahasiswa', $idMahasiswa);

        if ($idMatkul) {
            $query->where('idPerkuliahan', $idMatkul);
        }

        return $query->orderByDesc('idIzin')->get();
    }

    // getDetailRiwayat(idRiwayat, jenis): hanya milik mahasiswa yang login
    private function getDetailRiwayat($id, $jenis, $daftar)
    {
        return $daftar->firstWhere($jenis === 'izin' ? 'idIzin' : 'idPresensi', $id);
    }
}