<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <--- WAJIB ADA BIAR BISA PAKAI QUERY SQL
use App\Models\Log; 
use App\Models\Transaksi;
use Carbon\Carbon;

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
        
        // 3. Hitung Berat Menggunakan Stored Function SQL
        $queryBerat = DB::select("SELECT fn_total_berat_hari_ini() AS total");
        $beratHariIni = $queryBerat[0]->total;

        // 4. Kirim semua variabel ke View
        return view('admin.dashboard', compact('recentLogs', 'totalTransaksi', 'transaksiHariIni', 'beratHariIni')); 
    }
}