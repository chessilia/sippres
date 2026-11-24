<?php

namespace App\Http\Controllers;

use App\Models\LaporanKendala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrisisCenterController extends Controller
{
    private const KATEGORI = [
        'Kendala Presensi', 'Kendala Perizinan', 'Kendala Akun', 'Kendala Aplikasi', 'Lainnya',
    ];

    private function cekPengguna(): void
    {
        abort_unless(in_array(Auth::user()->role, ['Mahasiswa', 'Dosen']), 403);
    }

    // halaman Lapor Kendala + Laporan Saya -> UI-011
    public function index(Request $request)
    {
        $this->cekPengguna();

        $daftar = LaporanKendala::where('idPelapor', Auth::id())->orderByDesc('idLaporan')->get();

        return view('crisis.lapor', [
            'kategori' => self::KATEGORI,
            'daftar'   => $daftar,
            'detail'   => $request->id ? $daftar->firstWhere('idLaporan', $request->id) : null,
        ]);
    }

    // kirimLaporan(idPengguna, kategori, deskripsi, aksi Submit/Batal)
    public function kirimLaporan(Request $request)
    {
        $this->cekPengguna();

        if ($request->input('aksi') === 'Batal') {
            return redirect()->route('crisis.pengguna')->with('success', 'Pengiriman laporan dibatalkan');
        }

        // validasiLaporan(kategori, deskripsi)
        if (!$this->validasiLaporan($request)) {
            return back()->withInput()->with('error', 'Data harus dilengkapi');
        }

        // simpanLaporan(idPengguna, kategori, deskripsi, "Baru")
        try {
            LaporanKendala::create([
                'idPelapor'    => Auth::id(),
                'kategori'     => $request->kategori,
                'deskripsi'    => $request->deskripsi,
                'status'       => 'Baru',
                'tanggalLapor' => now(),
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Laporan gagal dikirim');
        }

        return redirect()->route('crisis.pengguna')->with('success', 'Laporan berhasil dikirim');
    }

    // validasiLaporan(kategori, deskripsi): boolean
    private function validasiLaporan(Request $request): bool
    {
        $validator = validator($request->all(), [
            'kategori'  => 'required|in:' . implode(',', self::KATEGORI),
            'deskripsi' => 'required|string|min:10|max:1000',
        ]);

        return !$validator->fails();
    }
}