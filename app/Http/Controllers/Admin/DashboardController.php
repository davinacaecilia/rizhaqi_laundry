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
    // ... (LOGIKA LAMA TETAP SAMA: Log, Timezone, Stats, Berat) ...

    $recentLogs = Log::with('user')->orderBy('waktu', 'desc')->take(5)->get();
    $hariIni = Carbon::now('Asia/Jakarta')->format('Y-m-d');
    $totalTransaksi = Transaksi::count();
    $transaksiHariIni = Transaksi::whereDate('tgl_masuk', $hariIni)->count();
    
    // Hitung Berat
    $queryBerat = DB::select("SELECT fn_total_berat_hari_ini() AS total");
    $beratHariIni = $queryBerat[0]->total ?? 0;

    // ... (LOGIKA CHART BULANAN TETAP SAMA) ...
    $dataBeratPerBulan = array_fill(0, 12, 0);
    $chartQuery = Transaksi::select(
                    DB::raw('MONTH(tgl_masuk) as bulan'), 
                    DB::raw('SUM(berat) as total_berat')
                )
                ->whereYear('tgl_masuk', date('Y'))
                ->where('status_pesanan', '!=', 'batal')
                ->groupBy('bulan')
                ->get();

    foreach ($chartQuery as $row) {
        $dataBeratPerBulan[$row->bulan - 1] = $row->total_berat;
    }

    // ==========================================================
    // [BARU] AMBIL TRANSAKSI TERBARU (KHUSUS TAMPILAN ADMIN)
    // ==========================================================
    $recentTransactions = Transaksi::with('pelanggan') // Include data pelanggan
        ->orderBy('created_at', 'desc') // Urutkan dari yang paling baru
        ->take(5) // Ambil 5 saja
        ->get();

    return view('admin.dashboard', compact(
        'recentLogs', 
        'totalTransaksi', 
        'transaksiHariIni', 
        'beratHariIni',
        'dataBeratPerBulan',
        'recentTransactions' // <--- JANGAN LUPA TAMBAHKAN INI
    )); 
}
}