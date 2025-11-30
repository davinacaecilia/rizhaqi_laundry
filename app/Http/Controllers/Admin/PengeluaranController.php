<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index()
    {
        // Nanti di sini ambil data real dari database
        // $pengeluaran = Pengeluaran::all();
        
        // Arahkan ke view yang sudah kita buat tadi
        return view('admin.pengeluaran.index');
    }

    // Menampilkan halaman form tambah pengeluaran (create)
    public function create()
    {
        // Arahkan ke view form tambah
        return view('admin.pengeluaran.create');
    }

    // Menyimpan data pengeluaran baru (store)
    public function store(Request $request)
    {
        // 1. Validasi data input (sesuai kolom database)
        // $request->validate([
        //     'tanggal' => 'required|date',
        //     'keterangan' => 'required|string',
        //     'jumlah' => 'required|numeric',
        //     'id_user' => 'required|exists:users,id_user',
        // ]);

        // 2. Simpan ke Database (Logic Backend nanti)
        // Pengeluaran::create($request->all());

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dicatat!');
    }

    public function edit($id)
    {
        return view('admin.pengeluaran.edit');
    }

    // Menyimpan perubahan data (Update)
    public function update(Request $request, $id)
    {
        // Logic update database
        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil dihapus!');
    }
}