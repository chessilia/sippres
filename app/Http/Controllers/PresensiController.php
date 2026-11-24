<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\SesiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    // hanya Mahasiswa yang boleh mengakses
    private function cekMahasiswa(): void
    {
        abort_unless(Auth::user()->role === 'Mahasiswa', 403);
    }

    // halaman Presensi Pindai QR Code -> UI-007
    public function index()
    {
        $this->cekMahasiswa();

        return view('mahasiswa.presensi', [
            'daftarSesi' => $this->getSesiTersedia(Auth::id()),
        ]);
    }

    // getSesiTersedia(idMahasiswa): sesi aktif pada perkuliahan yang diikuti
    public function getSesiTersedia($idMahasiswa)
    {
        $ikut = DB::table('peserta_kuliah')
                  ->where('idMahasiswa', $idMahasiswa)->pluck('idPerkuliahan');

        $sudah = DB::table('presensi')
                   ->where('idMahasiswa', $idMahasiswa)->pluck('idSesi');

        return SesiPresensi::with('perkuliahan')
            ->where('status', 'Aktif')
            ->whereIn('idPerkuliahan', $ikut)
            ->get()
            ->filter(fn ($sesi) => $sesi->masihBerlaku())
            ->each(fn ($sesi) => $sesi->sudahPresensi = $sudah->contains($sesi->idSesi))
            ->values();
    }

    // kirimPresensi(idMahasiswa, idSesi, kodeQR, lokasi) -> PSD-012
    public function kirimPresensi(Request $request)
    {
        $this->cekMahasiswa();

        $request->validate([
            'idSesi'    => 'required|integer',
            'kodeQR'    => 'required|string',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $idMahasiswa = Auth::id();
        $sesi = SesiPresensi::find($request->idSesi);

        // 1. periksa status sesi presensi
        if (!$sesi || !$sesi->masihBerlaku()) {
            if ($sesi && $sesi->status === 'Aktif') {
                $sesi->update(['status' => 'Selesai']);   // sudah lewat durasi
            }
            return back()->with('error', 'Sesi presensi tidak dapat digunakan');
        }

        // 2. mahasiswa harus terdaftar pada perkuliahan ini
        $terdaftar = DB::table('peserta_kuliah')
            ->where('idMahasiswa', $idMahasiswa)
            ->where('idPerkuliahan', $sesi->idPerkuliahan)->exists();
        if (!$terdaftar) {
            return back()->with('error', 'Anda tidak terdaftar pada perkuliahan ini');
        }

        // 3. kode QR harus cocok
        if (trim($request->kodeQR) !== $sesi->kodeQR) {
            return back()->with('error', 'QR Code tidak valid');
        }

        // 4. tolak presensi ganda pada sesi yang sama
        $sudah = Presensi::where('idMahasiswa', $idMahasiswa)
                         ->where('idSesi', $sesi->idSesi)->exists();
        if ($sudah) {
            return back()->with('error', 'Anda sudah melakukan presensi pada sesi ini');
        }

        // 5. validasi lokasi (hanya jika dosen mengaktifkan radius)
        $lokasi = null;
        if ($request->filled(['latitude', 'longitude'])) {
            $lokasi = round($request->latitude, 6) . ',' . round($request->longitude, 6);
        }

        if ($sesi->radius > 0) {
            if ($lokasi === null) {
                return back()->with('error', 'Lokasi tidak terdeteksi. Izinkan akses lokasi di browser.');
            }

            $jarak = $this->hitungJarakMeter(
                $sesi->latitude, $sesi->longitude,
                $request->latitude, $request->longitude
            );

            if ($jarak > $sesi->radius) {
                return back()->with('error',
                    'Anda berada di luar radius kelas (' . round($jarak) . ' m dari kelas, batas ' . $sesi->radius . ' m)');
            }
        }

        // simpan presensi
        Presensi::create([
            'idMahasiswa' => $idMahasiswa,
            'idSesi'      => $sesi->idSesi,
            'waktu'       => now(),
            'lokasi'      => $lokasi,
            'status'      => 'Hadir',
        ]);

        // notifikasi: presensi berhasil dicatat (SR-21)
        DB::table('notifikasi')->insert([
            'idPenerima' => $idMahasiswa,
            'jenis'      => 'Presensi',
            'isi'        => 'Presensi ' . $sesi->perkuliahan->namaMatkul
                          . ' berhasil dicatat pukul ' . now()->format('H:i'),
            'waktu'      => now(),
            'statusBaca' => false,
        ]);

        return redirect()->route('presensi.mahasiswa')->with('success', 'Presensi berhasil dicatat');
    }

    // jarak dua titik koordinat dalam meter (rumus haversine)
    private function hitungJarakMeter($lat1, $lon1, $lat2, $lon2): float
    {
        $r    = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return 2 * $r * asin(sqrt($a));
    }
}