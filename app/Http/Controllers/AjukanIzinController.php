<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Perkuliahan;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AjukanIzinController extends Controller
{
    // hanya Mahasiswa yang boleh mengakses
    private function cekMahasiswa(): void
    {
        abort_unless(Auth::user()->role === 'Mahasiswa', 403);
    }

    // getDataPerkuliahan(idMahasiswa) -> UI-008 (form + daftar pengajuan saya)
    public function getDataPerkuliahan(Request $request)
    {
        $this->cekMahasiswa();
        $idMahasiswa = Auth::id();

        $ikut = DB::table('peserta_kuliah')
                  ->where('idMahasiswa', $idMahasiswa)->pluck('idPerkuliahan');

        // pengajuan yang sedang diubah (hanya milik sendiri dan masih "Menunggu")
        $ubah = $request->filled('ubah')
            ? PengajuanIzin::where('idIzin', $request->ubah)
                ->where('idMahasiswa', $idMahasiswa)
                ->where('status', 'Menunggu')->first()
            : null;

        return view('mahasiswa.izin', [
            'daftarPerkuliahan' => Perkuliahan::whereIn('idPerkuliahan', $ikut)->get(),
            'daftarPengajuan'   => PengajuanIzin::with('perkuliahan')
                                    ->where('idMahasiswa', $idMahasiswa)
                                    ->orderByDesc('idIzin')->get(),
            'ubah'              => $ubah,
        ]);
    }

    // kirimPengajuan(idMahasiswa, idPerkuliahan, pertemuan, alasan, fileBukti, aksi)
    public function kirimPengajuan(Request $request)
    {
        $this->cekMahasiswa();

        // aksi "Batal" -> kembali ke dashboard
        if ($request->input('aksi') === 'Batal') {
            return redirect()->route('dashboard.mahasiswa');
        }

        // validasiData(...)
        if (!$this->validasiData($request)) {
            return back()->withInput()->with('error', 'Data belum lengkap / Format bukti tidak sesuai');
        }

        if ($this->sudahAda($request)) {
            return back()->withInput()->with('error', 'Pengajuan untuk pertemuan ini sudah ada');
        }

        $path = $request->hasFile('fileBukti')
            ? $request->file('fileBukti')->store('bukti_izin', 'public')
            : null;

        // simpanPengajuan(..., "Menunggu") + kirim notifikasi ke dosen pengampu
        try {
            DB::transaction(function () use ($request, $path) {
                $mahasiswa   = Auth::user();
                $perkuliahan = Perkuliahan::findOrFail($request->idPerkuliahan);

                PengajuanIzin::create([
                    'idMahasiswa'      => $mahasiswa->idUser,
                    'idPerkuliahan'    => $request->idPerkuliahan,
                    'pertemuan'        => $request->pertemuan,
                    'alasan'           => $request->alasan,
                    'fileBukti'        => $path,
                    'status'           => 'Menunggu',
                    'tanggalPengajuan' => now(),
                ]);

                if ($perkuliahan->idDosen) {
                    Notifikasi::create([
                        'idPenerima' => $perkuliahan->idDosen,
                        'jenis'      => 'Izin',
                        'isi'        => "{$mahasiswa->nama} mengajukan izin untuk {$perkuliahan->namaMatkul} kelas {$perkuliahan->kelas} (Pertemuan {$request->pertemuan}).",
                        'waktu'      => now(),
                        'statusBaca' => false,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            return back()->withInput()->with('error', 'Pengajuan izin gagal dikirim');
        }

        return redirect()->route('izin.mahasiswa')->with('success', 'Pengajuan izin berhasil dikirim');
    }

    // ubah pengajuan selama belum diproses dosen (SR-22)
    public function ubahPengajuan(Request $request, $id)
    {
        $this->cekMahasiswa();

        $izin = PengajuanIzin::where('idIzin', $id)->where('idMahasiswa', Auth::id())->firstOrFail();

        if ($izin->status !== 'Menunggu') {
            return redirect()->route('izin.mahasiswa')
                ->with('error', 'Pengajuan sudah diproses dosen, tidak bisa diubah');
        }

        if (!$this->validasiData($request)) {
            return back()->withInput()->with('error', 'Data belum lengkap / Format bukti tidak sesuai');
        }

        if ($this->sudahAda($request, $izin->idIzin)) {
            return back()->withInput()->with('error', 'Pengajuan untuk pertemuan ini sudah ada');
        }

        $data = [
            'idPerkuliahan' => $request->idPerkuliahan,
            'pertemuan'     => $request->pertemuan,
            'alasan'        => $request->alasan,
        ];

        $pathBaru = null;
        $pathLama = $izin->fileBukti;

        if ($request->hasFile('fileBukti')) {
            $pathBaru = $request->file('fileBukti')->store('bukti_izin', 'public');
            $data['fileBukti'] = $pathBaru;
        }

        try {
            DB::transaction(function () use ($izin, $data, $request) {
                $mahasiswa   = Auth::user();
                $perkuliahan = Perkuliahan::findOrFail($request->idPerkuliahan);

                $izin->update($data);

                if ($perkuliahan->idDosen) {
                    Notifikasi::create([
                        'idPenerima' => $perkuliahan->idDosen,
                        'jenis'      => 'Izin',
                        'isi'        => "{$mahasiswa->nama} memperbarui pengajuan izin untuk {$perkuliahan->namaMatkul} kelas {$perkuliahan->kelas} (Pertemuan {$request->pertemuan}).",
                        'waktu'      => now(),
                        'statusBaca' => false,
                    ]);
                }
            });

            if ($pathBaru && $pathLama) {
                Storage::disk('public')->delete($pathLama);
            }
        } catch (\Throwable $e) {
            if ($pathBaru) {
                Storage::disk('public')->delete($pathBaru);
            }
            return back()->withInput()->with('error', 'Pengajuan izin gagal diperbarui');
        }

        return redirect()->route('izin.mahasiswa')->with('success', 'Pengajuan izin berhasil diperbarui');
    }

    // batalkan pengajuan selama belum diproses dosen (SR-22)
    public function batalkanPengajuan($id)
    {
        $this->cekMahasiswa();

        $izin = PengajuanIzin::with('perkuliahan')
            ->where('idIzin', $id)
            ->where('idMahasiswa', Auth::id())
            ->firstOrFail();

        if ($izin->status !== 'Menunggu') {
            return back()->with('error', 'Pengajuan sudah diproses dosen, tidak bisa dibatalkan');
        }

        $fileBukti   = $izin->fileBukti;
        $perkuliahan = $izin->perkuliahan;
        $pertemuan   = $izin->pertemuan;

        try {
            DB::transaction(function () use ($izin, $perkuliahan, $pertemuan) {
                $mahasiswa = Auth::user();
                $izin->delete();

                if ($perkuliahan && $perkuliahan->idDosen) {
                    Notifikasi::create([
                        'idPenerima' => $perkuliahan->idDosen,
                        'jenis'      => 'Izin',
                        'isi'        => "{$mahasiswa->nama} membatalkan pengajuan izin untuk {$perkuliahan->namaMatkul} kelas {$perkuliahan->kelas} (Pertemuan {$pertemuan}).",
                        'waktu'      => now(),
                        'statusBaca' => false,
                    ]);
                }
            });

            if ($fileBukti) {
                Storage::disk('public')->delete($fileBukti);
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Pengajuan izin gagal dibatalkan');
        }

        return redirect()->route('izin.mahasiswa')->with('success', 'Pengajuan izin dibatalkan');
    }

    // validasiData(idPerkuliahan, pertemuan, alasan, fileBukti): boolean
    private function validasiData(Request $request): bool
    {
        $validator = validator($request->all(), [
            'idPerkuliahan' => ['required',
                Rule::exists('peserta_kuliah', 'idPerkuliahan')->where('idMahasiswa', Auth::id())],
            'pertemuan' => 'required|integer|between:1,16',
            'alasan'    => 'required|string|max:255',
            'fileBukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        return !$validator->fails();
    }

    // cegah pengajuan ganda untuk perkuliahan dan pertemuan yang sama
    private function sudahAda(Request $request, $kecuali = null): bool
    {
        return PengajuanIzin::where('idMahasiswa', Auth::id())
            ->where('idPerkuliahan', $request->idPerkuliahan)
            ->where('pertemuan', $request->pertemuan)
            ->whereIn('status', ['Menunggu', 'Disetujui'])
            ->when($kecuali, fn ($q) => $q->where('idIzin', '!=', $kecuali))
            ->exists();
    }
}