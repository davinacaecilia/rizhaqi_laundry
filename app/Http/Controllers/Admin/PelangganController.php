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

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('admin.pelanggan.index')->with('success', 'Pelanggan dihapus');
    }
}