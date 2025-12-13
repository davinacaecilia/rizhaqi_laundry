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
        // ==========================================================
        // 1. LOGIKA LAMA (TIDAK DIUBAH)
        // ==========================================================
        
        // Ambil 5 Log Terakhir
        $recentLogs = Log::with('user') 
                         ->orderBy('waktu', 'desc')
                         ->take(5)
                         ->get();

        // Perbaikan Zona Waktu Jakarta
        $hariIni = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // Hitung Statistik Kartu
        $totalTransaksi   = Transaksi::count();
        $transaksiHariIni = Transaksi::whereDate('tgl_masuk', $hariIni)->count(); 
        
        // Hitung Berat (Pakai Function SQL kamu yg sebelumnya)
        $queryBerat = DB::select("SELECT fn_total_berat_hari_ini() AS total");
        $beratHariIni = $queryBerat[0]->total ?? 0;


        // ==========================================================
        // 2. LOGIKA BARU: DATA CHART BULANAN
        // ==========================================================
        
        // A. Siapkan kerangka array kosong (0 sampai 0) untuk 12 bulan
        // Index 0 = Januari, Index 11 = Desember
        $dataBeratPerBulan = array_fill(0, 12, 0);

        // B. Query database: Jumlahkan berat, Kelompokkan per Bulan, Tahun Ini saja
        $chartQuery = Transaksi::select(
                            DB::raw('MONTH(tgl_masuk) as bulan'), 
                            DB::raw('SUM(berat) as total_berat')
                        )
                        ->whereYear('tgl_masuk', date('Y')) // Filter Tahun Ini
                        ->groupBy('bulan')
                        ->get();

        // C. Masukkan data DB ke dalam Array kerangka tadi
        foreach ($chartQuery as $row) {
            // $row->bulan isinya 1 s/d 12.
            // Array Index mulainya dari 0. Jadi bulan - 1.
            $dataBeratPerBulan[$row->bulan - 1] = $row->total_berat;
        }


        // ==========================================================
        // 3. KIRIM KE VIEW
        // ==========================================================
        return view('admin.dashboard', compact(
            'recentLogs', 
            'totalTransaksi', 
            'transaksiHariIni', 
            'beratHariIni',
            'dataBeratPerBulan' // <--- Variabel baru untuk Chart
        )); 
    }
}