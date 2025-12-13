<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib import ini
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

        // --- PERBAIKAN ZONA WAKTU ---
        // Kita paksa pakai tanggal hari ini di zona waktu Jakarta (WIB)
        $hariIni = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // 2. Hitung Statistik Kartu
        $totalTransaksi   = Transaksi::count();
        
        // Gunakan variabel $hariIni agar hitungan "Hari Ini" akurat sesuai WIB
        $transaksiHariIni = Transaksi::whereDate('tgl_masuk', $hariIni)->count(); 
        
        // 3. Hitung Berat (Menggunakan Stored Function SQL)
        // Kita pakai 'get_total_berat_hari_ini' karena itu yang ada di database lokalmu saat ini
        // Punya temanmu 'fn_...' nanti kita sesuaikan belakangan kalau perlu
        $queryBerat = DB::select("SELECT fn_total_berat_hari_ini() AS total");
        $beratHariIni = $queryBerat[0]->total ?? 0;

        // 4. Kirim semua variabel ke View
        return view('admin.dashboard', compact('recentLogs', 'totalTransaksi', 'transaksiHariIni', 'beratHariIni')); 
    }
}