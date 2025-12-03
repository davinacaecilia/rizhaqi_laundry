<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;

class PegawaiTransaksiController extends Controller
{
    public function index()
    {
        // Data dummy utk tampilan
        $transaksi = [
            ['id' => 1, 'nama' => 'Budi', 'status' => 'proses'],
            ['id' => 2, 'nama' => 'Ani', 'status' => 'selesai'],
        ];

        return view('pegawai.transaksi.index', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = [
            'id' => $id,
            'nama' => 'Dummy Pelanggan',
            'berat' => 5,
            'status' => 'proses'
        ];

        return view('pegawai.transaksi.show', compact('transaksi'));
    }

    public function status($id = null)
    {
        if ($id) {
            $transaksi = [
                'id' => $id,
                'nama' => 'Dummy Pelanggan',
                'status' => 'proses'
            ];

            return view('pegawai.transaksi.status', compact('transaksi'));
        }

        $transaksi = [
            ['id' => 1, 'nama' => 'Budi', 'status' => 'proses'],
            ['id' => 2, 'nama' => 'Ani', 'status' => 'selesai'],
        ];

        return view('pegawai.transaksi.status', compact('transaksi'));
    }

    public function updateStatus($id)
    {
        return back()->with('success', 'Status berhasil diperbarui (dummy).');
    }
}
