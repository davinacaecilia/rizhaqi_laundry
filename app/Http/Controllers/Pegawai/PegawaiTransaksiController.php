<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class PegawaiTransaksiController extends Controller
{
    public function index(Request $request)
    {
        // --- LOGIC FILTER (SEARCH + STATUS + TANGGAL) ---
        $query = Transaksi::with('pelanggan')
            // 1. Ambil semua kolom asli transaksi
            ->select('transaksi.*')

            // 2. PANGGIL FUNCTION DATABASE SEBAGAI KOLOM VIRTUAL
            // Kita namakan 'total_biaya' supaya di view tidak perlu ubah kodingan
            ->selectRaw('fn_hitung_total_transaksi(transaksi.id_transaksi) as total_biaya');

        // 1. Search (Invoice / Nama Pelanggan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_invoice', 'like', "%$search%")
                    ->orWhereHas('pelanggan', function ($p) use ($search) {
                        $p->where('nama', 'like', "%$search%");
                    });
            });
        }

        // 2. Filter Status Bayar
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }

        // 3. Filter Tanggal Masuk
        if ($request->filled('tanggal')) {
            $query->whereDate('tgl_masuk', $request->tanggal);
        }

        // Order & Paginate
        $transaksi = $query->orderBy('tgl_masuk', 'desc')->paginate(10);

        // Append query string (biar filter ga ilang pas ganti halaman)
        $transaksi->appends($request->all());

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan', 'pembayaran', 'inventaris'])
            // 1. Ambil kolom asli
            ->select('transaksi.*')

            // 2. AMBIL TOTAL BIAYA (Virtual Column)
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')

            // 3. AMBIL SISA TAGIHAN (Virtual Column)
            // Biar kita gak perlu hitung manual (total - bayar) di view
            ->selectRaw('fn_sisa_tagihan(id_transaksi) as sisa_tagihan')

            ->findOrFail($id);

        return view('admin.transaksi.show', compact('transaksi'));
    }

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
        if ($trx->status_pesanan == 'disetrika') {
            $trx->status_pesanan = 'packing';
            $trx->save();

            // Opsional: Tambah ke Log Pegawai disini

            return redirect()->back()->with('success', 'Status pesanan ' . $trx->kode_invoice . ' berhasil diperbarui ke Packing.');
        }

        return redirect()->back()->with('error', 'Status pesanan ' . $trx->kode_invoice . ' tidak dapat diubah (Status saat ini: ' . $trx->status_pesanan . ').');
    }
}

