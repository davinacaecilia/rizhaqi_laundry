<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;

class PegawaiPelangganController extends Controller
{
    public function index()
    {
        $pelanggan = [
            ['nama' => 'Budi'],
            ['nama' => 'Tika'],
            ['nama' => 'Andi']
        ];

        return view('pegawai.pelanggan.index', compact('pelanggan'));
    }
}
