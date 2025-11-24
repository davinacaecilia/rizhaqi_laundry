<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function show()
    {
        return view('login');
    }

    /**
     * Memproses data login yang dikirim dari form.
     * INI JAWABAN ATAS PERTANYAANMU.
     */
    public function submit(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cek email & password (ini cara dummy-mu, versi Laravel)
        if ($request->email == 'admin@laundry.com' && $request->password == 'admin123') {
            
            // 3. JIKA BERHASIL:
            // Kita "pura-pura" login (nanti ini pakai Auth::attempt)
            // dan langsung...
            
            // 4. ...ALIHKAN (REDIRECT) KE DASHBOARD!
            return redirect()->route('admin.dashboard');
        
        }

        // 5. JIKA GAGAL:
        // Kembalikan ke halaman login, sambil kirim pesan error.
        return back()->withErrors([
            'email' => 'Email atau password yang kamu masukkan salah!',
        ]);
    }

    /**
     * Menangkap submit form register (dummy, biar nggak error).
     */
    public function registerSubmit(Request $request)
    {
        // Kembalikan saja ke halaman login
        return redirect()->route('login');
    }

    /**
     * Memproses logout.
     */
    public function logout(Request $request)
    {
        // Nanti kita isi logika logout di sini
        return redirect()->route('login');
    }
}