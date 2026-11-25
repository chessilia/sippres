<?php

namespace App\Http\Controllers;

use App\Models\LaporanKendala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
            'detail'   => $request->id ? $daftar->firstWhere('idLaporan',$request->id) : null,
        ]);
    }

    // kirimLaporan(idPengguna, kategori, deskripsi, foto, aksi Submit/Batal)
    public function kirimLaporan(Request $request)
    {
        $this->cekPengguna();

        if ($request->input('aksi') === 'Batal') {
            return redirect()->route('crisis.pengguna')->with('success', 'Pengiriman laporan dibatalkan');
        }

        // Cek jika upload melebihi batas upload_max_filesize / post_max_size di php.ini
        if (
            (isset($_FILES['foto']) &&$_FILES['foto']['error'] === UPLOAD_ERR_INI_SIZE) ||
            (empty($request->all()) && (int)$request->server('CONTENT_LENGTH') > 0)
        ) {
            return back()->withInput()->with('error', 'Ukuran foto terlalu besar. Maksimal ukuran foto adalah 5 MB.');
        }

        // validasiLaporan(kategori, deskripsi, foto)
        $pesanError = $this->validasiLaporan($request);
        if ($pesanError !== null) {
            return back()->withInput()->with('error', $pesanError);
        }

        // simpanLaporan(idPengguna, kategori, deskripsi, foto, "Baru")
        try {
            $pathFoto = null;
            if ($request->hasFile('foto')) {
                $pathFoto =$request->file('foto')->store('laporan-kendala', 'public');
            }

            $data = [
                'idPelapor'    => Auth::id(),
                'kategori'     => $request->kategori,
                'deskripsi'    => $request->deskripsi,
                'status'       => 'Baru',
                'tanggalLapor' => now(),
            ];

            if (Schema::hasColumn('laporan_kendala', 'foto')) {
                $data['foto'] =$pathFoto;
            }

            LaporanKendala::create($data);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Laporan gagal dikirim: ' . $e->getMessage());
        }

        return redirect()->route('crisis.pengguna')->with('success', 'Laporan berhasil dikirim');
    }

    // validasiLaporan(kategori, deskripsi, foto): ?string (null jika valid)
    private function validasiLaporan(Request $request): ?string
    {
        $validator = validator($request->all(), [
            'kategori'  => 'required|in:' . implode(',', self::KATEGORI),
            'deskripsi' => 'required|string|max:1000',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'kategori.required'  => 'Kategori kendala wajib dipilih.',
            'deskripsi.required' => 'Deskripsi kendala wajib diisi.',
            'foto.image'         => 'Lampiran harus berupa file gambar.',
            'foto.mimes'         => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'foto.max'           => 'Ukuran foto terlalu besar. Maksimal ukuran foto adalah 5 MB.',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        return null;
    }
}