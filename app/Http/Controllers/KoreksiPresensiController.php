<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use App\Models\Presensi;
use App\Services\KehadiranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KoreksiPresensiController extends Controller
{
    public function __construct(private KehadiranService $kehadiran)
    {
    }

    private function cekDosen(): void
    {
        abort_unless(Auth::user()->role === 'Dosen', 403);
    }

    // getDataPresensi(idDosen) -> UI-016
    public function getDataPresensi(Request $request)
    {
        $this->cekDosen();

        $daftar      = Perkuliahan::where('idDosen', Auth::id())->orderBy('idPerkuliahan')->get();
        $perkuliahan = $daftar->firstWhere('idPerkuliahan', $request->perkuliahan) ?? $daftar->first();

        $pertemuan = collect();
        $mahasiswa = collect();
        $matriks   = [];
        $nomor     = null;
        $detail    = null;

        if ($perkuliahan) {
            [$pertemuan, $mahasiswa, $matriks] = $this->kehadiran->matriks($perkuliahan->idPerkuliahan);

            // pertemuan terpilih (bawaan: yang terbaru)
            $nomor = $pertemuan->contains('pertemuan', (int) $request->pertemuan)
                ? (int) $request->pertemuan
                : $pertemuan->max('pertemuan');

            $detail = $this->getDetailPresensi($request->mahasiswa, $mahasiswa, $matriks, $nomor);
        }

        return view('dosen.data_presensi', compact(
            'daftar', 'perkuliahan', 'pertemuan', 'nomor', 'mahasiswa', 'matriks', 'detail'
        ));
    }

    // getDetailPresensi(idPresensi): data mahasiswa yang akan dikoreksi
    private function getDetailPresensi($idMahasiswa, $mahasiswa, $matriks, $nomor)
    {
        $m = $idMahasiswa ? $mahasiswa->firstWhere('idUser', $idMahasiswa) : null;

        return ($m && $nomor)
            ? ['mahasiswa' => $m, 'sel' => $matriks[$m->idUser][$nomor]]
            : null;
    }

    // koreksiPresensi(idPresensi, status, keterangan, aksi Simpan/Batal)
    public function koreksiPresensi(Request $request)
    {
        $this->cekDosen();

        $kembali = ['perkuliahan' => $request->idPerkuliahan, 'pertemuan' => $request->pertemuan];

        if ($request->input('aksi') === 'Batal') {
            return redirect()->route('koreksi.dosen', $kembali);
        }

        // perkuliahan harus milik dosen ini
        $perkuliahan = Perkuliahan::where('idPerkuliahan', $request->idPerkuliahan)
                                  ->where('idDosen', Auth::id())->firstOrFail();

        // validasiData(status, keterangan)
        if (!$this->validasiData($request, $perkuliahan)) {
            return back()->withInput()->with('error', 'Data koreksi tidak valid');
        }

        $sesi = $this->kehadiran->daftarPertemuan($perkuliahan->idPerkuliahan)
                     ->firstWhere('pertemuan', (int) $request->pertemuan);

        // updatePresensi(...)
        try {
            $presensi = Presensi::firstOrNew([
                'idMahasiswa' => $request->idMahasiswa,
                'idSesi'      => $sesi->idSesi,
            ]);

            if (!$presensi->exists) {
                $presensi->waktu = now();      // mahasiswa belum punya catatan, dibuat baru
            }

            $presensi->status     = $request->status;
            $presensi->keterangan = $request->keterangan;
            $presensi->save();
        } catch (\Throwable $e) {
            return back()->with('error', 'Koreksi presensi gagal disimpan');
        }

        return redirect()
            ->route('koreksi.dosen', $kembali + ['mahasiswa' => $request->idMahasiswa])
            ->with('success', 'Status kehadiran berhasil diperbarui');
    }

    // validasiData(status, keterangan): boolean
    private function validasiData(Request $request, Perkuliahan $perkuliahan): bool
    {
        $validator = validator($request->all(), [
            'idMahasiswa' => ['required', Rule::exists('peserta_kuliah', 'idMahasiswa')
                                ->where('idPerkuliahan', $perkuliahan->idPerkuliahan)],
            'pertemuan'  => 'required|integer|min:1',
            'status'     => 'required|in:Hadir,Izin,Sakit,Alpa',
            'keterangan' => 'required|string|max:255',
        ]);

        $sesiAda = $this->kehadiran->daftarPertemuan($perkuliahan->idPerkuliahan)
                        ->contains('pertemuan', (int) $request->pertemuan);

        return !$validator->fails() && $sesiAda;
    }
}