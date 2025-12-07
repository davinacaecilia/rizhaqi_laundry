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

            // Dapatkan user yang baru login
            $user = Auth::user();

            switch ($user->role) {
                case 'admin':
                case 'owner':
                    return redirect()->route('admin.dashboard');
                case 'pegawai':
                    // Redirect Pegawai ke Dashboard Pegawai
                    return redirect()->route('pegawai.dashboard');
                default:
                    return redirect('/');
            }
        }

        return back()->with('error', 'Email atau Password salah!');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->to('/')->with('success', 'Anda berhasil logout!');
    }
}
