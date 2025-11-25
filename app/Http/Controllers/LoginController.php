<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class LoginController extends Controller
{
    public function show()
    {
        return view('login');
    }

    // Tangani form login (dummy)
    public function submit(Request $request)
    {
        // Versi frontend aja — ga pakai database
        // Jadi langsung redirect ke dashboard
        return redirect()->to('admin/dashboard');
    }

    // Logout (dummy)
    public function logout()
    {
        // Di frontend, cukup redirect ke halaman login lagi
        return redirect()->to('/');
    }
}
