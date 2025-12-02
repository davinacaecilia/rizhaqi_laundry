<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        // Di sini nanti kamu bisa kirim data summary keuangan ke view
        // Misal: $totalPemasukan = Transaksi::where('status_bayar', 'lunas')->sum('total_biaya');
        
        return view('admin.laporan.index');
    }
}