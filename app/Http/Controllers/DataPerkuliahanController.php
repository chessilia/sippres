<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DataPerkuliahanController extends Controller
{
    // hanya Admin yang boleh mengakses
    private function cekAdmin(): void
    {
        abort_unless(Auth::user()->role === 'Admin', 403);
    }

        // getDaftarPerkuliahan() -> UI-019 (daftar, cari, filter hari, panel edit/tambah/peserta)
    public function getDaftarPerkuliahan(Request $request)
    {
        $this->cekAdmin();

        $query = Perkuliahan::with('dosen');

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('namaMatkul', 'like', "%$cari%")
                  ->orWhere('kodeMatkul', 'like', "%$cari%")
                  ->orWhereHas('dosen', fn ($d) => $d->where('nama', 'like', "%$cari%"));
            });
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // panel kelola peserta kuliah
        $kelola  = $request->filled('peserta') ? Perkuliahan::find($request->peserta) : null;
        $peserta = collect();
        $calon   = collect();

        if ($kelola) {
            $ikut    = DB::table('peserta_kuliah')->where('idPerkuliahan', $kelola->idPerkuliahan)->pluck('idMahasiswa');
            $peserta = User::whereIn('idUser', $ikut)->orderBy('nama')->get();
            $calon   = User::where('role', 'Mahasiswa')->whereNotIn('idUser', $ikut)->orderBy('nama')->get();
        }

        return view('admin.perkuliahan', [
            'daftar'      => $query->orderBy('idPerkuliahan')->get(),
            'detail'      => $this->getDetailPerkuliahan($request->edit),
            'daftarDosen' => User::where('role', 'Dosen')->get(),
            'tambah'      => $request->has('tambah'),
            'kelola'      => $kelola,
            'peserta'     => $peserta,
            'calon'       => $calon,
        ]);
    }

    // getDetailPerkuliahan(idPerkuliahan)
    private function getDetailPerkuliahan($id)
    {
        return $id ? Perkuliahan::find($id) : null;
    }

    // prosesPerubahan(idPerkuliahan, opsi Edit/Hapus, dataBaru) -> PSD-016
    public function prosesPerubahan(Request $request, $id)
    {
        $this->cekAdmin();

        $perkuliahan = Perkuliahan::findOrFail($id);

        if ($request->opsi === 'Edit') {
            // validasiPerubahan(dataBaru)
            if (!$this->validasiPerubahan($request)) {
                return back()->withInput()->with('error', 'Data perkuliahan tidak valid');
            }

            try {
                $perkuliahan->update($request->only([
                    'kodeMatkul', 'namaMatkul', 'idDosen',
                    'hari', 'jamMulai', 'jamSelesai', 'ruang', 'kelas',
                ]));
            } catch (\Throwable $e) {
                return back()->with('error', 'Data perkuliahan gagal diperbarui');
            }

            return redirect()->route('perkuliahan.daftar')
                ->with('success', 'Data perkuliahan berhasil diperbarui');
        }

        // opsi Hapus (konfirmasi sudah dilakukan lewat dialog di UI)
        try {
            $perkuliahan->delete();
        } catch (\Throwable $e) {
            return back()->with('error', 'Data perkuliahan gagal dihapus');
        }

        return redirect()->route('perkuliahan.daftar')
            ->with('success', 'Data perkuliahan berhasil dihapus');
    }

    // validasiPerubahan(dataBaru): boolean
    private function validasiPerubahan(Request $request): bool
    {
        $validator = validator($request->all(), [
            'kodeMatkul' => 'required|string|max:10',
            'namaMatkul' => 'required|string|max:100',
            'kelas'      => 'required|string|max:5',
            'idDosen'    => ['required', Rule::exists('user', 'idUser')->where('role', 'Dosen')],
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'ruang'      => 'required|string|max:20',
            'jamMulai'   => 'required|date_format:H:i',
            'jamSelesai' => 'required|date_format:H:i|after:jamMulai',
        ]);

        return !$validator->fails();
    }

        // tambahPerkuliahan(dataBaru) -> cabang "Tambah" pada proses bisnis 4
    public function tambahPerkuliahan(Request $request)
    {
        $this->cekAdmin();

        if (!$this->validasiPerubahan($request)) {
            return back()->withInput()->with('error', 'Data perkuliahan tidak valid');
        }

        $sudahAda = Perkuliahan::where('kodeMatkul', $request->kodeMatkul)
                               ->where('kelas', $request->kelas)->exists();
        if ($sudahAda) {
            return back()->withInput()->with('error', 'Perkuliahan dengan kode dan kelas tersebut sudah ada');
        }

        try {
            Perkuliahan::create($request->only([
                'kodeMatkul', 'namaMatkul', 'idDosen',
                'hari', 'jamMulai', 'jamSelesai', 'ruang', 'kelas',
            ]));
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Data perkuliahan gagal disimpan');
        }

        return redirect()->route('perkuliahan.daftar')->with('success', 'Data perkuliahan berhasil ditambahkan');
    }
}