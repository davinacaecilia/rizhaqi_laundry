<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log; 
use App\Models\Transaksi;
use Carbon\Carbon; // PENTING: Import Carbon untuk filter hari ini

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil 5 Log Terakhir
        $recentLogs = Log::with('user') 
                         ->orderBy('waktu', 'desc')
                         ->take(5)
                         ->get();

        // 2. Hitung Statistik Kartu (Real-time dari Database)
        $totalTransaksi   = Transaksi::count(); // Total semua baris
        $transaksiHariIni = Transaksi::whereDate('tgl_masuk', Carbon::today())->count(); // Cuma hari ini
        $beratHariIni     = Transaksi::whereDate('tgl_masuk', Carbon::today())->sum('berat'); // Total berat hari ini

        // 3. Kirim semua variabel ke View
        return view('admin.dashboard', compact('recentLogs', 'totalTransaksi', 'transaksiHariIni', 'beratHariIni')); 
    }
}