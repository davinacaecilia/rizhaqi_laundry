<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LaporanHarianPegawai;
use Illuminate\Support\Facades\DB; // Import DB Facade

class LaporanPegawaiController extends Controller
{
    public function index()
{
    $laporan = LaporanHarianPegawai::select(
            // GANTI 'laporan_harian_pegawai.id' DENGAN NAMA PRIMARY KEY YANG BENAR
            'laporan_harian_pegawai.id_laporan', // <-- ASUMSI NAMA PRIMARY KEY

            'laporan_harian_pegawai.tgl_dikerjakan',
            'laporan_harian_pegawai.id_transaksi',
            'u.nama as nama_pegawai',
            DB::raw('COALESCE(SUM(dt.jumlah), 0) as total_berat')
        )
        ->join('users as u', 'laporan_harian_pegawai.id_user', '=', 'u.id_user')
        ->leftJoin('detail_transaksi as dt', 'laporan_harian_pegawai.id_transaksi', '=', 'dt.id_transaksi')

        // Ganti juga di GROUP BY
        ->groupBy(
            'laporan_harian_pegawai.id_laporan', // <-- GANTI DI SINI JUGA
            'laporan_harian_pegawai.tgl_dikerjakan',
            'laporan_harian_pegawai.id_transaksi',
            'u.nama'
        )
        ->orderBy('laporan_harian_pegawai.tgl_dikerjakan', 'desc')
        ->get();

    return view('pegawai.laporan-pegawai', compact('laporan'));
}
}
