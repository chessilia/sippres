<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class LupaPasswordController extends Controller
{
    // halaman: masukkan email
    public function tampilkanFormLupa()
    {
        return view('auth.lupa');
    }

    // kirim tautan reset ke email terdaftar
    public function kirimTautan(Request $request)
    {
        if (!Validator::make($request->all(), ['email' => 'required|email'])->passes()) {
            return back()->withInput()->with('error', 'Masukkan alamat email yang valid');
        }

        $status = Password::sendResetLink(
            ['email' => $request->email],
            function ($user, $token) {
                // kirim email berisi tautan reset
                $user->sendPasswordResetNotification($token);

                // MODE PENGEMBANGAN: tampilkan tautan di halaman agar mudah diuji
                if (app()->environment('local')) {
                    session()->flash('tautan_dev', route('password.reset', [
                        'token' => $token,
                        'email' => $user->email,
                    ]));
                }
            }
        );

        // permintaan yang terlalu sering
        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput()->with('error', 'Tunggu satu menit sebelum meminta tautan lagi');
        }

        // pesan sama, baik email terdaftar maupun tidak, agar email orang lain tidak bisa ditebak
        return back()->with('success',
            'Jika email terdaftar, tautan untuk mengatur ulang kata sandi telah dikirim');
    }

    // halaman: masukkan kata sandi baru (dibuka dari tautan di email)
    public function tampilkanFormReset(Request $request, $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => $request->email]);
    }

    // simpan kata sandi baru
    public function resetPassword(Request $request)
    {
        $validator = validator($request->all(), [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Kata sandi minimal 8 karakter dan konfirmasinya harus sama');
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => $password])->save();   // di-hash otomatis
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Kata sandi berhasil diperbarui, silakan masuk');
        }

        return back()->withInput($request->only('email'))
            ->with('error', 'Tautan reset tidak valid atau sudah kedaluwarsa');
    }
}