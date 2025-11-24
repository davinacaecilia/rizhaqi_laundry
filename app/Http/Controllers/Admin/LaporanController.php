<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function transaksi()
    {
        return view('admin.laporan.transaksi');
    }

    public function pendapatan()
    {
        return view('admin.laporan.pendapatan');
    }
}