<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanHarianPegawai;
use Illuminate\Support\Facades\Auth;

class LaporanPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $currentUserId = Auth::id();

        if (!$currentUserId) {
            return redirect('/login')->with('error', 'Anda harus login untuk mengakses laporan.');
        }

        $dateFilter = $request->input('date');

        $query = LaporanHarianPegawai::with([
                'transaksi.pelanggan',
                'pegawai'
            ])
            ->where('id_user', $currentUserId);

        if ($dateFilter) {
            $query->whereDate('tgl_dikerjakan', $dateFilter);
        }

        $laporan = $query->orderBy('tgl_dikerjakan', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->get();

        // Total berat dari transaksi
        $totalBeratDikerjakan = $laporan->sum(function ($item) {
            return $item->transaksi->berat ?? 0;
        });

        return view('pegawai.laporan-pegawai', compact(
            'laporan',
            'dateFilter',
            'totalBeratDikerjakan'
        ));
    }
}
