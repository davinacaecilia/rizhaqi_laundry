<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::orderBy('id_pelanggan', 'desc')->paginate(10);

        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('admin.pelanggan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'alamat'         => 'nullable|string',
            'no_telepon'     => 'required|numeric|digits_between:10,15',
        ], [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'no_telepon.required'     => 'Nomor telepon wajib diisi.',
            'no_telepon.numeric'      => 'Nomor telepon harus berupa angka.',
        ]);
        
        Pelanggan::create([
            'nama'    => $request->nama_pelanggan,
            'alamat'  => $request->alamat,
            'telepon' => $request->no_telepon,
        ]);

        // 3. Redirect kembali ke index dengan pesan sukses
        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil ditambahkan!');
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('admin.pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        // 1. Validasi
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'alamat'         => 'nullable|string',
            'no_telepon'     => 'required|numeric|digits_between:10,15',
        ]);
        
        $pelanggan->update([
            'nama'    => $request->nama_pelanggan,
            'alamat'  => $request->alamat,
            'telepon' => $request->no_telepon,
        ]);

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();

        return redirect()->route('admin.pelanggan.index')
            ->with('success', 'Data pelanggan berhasil dihapus!');
    }
}