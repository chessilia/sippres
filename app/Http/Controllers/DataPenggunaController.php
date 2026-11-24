<?php

namespace App\Http\Controllers;

use App\Models\LaporanKendala;
use App\Models\PengajuanIzin;
use App\Models\Perkuliahan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DataPenggunaController extends Controller
{
    private function cekAdmin(): void
    {
        abort_unless(Auth::user()->role === 'Admin', 403);
    }

    // getDaftarPengguna() -> UI-020
    public function getDaftarPengguna(Request $request)
    {
        $this->cekAdmin();

        $semua = User::orderBy('nama')->get();
        $peran = in_array($request->peran, ['Mahasiswa', 'Dosen', 'Admin']) ? $request->peran : null;
        $cari  = trim((string) $request->cari);

        $daftar = $semua
            ->when($peran, fn ($c) => $c->where('role', $peran))
            ->when($cari !== '', fn ($c) => $c->filter(fn ($u) => Str::contains(
                Str::lower($u->nama . ' ' . $u->username . ' ' . $u->email), Str::lower($cari)
            )));

        $hapus = $this->getDetailPengguna($request->hapus, $semua);

        return view('admin.pengguna', [
            'daftar'  => $daftar->values(),
            'peran'   => $peran,
            'cari'    => $cari,
            'jumlah'  => $semua->countBy('role'),
            'total'   => $semua->count(),
            'detail'  => $this->getDetailPengguna($request->edit, $semua),
            'hapus'   => $hapus,
            'terkait' => $hapus ? $this->cekDataTerkait($hapus) : [],
        ]);
    }

    // getDetailPengguna(idUser)
    private function getDetailPengguna($id, $semua)
    {
        return $id ? $semua->firstWhere('idUser', $id) : null;
    }

    // perbaruiDataPengguna(idUser, nama, username, email, role)
    public function perbaruiDataPengguna(Request $request, $id)
    {
        $this->cekAdmin();

        $user = User::findOrFail($id);

        // validasiDataPengguna(nama, email, role)
        if (!$this->validasiDataPengguna($request, $user)) {
            return back()->withInput()->with('error', 'Data pengguna tidak valid');
        }

        // peran Admin tidak diubah lewat form ini
        $role = $user->role === 'Admin' ? 'Admin' : $request->role;

        // dosen yang masih mengampu perkuliahan tidak boleh berganti peran
        if ($user->role === 'Dosen' && $role !== 'Dosen'
            && Perkuliahan::where('idDosen', $user->idUser)->exists()) {
            return back()->withInput()->with('error', 'Dosen masih mengampu perkuliahan, peran tidak dapat diubah');
        }

        // updateUser(idUser, nama, email, role)
        try {
            $user->update([
                'nama'     => $request->nama,
                'username' => $request->username,
                'email'    => $request->email,
                'role'     => $role,
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Data pengguna gagal diperbarui');
        }

        return redirect()->route('pengguna.daftar')->with('success', 'Data pengguna berhasil diperbarui');
    }

    // validasiDataPengguna(nama, email, role): boolean
    private function validasiDataPengguna(Request $request, User $user): bool
    {
        $validator = validator($request->all(), [
            'nama'     => 'required|string|max:100',
            'username' => ['required', 'string', 'max:30',
                           Rule::unique('user', 'username')->ignore($user->idUser, 'idUser')],
            'email'    => ['required', 'email', 'max:100',
                           Rule::unique('user', 'email')->ignore($user->idUser, 'idUser')],
            'role'     => [$user->role === 'Admin' ? 'nullable' : 'required', 'in:Mahasiswa,Dosen'],
        ]);

        return !$validator->fails();
    }

    // hapusDataPengguna(idUser, konfirmasi, lanjut)
    public function hapusDataPengguna(Request $request, $id)
    {
        $this->cekAdmin();

        $user = User::findOrFail($id);

        if ($user->idUser === Auth::id()) {
            return back()->with('error', 'Akun yang sedang kamu pakai tidak dapat dihapus');
        }

        if (Perkuliahan::where('idDosen', $user->idUser)->exists()) {
            return redirect()->route('pengguna.daftar')->with('error',
                'Dosen masih mengampu perkuliahan. Pindahkan atau hapus perkuliahannya terlebih dahulu');
        }

        // cekDataTerkait(idUser): jika ada, minta konfirmasi ulang
        if ($this->cekDataTerkait($user) && $request->lanjut !== '1') {
            return redirect()->route('pengguna.daftar', ['hapus' => $user->idUser]);
        }

        // hapusUser(idUser) beserta data terkait
        try {
            DB::transaction(function () use ($user) {
                $id = $user->idUser;

                foreach (PengajuanIzin::where('idMahasiswa', $id)->get() as $izin) {
                    if ($izin->fileBukti) {
                        Storage::disk('public')->delete($izin->fileBukti);
                    }
                }
                PengajuanIzin::where('idMahasiswa', $id)->delete();
                Presensi::where('idMahasiswa', $id)->delete();
                DB::table('peserta_kuliah')->where('idMahasiswa', $id)->delete();
                LaporanKendala::where('idPelapor', $id)->delete();
                DB::table('notifikasi')->where('idPenerima', $id)->delete();
                DB::table('sesi_pengguna')->where('idUser', $id)->delete();

                $user->delete();
            });
        } catch (\Throwable $e) {
            return redirect()->route('pengguna.daftar')->with('error', 'Data pengguna gagal dihapus');
        }

        return redirect()->route('pengguna.daftar')->with('success', 'Data pengguna berhasil dihapus');
    }

    // cekDataTerkait(idUser): daftar data yang masih dimiliki pengguna (jumlah > 0)
    private function cekDataTerkait(User $user): array
    {
        return array_filter([
            'catatan presensi'      => Presensi::where('idMahasiswa', $user->idUser)->count(),
            'pengajuan izin'        => PengajuanIzin::where('idMahasiswa', $user->idUser)->count(),
            'keikutsertaan kuliah'  => DB::table('peserta_kuliah')->where('idMahasiswa', $user->idUser)->count(),
            'laporan kendala'       => LaporanKendala::where('idPelapor', $user->idUser)->count(),
        ]);
    }
}