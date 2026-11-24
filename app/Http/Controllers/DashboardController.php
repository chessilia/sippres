<?php

namespace App\Http\Controllers;

use App\Models\LaporanKendala;
use App\Models\PengajuanIzin;
use App\Models\Perkuliahan;
use App\Models\SesiPresensi;
use App\Models\User;
use App\Services\KehadiranService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private KehadiranService $kehadiran)
    {
    }

    private function hariIni(): string
    {
        return ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][now()->dayOfWeek];
    }

    // sesi aktif milik sekumpulan perkuliahan
    private function sesiAktif($idPerkuliahan)
    {
        return SesiPresensi::with('perkuliahan')
            ->where('status', 'Aktif')
            ->whereIn('idPerkuliahan', $idPerkuliahan)
            ->get()
            ->filter(fn ($s) => $s->masihBerlaku())
            ->values();
    }

    // Dashboard Mahasiswa -> UI-005
    public function mahasiswa()
    {
        abort_unless(Auth::user()->role === 'Mahasiswa', 403);

        $id   = Auth::id();
        $ikut = DB::table('peserta_kuliah')->where('idMahasiswa', $id)->pluck('idPerkuliahan');

        $sesiAktif = $this->sesiAktif($ikut);
        $sudah     = DB::table('presensi')->where('idMahasiswa', $id)
                       ->whereIn('idSesi', $sesiAktif->pluck('idSesi'))->pluck('idSesi');
        $sesiAktif->each(fn ($s) => $s->sudahPresensi = $sudah->contains($s->idSesi));

        // kuliah berikutnya: urut dari hari terdekat
        $urutHari = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
        $aktif    = $sesiAktif->pluck('idPerkuliahan');

        $berikutnya = Perkuliahan::whereIn('idPerkuliahan', $ikut)->get()
            ->map(function ($p) use ($urutHari, $aktif) {
                $selisih = ($urutHari[$p->hari] - now()->dayOfWeekIso + 7) % 7;

                // kuliah hari ini yang sudah lewat jamnya dihitung minggu depan
                if ($selisih === 0 && now()->format('H:i:s') > $p->jamSelesai) {
                    $selisih = 7;
                }

                $p->selisihHari = $selisih;
                $p->aktif       = $aktif->contains($p->idPerkuliahan);

                return $p;
            })
            ->sortBy([['selisihHari', 'asc'], ['jamMulai', 'asc']])
            ->take(3)->values();

        return view('dashboard.mahasiswa', [
            'sesiAktif'      => $sesiAktif,
            'ringkasan'      => $this->kehadiran->ringkasanMahasiswa($id),
            'berikutnya'     => $berikutnya,
            'presensiHariIni' => DB::table('presensi')->where('idMahasiswa', $id)->whereDate('waktu', today())->count(),
        ]);
    }

    // Dashboard Dosen -> UI-013
    public function dosen()
    {
        abort_unless(Auth::user()->role === 'Dosen', 403);

        $kuliah = Perkuliahan::where('idDosen', Auth::id())->get();
        $ids    = $kuliah->pluck('idPerkuliahan');

        $sesiAktif = $this->sesiAktif($ids)->each(function ($s) {
            $s->hadir = DB::table('presensi')->where('idSesi', $s->idSesi)->where('status', 'Hadir')->count();
            $s->total = DB::table('peserta_kuliah')->where('idPerkuliahan', $s->idPerkuliahan)->count();
        });

        $jadwalHariIni = $kuliah->where('hari', $this->hariIni())->sortBy('jamMulai')->values()
            ->each(fn ($p) => $p->sesiAktif = $sesiAktif->contains('idPerkuliahan', $p->idPerkuliahan));

        return view('dashboard.dosen', [
            'sesiAktif'     => $sesiAktif,
            'izinMenunggu'  => PengajuanIzin::with(['mahasiswa', 'perkuliahan'])
                                ->where('status', 'Menunggu')->whereIn('idPerkuliahan', $ids)
                                ->orderByDesc('idIzin')->get(),
            'rata'          => $this->kehadiran->rataKehadiran($kuliah),
            'jadwalHariIni' => $jadwalHariIni,
        ]);
    }

    // Dashboard Admin -> UI-018
    public function admin()
    {
        abort_unless(Auth::user()->role === 'Admin', 403);

        $laporan = LaporanKendala::with('pelapor')->orderByDesc('idLaporan')->get();
        $baru    = $laporan->where('status', 'Baru')->count();

        return view('dashboard.admin', [
            'totalLaporan' => $laporan->count(),
            'laporanBaru'  => $baru,
            'ditindak'     => $laporan->count() - $baru,
            'pengguna'     => User::all()->countBy('role'),
            'rata'         => $this->kehadiran->rataKehadiran(Perkuliahan::all()),
            'terbaru'      => $laporan->take(5),
        ]);
    }
}