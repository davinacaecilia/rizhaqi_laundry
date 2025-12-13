<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // =========================================================================
    // 1. LAPORAN KEUANGAN & HARIAN (INDEX LAMA)
    // =========================================================================
    public function index(Request $request)
    {
        $tanggal   = $request->input('tanggal', date('Y-m-d'));
        $bulan     = $request->input('bulan', date('m'));
        $tahun     = $request->input('tahun', date('Y'));
        
        // Fitur Tab Pintar
        $activeTab = $request->input('tab', 'harian');

        // A. DATA CUCIAN MASUK (Tab Harian - Tabel Kiri)
        $dataCucian = DB::table('v_laporan_harian')
                        ->whereDate('tgl_masuk', $tanggal)
                        ->orderBy('tgl_masuk', 'desc')
                        ->get();

        // B. DATA ARUS KAS (Tab Harian - Tabel Kanan)
        $arusKas = DB::table('v_arus_kas')
                ->where('tanggal', $tanggal)
                ->get();

        // C. DATA REKAP BULANAN (Tab Bulanan)
        $rekapBulanan = DB::table('v_rekap_keuangan')
                          ->whereMonth('tanggal', $bulan)
                          ->whereYear('tanggal', $tahun)
                          ->orderBy('tanggal', 'asc')
                          ->get();

        return view('admin.laporan.index', compact(
            'dataCucian', 'arusKas', 'rekapBulanan',
            'tanggal', 'bulan', 'tahun', 'activeTab'
        ));
    }

    // =========================================================================
    // 2. LAPORAN KINERJA PEGAWAI (BARU)
    // =========================================================================
    public function laporanPegawai(Request $request)
    {
        // 1. Ambil tanggal dari input user, default hari ini
        $tanggal = $request->input('date', date('Y-m-d'));

        // 2. Query Data Kinerja Pegawai
        // - Mengambil dari tabel transaksi
        // - Join ke tabel users
        // - Filter Tanggal Transaksi
        // - Filter User Role = 'pegawai' (Owner tidak masuk)
        // - Sum Berat
        
        $laporan = DB::table('transaksi')
            ->join('users', 'transaksi.id_user', '=', 'users.id_user')
            ->select(
                'users.nama as nama_pegawai',
                'users.email as email_pegawai',
                DB::raw('SUM(transaksi.berat) as total_berat')
            )
            ->whereDate('transaksi.tgl_masuk', $tanggal)  // Menggunakan tgl_masuk transaksi
            ->where('users.role', 'pegawai')              // <--- FILTER PENTING: HANYA PEGAWAI
            ->groupBy('users.id_user', 'users.nama', 'users.email')
            ->orderBy('total_berat', 'desc')
            ->get();

        return view('admin.laporan.laporan-pegawai', compact('laporan', 'tanggal'));
    }
}