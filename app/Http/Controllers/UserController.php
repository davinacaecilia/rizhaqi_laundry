<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use App\Models\Layanan;

class UserController extends Controller
{
    // Halaman Home
    public function home()
    {
        $layanan = Layanan::on('public_access') // Gunakan koneksi terbatas// Tidak perlu tampilkan Add On
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_layanan', 'asc')
            ->get();

        // Kelompokkan layanan berdasarkan kategori
        $layanan_by_kategori = $layanan->groupBy('kategori');

        return view('home', compact('layanan_by_kategori'));
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

            $diproses_group = ['dicuci', 'dikeringkan', 'disetrika', 'packing'];

            if (in_array($raw_status, $diproses_group)) {
                $status_for_user = 'diproses';
                $status_display = 'Diproses'; 
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
                $status_for_user = $raw_status;
                $status_display = ucwords(str_replace('_', ' ', $raw_status));
            }

            return view('status', compact('status_display', 'kode', 'isSuccess', 'status_for_user'));
        }

        return view('status', compact('status_display', 'kode', 'isSuccess', 'raw_status'));
    }
}
