<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    // getNotifikasi(idMahasiswa) -> UI-010 (untuk mahasiswa dan dosen)
    public function getNotifikasi(Request $request)
    {
        abort_unless(in_array(Auth::user()->role, ['Mahasiswa', 'Dosen']), 403);

        $daftar = Notifikasi::where('idPenerima', Auth::id())
                            ->orderByDesc('idNotifikasi')->get();

        // dibuka dulu agar notifikasi yang dipilih langsung ditandai sudah dibaca
        $detail = $this->getDetailNotifikasi($request->id, $daftar);

        return view('notifikasi.index', [
            'daftar'      => $daftar,
            'detail'      => $detail,
            'belumDibaca' => $daftar->where('statusBaca', false)->count(),
        ]);
    }

    // Tandai seluruh notifikasi milik pengguna yang login menjadi sudah dibaca
    public function bacaSemua()
    {
        abort_unless(in_array(Auth::user()->role, ['Mahasiswa', 'Dosen']), 403);

        Notifikasi::where('idPenerima', Auth::id())
            ->where('statusBaca', false)
            ->update(['statusBaca' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca');
    }

    // getDetailNotifikasi(idNotifikasi): hanya milik pengguna yang login
    private function getDetailNotifikasi($id, $daftar)
    {
        $notifikasi = $id ? $daftar->firstWhere('idNotifikasi', $id) : null;

        if ($notifikasi && !$notifikasi->statusBaca) {
            $notifikasi->update(['statusBaca' => true]);   // ditandai sudah dibaca
        }

        return $notifikasi;
    }
}