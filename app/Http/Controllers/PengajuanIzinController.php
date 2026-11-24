<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengajuanIzinController extends Controller
{
    // hanya Dosen yang boleh memproses pengajuan
    private function cekDosen(): void
    {
        abort_unless(Auth::user()->role === 'Dosen', 403);
    }

    // getDaftarPengajuan(idDosen) -> UI-015
    public function getDaftarPengajuan(Request $request)
    {
        $this->cekDosen();

        $daftar = PengajuanIzin::with(['mahasiswa', 'perkuliahan'])
            ->whereHas('perkuliahan', fn ($q) => $q->where('idDosen', Auth::id()))
            ->orderByRaw("FIELD(status, 'Menunggu', 'Disetujui', 'Ditolak')")
            ->orderByDesc('idIzin')
            ->get();

        return view('dosen.izin', [
            'daftar'         => $daftar,
            'jumlahMenunggu' => $daftar->where('status', 'Menunggu')->count(),
            'detail'         => $this->getDetailPengajuan($request->id, $daftar),
        ]);
    }

    // getDetailPengajuan(idIzin): hanya pengajuan pada perkuliahan milik dosen ini
    private function getDetailPengajuan($id, $daftar)
    {
        return $id ? $daftar->firstWhere('idIzin', $id) : null;
    }

    // prosesKeputusan(idIzin, keputusan Setujui/Tolak)
    public function prosesKeputusan(Request $request, $id)
    {
        $this->cekDosen();

        $izin = PengajuanIzin::with('perkuliahan')->findOrFail($id);
        abort_unless($izin->perkuliahan->idDosen == Auth::id(), 403);

        if (!in_array($request->keputusan, ['Setujui', 'Tolak'])) {
            return back()->with('error', 'Keputusan tidak valid');
        }

        if ($izin->status !== 'Menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses');
        }

        $statusBaru = $request->keputusan === 'Setujui' ? 'Disetujui' : 'Ditolak';

        try {
            DB::transaction(function () use ($izin, $statusBaru) {
                $izin->update(['status' => $statusBaru]);

                // notifikasi ke mahasiswa (SR-21)
                Notifikasi::create([
                    'idPenerima' => $izin->idMahasiswa,
                    'jenis'      => 'Izin',
                    'isi'        => 'Pengajuan izin ' . $izin->perkuliahan->namaMatkul
                                  . ' pertemuan ' . $izin->pertemuan . ' ' . strtolower($statusBaru),
                    'waktu'      => now(),
                    'statusBaca' => false,
                ]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Keputusan gagal disimpan');
        }

        return redirect()->route('izin.dosen', ['id' => $id])
            ->with('success', 'Pengajuan izin berhasil ' . strtolower($statusBaru));
    }

    // menampilkan berkas bukti (hanya pemilik, dosen pengampu, atau admin)
    public function bukti($id)
    {
        $izin = PengajuanIzin::with('perkuliahan')->findOrFail($id);
        $user = Auth::user();

        $boleh = $user->role === 'Admin'
            || ($user->role === 'Mahasiswa' && $izin->idMahasiswa == $user->idUser)
            || ($user->role === 'Dosen' && $izin->perkuliahan->idDosen == $user->idUser);

        abort_unless($boleh, 403);
        abort_unless($izin->fileBukti && Storage::disk('public')->exists($izin->fileBukti), 404);

        return response()->file(Storage::disk('public')->path($izin->fileBukti));
    }
}