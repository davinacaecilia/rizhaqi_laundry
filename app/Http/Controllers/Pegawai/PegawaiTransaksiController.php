<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class PegawaiTransaksiController extends Controller
{
    public function index()
    {
        // Data dummy utk tampilan
        $transaksi = [
            ['id' => 1, 'nama' => 'Budi', 'status' => 'proses'],
            ['id' => 2, 'nama' => 'Ani', 'status' => 'selesai'],
        ];

        return view('pegawai.transaksi.index', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = [
            'id' => $id,
            'nama' => 'Dummy Pelanggan',
            'berat' => 5,
            'status' => 'proses'
        ];

        return view('pegawai.transaksi.show', compact('transaksi'));
    }

    public function status(Request $request)
{
    $query = Transaksi::with('pelanggan')
        ->whereIn('status_pesanan', ['disetrika', 'packing']);

    // Filter Tanggal
    if ($request->has('date') && $request->date != '') {
        $query->whereDate('tgl_masuk', $request->date);
    }

    // GANTI paginate() JADI get()
    $transaksi = $query->orderByRaw("FIELD(status_pesanan, 'disetrika', 'packing')")
                       ->orderBy('tgl_masuk', 'asc')
                       ->get(); // <--- PENTING: Pakai get() untuk ambil semua data

    return view('pegawai.transaksi.status', compact('transaksi'));
}

    public function updateStatus($id)
    {
        $trx = Transaksi::findOrFail($id);
        
        if($trx->status_pesanan == 'disetrika') {
            $trx->status_pesanan = 'packing';
            $trx->save();
            
            // Opsional: Tambah ke Log Pegawai disini
            
            return redirect()->back()->with('success', 'Pekerjaan selesai! Status berubah ke Packing.');
        }
        
        return redirect()->back()->with('error', 'Status tidak valid.');
    }
}
