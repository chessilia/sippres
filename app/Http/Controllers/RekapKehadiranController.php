<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use App\Services\KehadiranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapKehadiranController extends Controller
{
    public function __construct(private KehadiranService $kehadiran)
    {
    }

    private function cekDosen(): void
    {
        abort_unless(Auth::user()->role === 'Dosen', 403);
    }

    // getPilihanPerkuliahan(idDosen)
    private function getPilihanPerkuliahan($idDosen)
    {
        return Perkuliahan::where('idDosen', $idDosen)->orderBy('idPerkuliahan')->get();
    }

    // getRekapKehadiran(idPerkuliahan) -> UI-017
    public function getRekapKehadiran(Request $request)
    {
        $this->cekDosen();

        $daftar      = $this->getPilihanPerkuliahan(Auth::id());
        $perkuliahan = $daftar->firstWhere('idPerkuliahan', $request->perkuliahan) ?? $daftar->first();

        $pertemuan = collect();
        $mahasiswa = collect();
        $matriks   = [];
        $rekap     = [];
        $ringkasan = null;
        $detail    = null;

        if ($perkuliahan) {
            [$pertemuan, $mahasiswa, $matriks] = $this->kehadiran->matriks($perkuliahan->idPerkuliahan);
            $rekap = $this->hitungRekap($pertemuan, $mahasiswa, $matriks);

            $ringkasan = [
                'totalMahasiswa' => $mahasiswa->count(),
                'rataRata'       => $mahasiswa->count() ? round(collect($rekap)->avg('persen')) : 0,
                'izinSakit'      => collect($rekap)->sum('Izin') + collect($rekap)->sum('Sakit'),
                'alpa'           => collect($rekap)->sum('Alpa'),
            ];

            $detail = $request->mahasiswa
                ? $mahasiswa->firstWhere('idUser', $request->mahasiswa) : null;
        }

        return view('dosen.rekap', compact(
            'daftar', 'perkuliahan', 'pertemuan', 'mahasiswa', 'matriks', 'rekap', 'ringkasan', 'detail'
        ));
    }

    // hitungRekap(dataKehadiran): jumlah Hadir/Izin/Sakit/Alpa dan persentase per mahasiswa
    private function hitungRekap($pertemuan, $mahasiswa, $matriks): array
    {
        // sesi yang masih berlangsung belum dihitung
        $selesai = $pertemuan->filter(fn ($s) => !$s->masihBerlaku());
        $total   = $selesai->count();

        $rekap = [];
        foreach ($mahasiswa as $m) {
            $h = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];

            foreach ($selesai as $s) {
                $status = $matriks[$m->idUser][$s->pertemuan]['status'];
                if (isset($h[$status])) {
                    $h[$status]++;
                }
            }

            $h['persen'] = $total > 0 ? round($h['Hadir'] / $total * 100) : 0;
            $rekap[$m->idUser] = $h;
        }

        return $rekap;
    }

    // eksporRekap(idPerkuliahan, format): berkas CSV yang bisa dibuka di Excel
    public function ekspor(Request $request)
    {
        $this->cekDosen();

        $perkuliahan = Perkuliahan::where('idPerkuliahan', $request->perkuliahan)
                                  ->where('idDosen', Auth::id())->firstOrFail();

        [$pertemuan, $mahasiswa, $matriks] = $this->kehadiran->matriks($perkuliahan->idPerkuliahan);
        $rekap = $this->hitungRekap($pertemuan, $mahasiswa, $matriks);

        $namaFile = 'rekap_' . $perkuliahan->kodeMatkul . '_' . $perkuliahan->kelas . '.csv';

        return response()->streamDownload(function () use ($pertemuan, $mahasiswa, $matriks, $rekap) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");      // agar huruf terbaca benar di Excel
            fwrite($out, "sep=;\n");           // agar Excel memisah kolom dengan benar

            fputcsv($out, array_merge(
                ['Nama', 'Username'],
                $pertemuan->map(fn ($s) => 'P' . $s->pertemuan)->all(),
                ['Hadir', 'Izin', 'Sakit', 'Alpa', 'Persentase (%)']
            ), ';');

            foreach ($mahasiswa as $m) {
                $baris = [$m->nama, $m->username];
                foreach ($pertemuan as $s) {
                    $baris[] = $matriks[$m->idUser][$s->pertemuan]['status'];
                }
                $h = $rekap[$m->idUser];
                fputcsv($out, array_merge($baris, [$h['Hadir'], $h['Izin'], $h['Sakit'], $h['Alpa'], $h['persen']]), ';');
            }

            fclose($out);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}