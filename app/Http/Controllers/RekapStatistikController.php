<?php

namespace App\Http\Controllers;

use App\Models\LaporanKendala;
use App\Models\PengajuanIzin;
use App\Models\Perkuliahan;
use App\Models\Presensi;
use App\Models\User;
use App\Services\KehadiranService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapStatistikController extends Controller
{
    public function __construct(private KehadiranService $kehadiran)
    {
    }

    private function cekAdmin(): void
    {
        abort_unless(Auth::user()->role === 'Admin', 403);
    }

    // getRekapStatistik(jenisData, periodeAwal, periodeAkhir) -> UI-022
    public function getRekapStatistik(Request $request)
    {
        $this->cekAdmin();

        $f     = $this->bacaFilter($request);
        $data  = null;
        $galat = false;

        try {
            $data = $this->kumpulkanData($f);
        } catch (\Throwable $e) {
            $galat = true;      // "Terjadi kesalahan saat mengambil data, silakan coba kembali"
        }

        return view('admin.statistik', [
            'f'                 => $f,
            'data'              => $data,
            'galat'             => $galat,
            'daftarPerkuliahan' => Perkuliahan::orderBy('namaMatkul')->get(),
            'daftarDosen'       => User::where('role', 'Dosen')->orderBy('nama')->get(),
        ]);
    }

    // membaca dan merapikan filter dari URL
    private function bacaFilter(Request $request): array
    {
        $bulan = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->bulan)
            ? $request->bulan : now()->format('Y-m');

        $awal = Carbon::createFromFormat('Y-m-d H:i:s', $bulan . '-01 00:00:00')->startOfMonth();

        return [
            'jenis'       => in_array($request->jenis, ['Presensi', 'Izin', 'Crisis Center']) ? $request->jenis : 'Semua',
            'bulan'       => $bulan,
            'awal'        => $awal,
            'akhir'       => $awal->copy()->endOfMonth(),
            'dosen'       => $request->dosen,
            'perkuliahan' => $request->perkuliahan,
            'status'      => in_array($request->status, ['Hadir', 'Izin', 'Sakit', 'Alpa']) ? $request->status : null,
        ];
    }

    // mengambil seluruh data sesuai filter lalu mengolahnya
    private function kumpulkanData(array $f): array
    {
        $kuliah = Perkuliahan::with('dosen')
            ->when($f['dosen'], fn ($q, $d) => $q->where('idDosen', $d))
            ->when($f['perkuliahan'], fn ($q, $p) => $q->where('idPerkuliahan', $p))
            ->get();

        $awalLalu  = $f['awal']->copy()->subMonthNoOverflow()->startOfMonth();
        $akhirLalu = $awalLalu->copy()->endOfMonth();

        $slot     = $this->ambilDataPresensi($f['awal'], $f['akhir'], $kuliah);
        $slotLalu = $this->ambilDataPresensi($awalLalu, $akhirLalu, $kuliah);
        $izin     = $this->ambilDataIzin($f['awal'], $f['akhir'], $kuliah->pluck('idPerkuliahan'));
        $laporan  = $this->ambilDataLaporan($f['awal'], $f['akhir']);

        $tercatat = Presensi::whereHas('sesi', fn ($q) => $q
            ->whereBetween('waktuMulai', [$f['awal'], $f['akhir']])
            ->whereIn('idPerkuliahan', $kuliah->pluck('idPerkuliahan')))->count();

        $data = $this->olahRekapStatistik($slot, $slotLalu, $izin, $laporan, $tercatat);

        // daftar kehadiran rinci, dapat difilter menurut status kehadiran
        $data['presensi']['detail'] = $slot
            ->when($f['status'], fn ($c, $s) => $c->where('status', $s))
            ->sortBy(fn ($x) => $x['sesi']->waktuMulai->timestamp)
            ->values();

        return $data;
    }

    // ambilDataPresensi(periodeAwal, periodeAkhir): satu baris = satu mahasiswa pada satu pertemuan
    private function ambilDataPresensi($awal, $akhir, $kuliah)
    {
        $slot = collect();

        foreach ($kuliah as $p) {
            [$pertemuan, $mahasiswa, $matriks] = $this->kehadiran->matriks($p->idPerkuliahan);

            foreach ($pertemuan as $s) {
                // sesi yang masih berlangsung atau di luar periode tidak dihitung
                if ($s->masihBerlaku() || $s->waktuMulai->lt($awal) || $s->waktuMulai->gt($akhir)) {
                    continue;
                }

                foreach ($mahasiswa as $m) {
                    $slot->push([
                        'perkuliahan' => $p,
                        'sesi'        => $s,
                        'mahasiswa'   => $m,
                        'status'      => $matriks[$m->idUser][$s->pertemuan]['status'],
                    ]);
                }
            }
        }

        return $slot;
    }

    // ambilDataIzin(periodeAwal, periodeAkhir)
    private function ambilDataIzin($awal, $akhir, $idPerkuliahan)
    {
        return PengajuanIzin::with(['mahasiswa', 'perkuliahan'])
            ->whereIn('idPerkuliahan', $idPerkuliahan)
            ->whereBetween('tanggalPengajuan', [$awal, $akhir])
            ->orderByDesc('idIzin')->get();
    }

    // ambilDataLaporan(periodeAwal, periodeAkhir)
    private function ambilDataLaporan($awal, $akhir)
    {
        return LaporanKendala::with('pelapor')
            ->whereBetween('tanggalLapor', [$awal, $akhir])
            ->orderByDesc('idLaporan')->get();
    }

    // olahRekapStatistik(dataRekap): hitung jumlah dan persentase
    private function olahRekapStatistik($slot, $slotLalu, $izin, $laporan, $tercatat): array
    {
        // ---- presensi ----
        $total  = $slot->count();
        $hitung = $slot->countBy('status');

        $perStatus = [];
        foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st) {
            $j = $hitung[$st] ?? 0;
            $perStatus[$st] = ['jumlah' => $j, 'persen' => $total ? round($j / $total * 100) : 0];
        }
        $rata = $perStatus['Hadir']['persen'];

        $totalLalu = $slotLalu->count();
        $rataLalu  = $totalLalu ? round($slotLalu->where('status', 'Hadir')->count() / $totalLalu * 100) : null;

        // persentase hadir per minggu dalam bulan (minggu 1 = tanggal 1-7, dst.)
        $mingguan = $slot
            ->groupBy(fn ($x) => (int) ceil($x['sesi']->waktuMulai->day / 7))
            ->sortKeys()
            ->map(fn ($g) => round($g->where('status', 'Hadir')->count() / $g->count() * 100));

        // ringkasan per perkuliahan (untuk tabel)
        $perMatkul = $slot->groupBy(fn ($x) => $x['perkuliahan']->idPerkuliahan)->map(function ($g) {
            $c = $g->countBy('status');
            $t = $g->count();

            return [
                'perkuliahan' => $g->first()['perkuliahan'],
                'pertemuan'   => $g->pluck('sesi')->unique('idSesi')->count(),
                'Hadir'       => $c['Hadir'] ?? 0,
                'Izin'        => $c['Izin'] ?? 0,
                'Sakit'       => $c['Sakit'] ?? 0,
                'Alpa'        => $c['Alpa'] ?? 0,
                'persen'      => $t ? round(($c['Hadir'] ?? 0) / $t * 100) : 0,
            ];
        })->values();

        // ---- izin ----
        $izinStatus = [];
        foreach (['Disetujui', 'Ditolak', 'Menunggu'] as $st) {
            $j = $izin->where('status', $st)->count();
            $izinStatus[$st] = ['jumlah' => $j, 'persen' => $izin->count() ? round($j / $izin->count() * 100) : 0];
        }

        // ---- laporan Crisis Center ----
        $laporanKategori = $laporan->groupBy('kategori')->map(fn ($g) => [
            'jumlah' => $g->count(),
            'persen' => round($g->count() / $laporan->count() * 100),
        ]);

        $laporanStatus = [];
        foreach (['Baru', 'Diproses', 'Selesai', 'Tidak Valid'] as $st) {
            $laporanStatus[$st] = $laporan->where('status', $st)->count();
        }

        return [
            'presensi' => [
                'total'     => $total,
                'tercatat'  => $tercatat,
                'perStatus' => $perStatus,
                'rata'      => $rata,
                'selisih'   => $rataLalu === null ? null : $rata - $rataLalu,
                'mingguan'  => $mingguan,
                'perMatkul' => $perMatkul,
            ],
            'izin' => [
                'total'     => $izin->count(),
                'menunggu'  => $izinStatus['Menunggu']['jumlah'],
                'perStatus' => $izinStatus,
                'daftar'    => $izin,
            ],
            'laporan' => [
                'total'       => $laporan->count(),
                'belum'       => $laporanStatus['Baru'],
                'perKategori' => $laporanKategori,
                'perStatus'   => $laporanStatus,
                'daftar'      => $laporan,
            ],
        ];
    }

    // ekspor rekap sesuai filter: CSV yang bisa dibuka di Excel
    public function ekspor(Request $request)
    {
        $this->cekAdmin();

        $f    = $this->bacaFilter($request);
        $data = $this->kumpulkanData($f);

        return response()->streamDownload(function () use ($f, $data) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fwrite($out, "sep=;\n");

            fputcsv($out, ['Rekap dan Statistik SIPPRES', 'Periode ' . $f['awal']->format('m/Y'), 'Jenis: ' . $f['jenis']], ';');

            if (in_array($f['jenis'], ['Semua', 'Presensi'])) {
                fputcsv($out, [], ';');
                fputcsv($out, ['PRESENSI'], ';');
                fputcsv($out, ['Mata Kuliah', 'Kelas', 'Dosen', 'Pertemuan', 'Hadir', 'Izin', 'Sakit', 'Alpa', 'Persentase Hadir (%)'], ';');
                foreach ($data['presensi']['perMatkul'] as $r) {
                    fputcsv($out, [
                        $r['perkuliahan']->namaMatkul, $r['perkuliahan']->kelas, $r['perkuliahan']->dosen->nama ?? '-',
                        $r['pertemuan'], $r['Hadir'], $r['Izin'], $r['Sakit'], $r['Alpa'], $r['persen'],
                    ], ';');
                }

                fputcsv($out, [], ';');
                fputcsv($out, ['DETAIL KEHADIRAN' . ($f['status'] ? ' (' . $f['status'] . ')' : '')], ';');
                fputcsv($out, ['Mahasiswa', 'Mata Kuliah', 'Kelas', 'Pertemuan', 'Tanggal', 'Status'], ';');
                foreach ($data['presensi']['detail'] as $r) {
                    fputcsv($out, [
                        $r['mahasiswa']->nama, $r['perkuliahan']->namaMatkul, $r['perkuliahan']->kelas,
                        $r['sesi']->pertemuan, $r['sesi']->waktuMulai->format('d/m/Y'), $r['status'],
                    ], ';');
                }
            }

            if (in_array($f['jenis'], ['Semua', 'Izin'])) {
                fputcsv($out, [], ';');
                fputcsv($out, ['PENGAJUAN IZIN'], ';');
                fputcsv($out, ['Mahasiswa', 'Mata Kuliah', 'Pertemuan', 'Tanggal', 'Status', 'Alasan'], ';');
                foreach ($data['izin']['daftar'] as $z) {
                    fputcsv($out, [
                        $z->mahasiswa->nama, $z->perkuliahan->namaMatkul, $z->pertemuan,
                        $z->tanggalPengajuan->format('d/m/Y'), $z->status, $z->alasan,
                    ], ';');
                }
            }

            if (in_array($f['jenis'], ['Semua', 'Crisis Center'])) {
                fputcsv($out, [], ';');
                fputcsv($out, ['LAPORAN CRISIS CENTER'], ';');
                fputcsv($out, ['Pelapor', 'Peran', 'Kategori', 'Tanggal', 'Status', 'Deskripsi'], ';');
                foreach ($data['laporan']['daftar'] as $l) {
                    fputcsv($out, [
                        $l->pelapor->nama, $l->pelapor->role, $l->kategori,
                        $l->tanggalLapor->format('d/m/Y'), $l->label_status, $l->deskripsi,
                    ], ';');
                }
            }

            fclose($out);
        }, 'statistik_' . $f['bulan'] . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}