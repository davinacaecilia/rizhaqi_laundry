<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanHarianPegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $currentUserId = Auth::id();

        // 1. SET DEFAULT TANGGAL (HARI INI)
        // Jika user tidak memilih tanggal (null), kita isi otomatis dengan hari ini.
        $dateFilter = $request->input('date') ?? Carbon::today()->format('Y-m-d');

        // 2. PANGGIL STORED PROCEDURE
        // Kita kirim ID User dan Tanggal ke Database
        $laporan = DB::select("CALL sp_get_laporan_harian_pegawai(?, ?)", [
            $currentUserId,
            $dateFilter
        ]);

        // 3. OLAH DATA (ARRAY -> COLLECTION)
        // Ubah jadi Collection agar bisa pakai fitur .sum(), .isEmpty() di Blade
        $laporanCollection = collect($laporan);

        // Hitung Total Berat
        $totalBeratDikerjakan = $laporanCollection->sum('berat');

        // 4. KIRIM KE VIEW
        return view('pegawai.laporan-pegawai', [
            'laporan' => $laporanCollection,
            'dateFilter' => $dateFilter, // Kirim balik tanggalnya agar input date terisi
            'totalBeratDikerjakan' => $totalBeratDikerjakan
        ]);
    }
}
