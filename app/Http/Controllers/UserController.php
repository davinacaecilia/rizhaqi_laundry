<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

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
        $transaksi = DB::connection('public_access')
                    ->table('transaksi')
                    ->where('kode_invoice', $kode)
                    ->first();

        $status_display = 'Kode pesanan tidak ditemukan.';
        $raw_status = null;
        $isSuccess = false;

        if ($transaksi) {
            $raw_status = $transaksi->status_pesanan;
            $isSuccess = true;

            // --- LOGIC PENGELOMPOKAN STATUS UNTUK USER (5 KATEGORI) ---
            $diproses_group = ['dicuci', 'dikeringkan', 'disetrika', 'packing'];

            if (in_array($raw_status, $diproses_group)) {
                $status_for_user = 'diproses';
                $status_display = 'Diproses'; // Tampilkan detail internal juga
            } elseif ($raw_status == 'siap diambil') {
                $status_for_user = 'siap_diambil';
                $status_display = 'Siap Diambil';
            } elseif ($raw_status == 'diterima') {
                $status_for_user = 'diterima';
                $status_display = 'Diterima';
            } elseif ($raw_status == 'batal') {
                $status_for_user = 'batal';
                $status_display = 'Dibatalkan';
            } elseif ($raw_status == 'selesai') {
                $status_for_user = 'selesai';
                $status_display = 'Selesai';
            } else {
                // Fallback untuk status yang tidak terdefinisi
                $status_for_user = $raw_status;
                $status_display = ucwords(str_replace('_', ' ', $raw_status));
            }

            // Kirim status yang dikelompokkan ke view
            return view('status', compact('status_display', 'kode', 'isSuccess', 'status_for_user'));
        }

        // Kasus kode tidak ditemukan
        return view('status', compact('status_display', 'kode', 'isSuccess', 'raw_status'));
    }
}
