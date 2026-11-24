<?php

namespace App\Http\Controllers;

use App\Models\LaporanKendala;
use App\Models\PengajuanIzin;
use App\Models\Perkuliahan;
use App\Models\User;
use App\Services\KehadiranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function __construct(private KehadiranService $kehadiran)
    {
    }

    // getProfil(idPengguna) -> UI-012
    public function getProfil()
    {
        $user = Auth::user();

        return view('profil.index', [
            'user'      => $user,
            'ringkasan' => $this->ringkasan($user),
        ]);
    }

    // ringkasan sesuai peran
    private function ringkasan($user): array
    {
        if ($user->role === 'Mahasiswa') {
            $r     = $this->kehadiran->ringkasanMahasiswa($user->idUser);
            $total = array_sum($r);

            return [
                'Mata kuliah' => DB::table('peserta_kuliah')->where('idMahasiswa', $user->idUser)->count(),
                'Kehadiran'   => ($total ? round($r['Hadir'] / $total * 100) : 0) . '%',
                'Izin'        => PengajuanIzin::where('idMahasiswa', $user->idUser)->count(),
            ];
        }

        if ($user->role === 'Dosen') {
            $kuliah = Perkuliahan::where('idDosen', $user->idUser)->get();

            return [
                'Mata kuliah'     => $kuliah->count(),
                'Kehadiran kelas' => ($this->kehadiran->rataKehadiran($kuliah) ?? 0) . '%',
                'Izin menunggu'   => PengajuanIzin::where('status', 'Menunggu')
                                        ->whereIn('idPerkuliahan', $kuliah->pluck('idPerkuliahan'))->count(),
            ];
        }

        return [
            'Pengguna'     => User::count(),
            'Laporan baru' => LaporanKendala::where('status', 'Baru')->count(),
        ];
    }

    // updateProfil(idPengguna, nama, email, fotoProfil, aksi Simpan/Batal)
    public function updateProfil(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($request->input('aksi') === 'Batal') {
            return redirect()->route('profil');
        }

        // validasiData(nama, email, fotoProfil)
        if (!$this->validasiData($request, $user)) {
            return back()->withInput()->with('error', 'Data profil tidak valid');
        }

        try {
            $data = ['nama' => $request->nama, 'email' => $request->email];

            if ($request->hasFile('fotoProfil')) {
                if ($user->fotoProfil) {
                    Storage::disk('public')->delete($user->fotoProfil);
                }
                $data['fotoProfil'] = $request->file('fotoProfil')->store('foto_profil', 'public');
            }

            $user->update($data);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Profile gagal diperbarui');
        }

        return redirect()->route('profil')->with('success', 'Profile berhasil diperbarui');
    }

    // validasiData(nama, email, fotoProfil): boolean
    private function validasiData(Request $request, $user): bool
    {
        $validator = validator($request->all(), [
            'nama'       => 'required|string|max:100',
            'email'      => ['required', 'email', 'max:100',
                             Rule::unique('user', 'email')->ignore($user->idUser, 'idUser')],
            'fotoProfil' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        return !$validator->fails();
    }

    // menampilkan foto profil (disimpan di storage, bukan folder publik)
    public function foto($id)
    {
        $user = User::findOrFail($id);

        abort_unless($user->fotoProfil && Storage::disk('public')->exists($user->fotoProfil), 404);

        return response()->file(Storage::disk('public')->path($user->fotoProfil));
    }
}