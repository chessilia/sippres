<?php

namespace App\Http\Controllers;

use App\Models\LaporanKendala;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenangananLaporanController extends Controller
{
    private function cekAdmin(): void
    {
        abort_unless(Auth::user()->role === 'Admin', 403);
    }

    // getDaftarLaporan() -> UI-022
    public function getDaftarLaporan(Request $request)
    {
        $this->cekAdmin();

        $semua  = LaporanKendala::with('pelapor')->orderByDesc('idLaporan')->get();
        $filter = in_array($request->status, ['Baru', 'Diproses', 'Selesai', 'Tidak Valid'])
            ? $request->status : null;

        return view('admin.crisis', [
            'daftar'  => $filter ? $semua->where('status', $filter) : $semua,
            'filter'  => $filter,
            'jumlah'  => $semua->countBy('status'),
            'total'   => $semua->count(),
            'detail'  => $this->getDetailLaporan($request->id, $semua),
        ]);
    }

    // getDetailLaporan(idLaporan)
    private function getDetailLaporan($id, $semua)
    {
        return $id ? $semua->firstWhere('idLaporan', $id) : null;
    }

    // simpanTindakLanjut(idLaporan, status, tindakLanjut) + kirim notifikasi ke pelapor
    public function simpanTindakLanjut(Request $request, $id)
    {
        $this->cekAdmin();

        $request->validate([
            'status'       => 'required|in:Baru,Diproses,Selesai',
            'tindakLanjut' => 'required|string|max:1000',
        ]);

        $laporan = LaporanKendala::findOrFail($id);

        if ($laporan->status === 'Tidak Valid') {
            return back()->with('error', 'Laporan yang ditandai tidak valid tidak dapat ditindaklanjuti');
        }

        try {
            DB::transaction(function () use ($request, $laporan) {
                $label = $request->status === 'Diproses' ? 'Ditangani' : $request->status;

                $laporan->update([
                    'status'       => $request->status,
                    'tindakLanjut' => $this->tambahRiwayat($laporan, $label . ': ' . $request->tindakLanjut),
                ]);

                // kirimNotifikasi(idPengguna, idLaporan, tindakLanjut)
                Notifikasi::create([
                    'idPenerima' => $laporan->idPelapor,
                    'jenis'      => 'Crisis Center',
                    'isi'        => 'Laporan kendala "' . $laporan->kategori . '" ' . strtolower($label)
                                  . '. Tindak lanjut: ' . Str::limit($request->tindakLanjut, 150),
                    'waktu'      => now(),
                    'statusBaca' => false,
                ]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Tindak lanjut gagal disimpan');
        }

        return redirect()->route('crisis.admin', ['id' => $id])
            ->with('success', 'Tindak lanjut berhasil disimpan');
    }

    // prosesLaporanTidakValid(idLaporan)
    public function prosesLaporanTidakValid($id)
    {
        $this->cekAdmin();

        $laporan = LaporanKendala::findOrFail($id);

        $laporan->update([
            'status'       => 'Tidak Valid',
            'tindakLanjut' => $this->tambahRiwayat($laporan, 'Ditandai tidak valid oleh admin'),
        ]);

        return redirect()->route('crisis.admin', ['id' => $id])
            ->with('success', 'Laporan ditandai tidak valid');
    }

    // riwayat penanganan: tiap tindak lanjut jadi satu baris baru berstempel waktu
    private function tambahRiwayat(LaporanKendala $laporan, string $isi): string
    {
        $baris = '[' . now()->format('d/m/Y H:i') . '] ' . $isi;

        return $laporan->tindakLanjut ? $laporan->tindakLanjut . "\n" . $baris : $baris;
    }
}