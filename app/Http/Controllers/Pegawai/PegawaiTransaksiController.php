<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\LaporanHarianPegawai;
use Illuminate\Support\Facades\Auth;

class PegawaiTransaksiController extends Controller
{

    public function status(Request $request)
    {
        $query = Transaksi::with('pelanggan')
            // 1. Ambil kolom asli
            ->select('transaksi.*')

            // 2. Wajib: Tambahkan VIRTUAL COLUMNS yang dibutuhkan oleh Blade Admin
            // Ini agar Blade tidak error saat mengakses $item->sisa_tagihan
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')
            ->selectRaw('fn_sisa_tagihan(id_transaksi) as sisa_tagihan')

            // 3. Filter status sesuai User Requirement Pegawai (Setrika & Packing)
            ->whereIn('status_pesanan', ['disetrika', 'packing']);

        // Filter Tanggal
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('tgl_masuk', $request->date);
        }

        // Urutkan berdasarkan status (disetrika dulu, baru packing) dan tanggal masuk
        $transaksi = $query->orderByRaw("FIELD(status_pesanan, 'disetrika', 'packing')")
            ->orderBy('tgl_masuk', 'asc')
            ->get();

        // Variabel $counts harus dikirim sebagai array kosong agar view tidak error
        $counts = [];

        // Asumsi nama view yang benar adalah 'pegawai.transaksi.status'
        return view('pegawai.transaksi.status', compact('transaksi', 'counts'));
    }

    public function updateStatus($id)
    {
        $trx = Transaksi::findOrFail($id);

        // Hanya izinkan update dari 'disetrika' ke 'packing'
        if ($trx->status_pesanan !== 'disetrika') {
            return redirect()->back()->with(
                'error',
                'Status pesanan ' . $trx->kode_invoice . ' tidak dapat diubah (Status saat ini: ' . $trx->status_pesanan . ').'
            );
        }

        // Update status transaksi
        $trx->update([
            'status_pesanan' => 'packing'
        ]);

        // ✅ BUAT LAPORAN (ANTI DUPLIKAT)
        LaporanHarianPegawai::firstOrCreate(
            [
                'id_transaksi' => $trx->id_transaksi, // kunci unik
            ],
            [
                'id_user'        => Auth::id(),
                'tgl_dikerjakan' => now(),
            ]
        );

        return redirect()->back()->with(
            'success',
            'Status pesanan ' . $trx->kode_invoice . ' berhasil diperbarui ke Packing.'
        );
    }

}

