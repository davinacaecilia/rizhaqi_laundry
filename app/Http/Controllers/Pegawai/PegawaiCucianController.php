<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PegawaiCucianController extends Controller
{
    // Menampilkan daftar cucian
    public function index()
    {
        // Dummy data supaya bisa ditampilkan di view
        $cucians = [
            ['id' => 1, 'nama' => 'Cucian A', 'status' => 'Selesai'],
            ['id' => 2, 'nama' => 'Cucian B', 'status' => 'Proses'],
            ['id' => 3, 'nama' => 'Cucian C', 'status' => 'Menunggu'],
        ];

        return view('pegawai.cucian.index', compact('cucians'));
    }

    // Menampilkan form proses cucian
    public function create()
    {
        // Bisa dikasih dummy data juga jika perlu
        return view('pegawai.cucian.create');
    }
}
