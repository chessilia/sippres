<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // tampilkanHalamanLogin() -> UI-003
    public function tampilkanHalamanLogin()
    {
        return view('auth.login');
    }

    // login() -> PSD-002
    public function login(Request $request)
    {
        $kredensial = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // validasiKredensial(email, password)
        if (Auth::attempt($kredensial)) {
            $request->session()->regenerate();

            // identifikasiPeran() -> tampilkanDashboard(peran)
            return match (Auth::user()->role) {
                'Mahasiswa' => redirect()->route('dashboard.mahasiswa'),
                'Dosen'     => redirect()->route('dashboard.dosen'),
                'Admin'     => redirect()->route('dashboard.admin'),
            };
        }

        return back()->withInput($request->only('email'))
            ->with('error', 'Email atau password salah');
    }

    // logout() -> PSD-003
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }
}