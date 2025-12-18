<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function index()
    {

        $pelanggan = Pelanggan::latest()->get();
        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('admin.pelanggan.create');
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'nama' => 'required|string|max:100',
            'telepon' => 'required|numeric', // Cukup numeric, panjangnya database yg atur
            'alamat' => 'nullable|string'
        ]);

        // Create (UUID otomatis digenerate oleh Model)
        Pelanggan::create($request->all());

        return redirect()->route('admin.pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('admin.pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100',
            'telepon' => 'required|numeric',
            'alamat' => 'nullable|string'
        ]);

        $pelanggan->update($request->all());

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan diperbarui');
    }

    public function destroy($id)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.pelanggan.index')->with('error', 'Admin tidak diizinkan untuk menghapus data pelanggan.');
        }

        $pelanggan = Pelanggan::find($id);

    // 2. CEK APAKAH DATA ADA
    if ($pelanggan) {

        // 3. CEK APAKAH PELANGGAN PUNYA TRANSAKSI?
        // Penting: Pelanggan yang sudah pernah transaksi sebaiknya TIDAK dihapus
        // karena akan menghilangkan riwayat laporan keuangan.
        if ($pelanggan->transaksi()->exists()) {
            return back()->with('error', 'Gagal! Pelanggan tidak bisa dihapus karena memiliki riwayat transaksi.');
        }

        // 4. EKSEKUSI HAPUS
        try {
            $pelanggan->delete();
            return back()->with('success', 'Data pelanggan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat menghapus data.');
        }

    } else {
        return back()->with('error', 'Data pelanggan tidak ditemukan.');
    }
    }
}