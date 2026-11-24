<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use App\Models\SesiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SesiPresensiController extends Controller
{
    // hanya Dosen yang boleh mengakses
    private function cekDosen(): void
    {
        abort_unless(Auth::user()->role === 'Dosen', 403);
    }

    // kirim notifikasi ke seluruh peserta kuliah saat sesi ditutup (manual maupun otomatis)
    private function kirimNotifTutupSesi(SesiPresensi $sesi, bool $otomatis = false): void
    {
        $sesi->loadMissing('perkuliahan');

        $penerima = DB::table('peserta_kuliah')
            ->where('idPerkuliahan', $sesi->idPerkuliahan)
            ->pluck('idMahasiswa');

        if ($penerima->isEmpty()) {
            return;
        }

        $sudahPresensi = DB::table('presensi')
            ->where('idSesi', $sesi->idSesi)
            ->pluck('status', 'idMahasiswa');

        $sekarang   = now();
        $namaMatkul = $sesi->perkuliahan->namaMatkul;
        $kelas      = $sesi->perkuliahan->kelas;
        $keterangan = $otomatis ? 'telah berakhir otomatis' : 'telah ditutup oleh dosen';

        $notifikasi = $penerima->map(function ($idMhs) use ($sudahPresensi, $namaMatkul, $kelas, $keterangan, $sekarang) {
            $statusAkhir = $sudahPresensi[$idMhs] ?? 'Alpa';

            return [
                'idPenerima' => $idMhs,
                'jenis'      => 'Presensi',
                'isi'        => "Sesi presensi {$namaMatkul} kelas {$kelas} {$keterangan}. Status akhir kamu: {$statusAkhir}.",
                'waktu'      => $sekarang,
                'statusBaca' => false,
            ];
        })->all();

        DB::table('notifikasi')->insert($notifikasi);
    }

    // sesi yang sudah melewati durasinya otomatis menjadi "Selesai"
    private function tutupOtomatis(): void
    {
        SesiPresensi::with('perkuliahan')->where('status', 'Aktif')->get()->each(function ($sesi) {
            $berakhir = $sesi->waktuMulai->copy()->addMinutes($sesi->durasi);
            if (now()->greaterThanOrEqualTo($berakhir)) {
                $sesi->update(['status' => 'Selesai']);
                $this->kirimNotifTutupSesi($sesi, true);
            }
        });
    }

    // getDaftarPerkuliahan(idDosen) -> UI-014
    public function getDaftarPerkuliahan()
    {
        $this->cekDosen();
        $this->tutupOtomatis();

        $idDosen = Auth::id();

        $riwayatSesi = SesiPresensi::with('perkuliahan')
            ->where('status', 'Selesai')
            ->whereHas('perkuliahan', fn ($q) => $q->where('idDosen', $idDosen))
            ->orderByDesc('waktuMulai')
            ->limit(15)
            ->get()
            ->each(function ($sesi) {
                $sesi->hadir = DB::table('presensi')->where('idSesi', $sesi->idSesi)->count();
                $sesi->total = DB::table('peserta_kuliah')
                                 ->where('idPerkuliahan', $sesi->idPerkuliahan)->count();
            });

        return view('dosen.presensi', [
            'daftar'      => Perkuliahan::where('idDosen', $idDosen)->orderBy('idPerkuliahan')->get(),
            'sesiAktif'   => $this->getSesiAktif($idDosen),
            'riwayatSesi' => $riwayatSesi,
        ]);
    }

    // getSesiAktif(idDosen) / ambilSesiAktif -> PSD-008
    public function getSesiAktif($idDosen)
    {
        return SesiPresensi::with('perkuliahan')
            ->where('status', 'Aktif')
            ->whereHas('perkuliahan', fn ($q) => $q->where('idDosen', $idDosen))
            ->get()
            ->each(function ($sesi) {
                // jumlah mahasiswa yang sudah presensi dibanding total peserta
                $sesi->hadir = DB::table('presensi')->where('idSesi', $sesi->idSesi)->count();
                $sesi->total = DB::table('peserta_kuliah')
                                 ->where('idPerkuliahan', $sesi->idPerkuliahan)->count();
            });
    }

    // buatSesi(idPerkuliahan, durasi) -> PSD-007
    public function buatSesi(Request $request)
    {
        $this->cekDosen();
        $this->tutupOtomatis();

        $request->validate([
            'idPerkuliahan' => ['required',
                Rule::exists('perkuliahan', 'idPerkuliahan')->where('idDosen', Auth::id())],
            'durasi'    => 'required|in:10,15,30,60',
            'radius'    => 'required|integer|min:0|max:1000',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // radius 0 = tanpa validasi lokasi; radius > 0 butuh lokasi dosen
        if ($request->radius > 0 && !$request->filled(['latitude', 'longitude'])) {
            return back()->withInput()->with('error',
                'Lokasi belum terdeteksi. Izinkan akses lokasi di browser, atau isi radius 0.');
        }

        // cekSesiAktif(idPerkuliahan)
        $sudahAktif = SesiPresensi::where('idPerkuliahan', $request->idPerkuliahan)
                                  ->where('status', 'Aktif')->exists();
        if ($sudahAktif) {
            return back()->withInput()->with('error', 'Sesi presensi sudah aktif');
        }

        // simpanSesi(idPerkuliahan, durasi)
        $sesi = SesiPresensi::create([
            'idPerkuliahan' => $request->idPerkuliahan,
            'kodeQR'        => (string) Str::uuid(),      // sementara, diganti di bawah
            'waktuMulai'    => now(),
            'durasi'        => $request->durasi,
            'status'        => 'Aktif',
            'latitude'      => $request->latitude ?? 0,
            'longitude'     => $request->longitude ?? 0,
            'radius'        => $request->radius,
        ]);

        // generateQRCode(idSesi) lalu simpanKodeQR(idSesi, kodeQR)
        $sesi->update(['kodeQR' => $this->generateQRCode($sesi->idSesi)]);

        // notifikasi ke mahasiswa peserta kuliah: sesi presensi dibuka (SR-21)
        $kuliah   = Perkuliahan::find($request->idPerkuliahan);
        $penerima = DB::table('peserta_kuliah')
                      ->where('idPerkuliahan', $request->idPerkuliahan)->pluck('idMahasiswa');

        if ($penerima->isNotEmpty()) {
            DB::table('notifikasi')->insert($penerima->map(fn ($idMhs) => [
                'idPenerima' => $idMhs,
                'jenis'      => 'Presensi',
                'isi'        => 'Sesi presensi ' . $kuliah->namaMatkul . ' kelas ' . $kuliah->kelas
                              . ' telah dibuka selama ' . $request->durasi . ' menit',
                'waktu'      => now(),
                'statusBaca' => false,
            ])->all());
        }

        return redirect()->route('presensi.dosen')->with('success', 'Sesi presensi berhasil dibuat');
    }

    // generateQRCode(idSesi): String
    private function generateQRCode($idSesi): string
    {
        return 'QR-' . $idSesi . '-' . now()->format('Ymd') . '-' . Str::random(8);
    }

    // tutupSesi(idSesi) -> PSD-008
    public function tutupSesi($id)
    {
        $this->cekDosen();

        $sesi = SesiPresensi::with('perkuliahan')->findOrFail($id);
        abort_unless($sesi->perkuliahan->idDosen == Auth::id(), 403);

        if ($sesi->status === 'Selesai') {
            return back()->with('error', 'Sesi presensi sudah ditutup / tidak ada sesi aktif');
        }

        try {
            $sesi->update(['status' => 'Selesai']);     // updateStatusSesi(idSesi, "Selesai")
            $this->kirimNotifTutupSesi($sesi, false);    // kirim notifikasi ke peserta kuliah
        } catch (\Throwable $e) {
            return back()->with('error', 'Sesi gagal ditutup');
        }

        return redirect()->route('presensi.dosen')->with('success', 'Sesi berhasil ditutup');
    }

    // batalSesi(idSesi): batalkan & hapus sesi presensi (baik aktif maupun selesai)
    public function batalSesi($id)
    {
        $this->cekDosen();

        $sesi = SesiPresensi::with('perkuliahan')->findOrFail($id);
        abort_unless($sesi->perkuliahan->idDosen == Auth::id(), 403);

        $namaMatkul    = $sesi->perkuliahan->namaMatkul;
        $kelas         = $sesi->perkuliahan->kelas;
        $idPerkuliahan = $sesi->idPerkuliahan;
        $wasAktif      = $sesi->status === 'Aktif';

        try {
            DB::transaction(function () use ($sesi) {
                DB::table('presensi')->where('idSesi', $sesi->idSesi)->delete();
                $sesi->delete();
            });

            $penerima = DB::table('peserta_kuliah')
                ->where('idPerkuliahan', $idPerkuliahan)
                ->pluck('idMahasiswa');

            if ($penerima->isNotEmpty()) {
                $sekarang = now();
                $pesan    = $wasAktif
                    ? "Sesi presensi {$namaMatkul} kelas {$kelas} telah dibatalkan oleh dosen."
                    : "Riwayat sesi presensi {$namaMatkul} kelas {$kelas} telah dihapus oleh dosen.";

                DB::table('notifikasi')->insert($penerima->map(fn ($idMhs) => [
                    'idPenerima' => $idMhs,
                    'jenis'      => 'Presensi',
                    'isi'        => $pesan,
                    'waktu'      => $sekarang,
                    'statusBaca' => false,
                ])->all());
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Sesi presensi gagal dihapus');
        }

        return redirect()->route('presensi.dosen')->with('success', 'Sesi presensi berhasil dihapus');
    }

    // daftar mahasiswa yang sudah presensi (dipanggil berkala oleh halaman dosen)
    public function daftarHadir($id)
    {
        $this->cekDosen();

        $sesi = SesiPresensi::with('perkuliahan')->findOrFail($id);
        abort_unless($sesi->perkuliahan->idDosen == Auth::id(), 403);

        $daftar = DB::table('presensi')
            ->join('user', 'user.idUser', '=', 'presensi.idMahasiswa')
            ->where('presensi.idSesi', $id)
            ->where('presensi.status', 'Hadir')
            ->orderBy('presensi.waktu')
            ->get(['user.nama', 'presensi.waktu']);

        return response()->json([
            'hadir'  => $daftar->count(),
            'total'  => DB::table('peserta_kuliah')->where('idPerkuliahan', $sesi->idPerkuliahan)->count(),
            'daftar' => $daftar->map(fn ($r) => [
                'nama'  => $r->nama,
                'waktu' => \Carbon\Carbon::parse($r->waktu)->format('H:i:s'),
            ]),
        ]);
    }
}