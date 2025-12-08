<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;

class LayananController extends Controller
{
    public function index()
    {
        $layanan = Layanan::orderBy('id_layanan', 'asc')->paginate(20);
        return view('admin.layanan.index', compact('layanan'));
    }

    public function create()
    {
        $kategori_list = Layanan::select('kategori')->distinct()->pluck('kategori');
        return view('admin.layanan.create', compact('kategori_list'));
    }

    public function store(Request $request)
    {
        // Logika Kategori (Sama)
        $kategori_final = ($request->kategori_mode == 'new') ? $request->kategori_baru : $request->kategori;
        $request->merge(['kategori_final' => $kategori_final]);

        // 1. Validasi
        $request->validate([
            'kategori_final' => 'required',
            'nama_layanan'   => 'required',
            'satuan'         => 'required',
            
            // Harga satuan boleh null JIKA is_flexible aktif
            'harga_satuan'   => 'nullable|required_if:is_flexible,0|numeric', 
            
            // Min Max wajib JIKA is_flexible aktif
            'harga_min'      => 'nullable|required_if:is_flexible,1|numeric|lt:harga_max',
            'harga_max'      => 'nullable|required_if:is_flexible,1|numeric|gt:harga_min',
        ], [
            'harga_min.lt' => 'Harga minimum harus lebih KECIL dari maksimum!',
        ]);

        // 2. Tentukan Harga Default untuk Database
        // Jika Flexible: Ambil harga_min. Jika Tetap: Ambil harga_satuan input.
        $harga_default = $request->has('is_flexible') ? $request->harga_min : $request->harga_satuan;

        // 3. Simpan
        Layanan::create([
            'kategori'     => $kategori_final,
            'nama_layanan' => $request->nama_layanan,
            'satuan'       => $request->satuan,
            'harga_satuan'        => $harga_default,
            'is_flexible'  => $request->has('is_flexible') ? 1 : 0,
            'harga_min'    => $request->harga_min,
            'harga_max'    => $request->harga_max,
        ]);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $layanan = Layanan::findOrFail($id);
        $kategori_list = Layanan::select('kategori')->distinct()->pluck('kategori');
        
        return view('admin.layanan.edit', compact('layanan', 'kategori_list'));
    }

    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id);

        // Logika Kategori sama seperti store
        $kategori_final = ($request->kategori_mode == 'new') ? $request->kategori_baru : $request->kategori;
        $request->merge(['kategori_final' => $kategori_final]);

        $request->validate([
            'kategori_final' => 'required|string',
            'nama_layanan'   => 'required|string',
            'satuan'         => 'required',
            'harga_satuan'   => 'required|numeric',
            'harga_min'      => 'nullable|required_if:is_flexible,1|numeric|lt:harga_max',
            'harga_max'      => 'nullable|required_if:is_flexible,1|numeric|gt:harga_min',
        ]);

        $layanan->update([
            'kategori'     => $kategori_final,
            'nama_layanan' => $request->nama_layanan,
            'satuan'       => $request->satuan,
            'harga'        => $request->harga_satuan,
            'is_flexible'  => $request->has('is_flexible') ? 1 : 0,
            'harga_min'    => $request->harga_min,
            'harga_max'    => $request->harga_max,
        ]);

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Layanan::findOrFail($id)->delete();
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus!');
    }
}
