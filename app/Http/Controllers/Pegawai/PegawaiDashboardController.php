<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Carbon\Carbon;
use App\Models\LaporanHarianPegawai; // <--- WAJIB IMPORT INI
use Illuminate\Support\Facades\Auth;

class PegawaiDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        $currentYear = Carbon::now()->year;

        // ==========================================
        // 1. LOGIKA "TOTAL SELESAI HARI INI" (Sesuai Laporan)
        // ==========================================
        // Menghitung berapa kali pegawai menekan tombol "Selesai" hari ini.
        $selesaiHariIni = LaporanHarianPegawai::where('id_user', $userId)
            ->whereDate('tgl_dikerjakan', $today)
            ->count(); 
        
        // ==========================================
        // 2. LOGIKA CHART BULANAN (KINERJA PEGAWAI)
        // ==========================================
        // Kita siapkan array kosong untuk bulan 1 s/d 12
        $monthlyPerformance = array_fill(1, 12, 0);

        // Ambil data laporan tahun ini milik pegawai tersebut
        $laporanTahunan = LaporanHarianPegawai::with('transaksi')
            ->where('id_user', $userId)
            ->whereYear('tgl_dikerjakan', $currentYear)
            ->get();

        // Looping untuk menjumlahkan berat per bulan
        foreach ($laporanTahunan as $lap) {
            $bulan = Carbon::parse($lap->tgl_dikerjakan)->format('n'); // Ambil angka bulan (1-12)
            $berat = $lap->transaksi->berat ?? 0; // Ambil berat dari relasi transaksi
            
            // Tambahkan berat ke bulan yang sesuai
            $monthlyPerformance[$bulan] += $berat;
        }

        // Ubah jadi array index 0-11 untuk Chart.js (Jan, Feb, ... Des)
        $chartData = array_values($monthlyPerformance);

        // ==========================================
        // 3. AMBIL PREVIEW ANTRIAN (Tugas yang belum selesai)
        // ==========================================
        // Menampilkan order yg statusnya 'disetrika' atau 'packing'
        // supaya pegawai tahu apa yang harus dikerjakan.
        $recentOrders = Transaksi::with('pelanggan')
            ->whereIn('status_pesanan', ['disetrika', 'packing'])
            ->orderBy('updated_at', 'desc') 
            ->limit(5) 
            ->get();

        return view('pegawai.dashboard', compact('selesaiHariIni', 'recentOrders', 'chartData'));
    }
}
