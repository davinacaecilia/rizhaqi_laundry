<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LaporanHarianPegawai;
use Carbon\Carbon;

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

    public function kinerjaPegawai(Request $request)
    {
        // 1. Filter Satu Tanggal (Default: Hari Ini)
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));

        // 2. Query ke Database View
        // Kita filter whereDate 'tgl_dikerjakan' sama dengan tanggal yang dipilih
        $laporanKinerja = DB::table('v_kinerja_pegawai')
            ->select(
                'id_user',
                'nama_pegawai',
                // Kita tetap pakai SUM untuk jaga-jaga jika ada multiple entries, 
                // meski harusnya per hari cuma 1 row per user di View.
                DB::raw('SUM(total_tugas) as kinerja_count'),
                DB::raw('SUM(total_berat) as kinerja_berat')
            )
            ->whereDate('tgl_dikerjakan', $tanggal) 
            ->groupBy('id_user', 'nama_pegawai')
            ->orderByDesc('kinerja_berat')
            ->get();

        // 3. Hitung Grand Total Hari Itu
        $grandTotalBerat = $laporanKinerja->sum('kinerja_berat');
        $grandTotalTugas = $laporanKinerja->sum('kinerja_count');

        return view('admin.laporan.kinerja', compact(
            'laporanKinerja', 
            'tanggal', 
            'grandTotalBerat',
            'grandTotalTugas'
        ));
>>>>>>> 2df83f4394ec46d724f2249de77a520240f74e9c
    }
}