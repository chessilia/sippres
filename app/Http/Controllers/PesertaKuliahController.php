<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesertaKuliahController extends Controller
{
    private function cekAdmin(): void
    {
        abort_unless(Auth::user()->role === 'Admin', 403);
    }

    // mendaftarkan satu atau beberapa mahasiswa ke sebuah perkuliahan
    public function tambahPeserta(Request $request, $id)
    {
        $this->cekAdmin();

        $perkuliahan = Perkuliahan::findOrFail($id);

        // hanya akun bertipe Mahasiswa yang diterima
        $valid = User::where('role', 'Mahasiswa')
                     ->whereIn('idUser', (array) $request->input('mahasiswa', []))
                     ->pluck('idUser');

        if ($valid->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu mahasiswa');
        }

        DB::table('peserta_kuliah')->insertOrIgnore(
            $valid->map(fn ($m) => [
                'idMahasiswa'   => $m,
                'idPerkuliahan' => $perkuliahan->idPerkuliahan,
            ])->all()
        );

        return redirect()->route('perkuliahan.daftar', ['peserta' => $id])
            ->with('success', $valid->count() . ' mahasiswa ditambahkan');
    }

    // mengeluarkan mahasiswa dari perkuliahan (catatan presensinya tetap tersimpan)
    public function hapusPeserta($id, $idMahasiswa)
    {
        $this->cekAdmin();

        DB::table('peserta_kuliah')
            ->where('idPerkuliahan', $id)
            ->where('idMahasiswa', $idMahasiswa)
            ->delete();

        return redirect()->route('perkuliahan.daftar', ['peserta' => $id])
            ->with('success', 'Mahasiswa dikeluarkan dari perkuliahan');
    }
}