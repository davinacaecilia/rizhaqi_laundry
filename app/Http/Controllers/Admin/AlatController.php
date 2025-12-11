<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alat;

class AlatController extends Controller
{
    public function index()
    {
        $alat = Alat::all();

        return view('admin.alat.index', compact('alat'));
    }
    public function create()
    {
        return view('admin.alat.create');
    }
    public function stok()
    {
        return view('admin.alat.stok');
    }

    public function store(Request $request)
    {
        // validasi Input
        $request->validate([
            'nama_alat' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'tanggal_maintenance' => 'nullable|date',
        ]);

        // simpan Data
        Alat::create([
            'nama_alat' => $request->nama_alat,
            'jumlah' => $request->jumlah,
            'tgl_maintenance_terakhir' => $request->tanggal_maintenance,
        ]);

        return redirect('admin/alat')->with('success', 'Data alat berhasil ditambahkan!');
    }
    public function show(string $id)
    {
    }
    public function edit($id)
    {
        // ambil data alat berdasarkan id_alat
        $alat = Alat::findOrFail($id);

        return view('admin.alat.edit', compact('alat'));
    }
    public function update(Request $request, string $id)
    {
        // validasi Input
        $request->validate([
            'nama_alat' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'tanggal_maintenance' => 'nullable|date',
        ]);

        // cari data alat dan update
        $alat = Alat::findOrFail($id);
        $alat->update([
            'nama_alat' => $request->nama_alat,
            'jumlah' => $request->jumlah,
            'tgl_maintenance_terakhir' => $request->tanggal_maintenance,
        ]);

        return redirect('admin/alat')->with('success', 'Data alat berhasil diperbarui!');
    }
    public function destroy(string $id)
    {
        Alat::destroy($id);
        return redirect('admin/alat')->with('success', 'Data alat berhasil dihapus!');
    }
}