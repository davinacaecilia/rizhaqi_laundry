<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\LaporanHarianPegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    // 1. Cek Data & Validasi Awal (Tetap pakai Eloquent utk baca status)
    $trx = Transaksi::findOrFail($id);

    // SOP: Pegawai hanya boleh mengubah dari 'disetrika' ke 'packing'
    if ($trx->status_pesanan !== 'disetrika') {
        return redirect()->back()->with(
            'error',
            'Status pesanan ' . $trx->kode_invoice . ' tidak dapat diubah (Status saat ini: ' . $trx->status_pesanan . ').'
        );
    }

    try {
        // 2. Jalankan Stored Procedure untuk Update Status
        // Parameter: (ID Transaksi, Status Baru, ID User)
        DB::statement("CALL sp_update_status_transaksi(?, ?, ?)", [
            $id,
            'packing', // Hardcode 'packing' karena pegawai cuma punya akses ini
            Auth::id()
        ]);

        // 3. Catat ke Laporan Harian Pegawai
        // (Kita taruh sini agar terekam setelah SP berhasil dijalankan)
        LaporanHarianPegawai::firstOrCreate(
            ['id_transaksi' => $id], // Cek agar tidak duplikat
            [
                'id_user'        => Auth::id(),
                'tgl_dikerjakan' => now(),
            ]
        );

        return redirect()->back()->with(
            'success',
            'Status pesanan ' . $trx->kode_invoice . ' berhasil diperbarui ke Packing.'
        );

    } catch (\Exception $e) {
        // 4. Tangkap Error dari Database (Trigger/SP)
        $pesan = $e->getMessage();

        // Bersihkan pesan error SQL bawaan agar lebih manusiawi
        if (str_contains($pesan, 'GAGAL:')) {
            // Mengambil teks setelah kata "GAGAL:" (dari Trigger)
            $pesan = substr($pesan, strpos($pesan, 'GAGAL:'));
        } elseif (str_contains($pesan, 'Security Alert:')) {
            $pesan = substr($pesan, strpos($pesan, 'Security Alert:'));
        }

        return redirect()->back()->with('error', $pesan);
    }
}

}

