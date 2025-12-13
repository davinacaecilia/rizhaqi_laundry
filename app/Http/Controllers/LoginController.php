<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function show()
    {
        return view('login');
    }
    public function submit(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        // HANYA ROLE INI YANG BOLEH LOGIN
        if (!in_array($user->role, ['admin', 'owner', 'pegawai'])) {
            Auth::logout();
            return back()->with('error', 'Akun tidak memiliki akses.');
        }

        // Redirect sesuai role
        if (in_array($user->role, ['admin', 'owner'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'pegawai') {
            return redirect()->route('pegawai.dashboard');
        }
    }

    return back()->with('error', 'Email atau password salah.');
}

   public function logout()
{
    Auth::logout();

    request()->session()->forget('internal_login'); // ⬅️ PENTING
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/')->with('success', 'Anda berhasil logout!');
}

}
