<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegistrasiController extends Controller
{
    // tampilkanHalamanRegistrasi() -> UI-002
    public function tampilkanHalamanRegistrasi()
    {
        return view('auth.registrasi');
    }

    // submitRegistrasi() -> PSD-001
    public function submitRegistrasi(Request $request)
    {
        // aksi "Batal" -> kembali ke landing page
        if ($request->input('aksi') === 'Batal') {
            return redirect()->route('landing');
        }

        // cekEmailTerdaftar(email)
        if (User::where('email', $request->email)->exists()) {
            return back()->withInput()->with('error', 'Email telah terdaftar');
        }

        // validasiData(username, nama, email, password)
        if (!$this->validasiData($request)) {
            return back()->withInput()
                ->with('error', 'Data belum lengkap / Password tidak sesuai');
        }

        // simpanUser(role, username, nama, email, password)
        try {
            User::create([
                'role'     => $request->role,
                'username' => $request->username,
                'nama'     => $request->nama,
                'email'    => $request->email,
                'password' => $request->password,
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Registrasi gagal, silakan coba lagi');
        }

        return redirect()->route('login')->with('success', 'Registrasi berhasil');
    }

    // validasiData(): boolean
    private function validasiData(Request $request): bool
    {
        $validator = validator($request->all(), [
            'role'     => 'required|in:Mahasiswa,Dosen',
            'username' => 'required|string|max:30|unique:user,username',
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100',
            'password' => 'required|string|min:8',
        ]);

        return !$validator->fails();
    }
}