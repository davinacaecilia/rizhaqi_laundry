<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // Halaman Home
    public function home()
    {
        return view('home');
    }

    // Halaman Daftar Harga
    public function harga()
    {
        return view('harga');
    }

    // Halaman Cek Status
    public function status()
    {
        return view('status');
    }

    // Proses cek status pesanan
    public function checkStatus(Request $request)
    {
        $kode = $request->input('kode');

        // Contoh data dummy (nanti bisa diambil dari database)
        $dataPesanan = [
            'RZQ001' => 'Sedang Diproses',
            'RZQ002' => 'Sudah Selesai',
            'RZQ003' => 'Sudah Diambil',
        ];

        $status = $dataPesanan[$kode] ?? 'Kode pesanan tidak ditemukan.';

        return view('status', compact('status', 'kode'));
    }
}
