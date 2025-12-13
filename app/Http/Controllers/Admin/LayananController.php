<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;

class LayananController extends Controller
{
    public function index()
    {
        $layanan = Layanan::orderBy('kategori', 'asc')
            ->orderBy('nama_layanan', 'asc')
            ->get();
        return view('admin.layanan.index', compact('layanan'));
    }

    public function create()
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.layanan.index')->with('error', 'Admin tidak diizinkan menambah layanan baru.');
        }

        // Ambil daftar kategori unik yang sudah ada di database
        // Gunanya buat isi dropdown "Pilih Kategori"
        $kategori_list = Layanan::select('kategori')
            ->distinct()
            ->orderBy('kategori', 'asc')
            ->pluck('kategori');

        return view('admin.layanan.create', compact('kategori_list'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.layanan.index')->with('error', 'Admin tidak diizinkan menambah layanan baru.');
        }

        // 1. Tentukan Kategori Mana yang Dipakai
        // Cek radio button 'kategori_mode' dari View
        $kategori_final = null;

        if ($request->kategori_mode == 'new') {
            $kategori_final = $request->kategori_baru;
        } else {
            $kategori_final = $request->kategori_existing;
        }

        // Masukkan ke request agar bisa divalidasi
        $request->merge(['kategori_final' => $kategori_final]);

        // 2. Validasi Input Umum
        $request->validate([
            'kategori_final' => 'required|string|max:100', // Kategori hasil olahan tadi
            'nama_layanan' => 'required|string|max:100',
            'satuan' => 'required|in:kg,pcs,m2',
        ]);

        // 3. Siapkan Array Data
        $data = [
            'kategori' => $kategori_final,
            'nama_layanan' => $request->nama_layanan,
            'satuan' => $request->satuan,
        ];

        // 4. Logika Mapping Harga (Rentang vs Tetap)
        if ($request->has('is_flexible')) {
            // --- JIKA MODE HARGA RENTANG ---
            $request->validate([
                'harga_min' => 'required|numeric|min:0',
                'harga_max' => 'required|numeric|gt:harga_min', // Max harus > Min
            ]);

            $data['harga_satuan'] = $request->harga_min; // Disimpan sebagai harga dasar
            $data['harga_maksimum'] = $request->harga_max; // Disimpan sebagai batas atas
        } else {
            // --- JIKA MODE HARGA TETAP ---
            $request->validate([
                'harga_tetap' => 'required|numeric|min:0',
            ]);

            $data['harga_satuan'] = $request->harga_tetap;
            $data['harga_maksimum'] = null; // Pastikan null agar dianggap harga tetap
        }

        // 5. Eksekusi Simpan
        Layanan::create($data);

        return redirect()->route('admin.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan');
    }

    public function edit($id)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.layanan.index')->with('error', 'Admin tidak diizinkan mengubah layanan.');
        }

        $layanan = Layanan::findOrFail($id);

        // Tetap kirim list kategori buat jaga-jaga kalau user mau ganti kategori
        $kategori_list = Layanan::select('kategori')->distinct()->pluck('kategori');

        return view('admin.layanan.edit', compact('layanan', 'kategori_list'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.layanan.index')->with('error', 'Admin tidak diizinkan mengubah layanan.');
        }

        $layanan = Layanan::findOrFail($id);

        // 1. Tentukan Kategori (Sama kayak Store)
        $kategori_final = null;
        if ($request->kategori_mode == 'new') {
            $kategori_final = $request->kategori_baru;
        } else {
            $kategori_final = $request->kategori_existing;
        }
        $request->merge(['kategori_final' => $kategori_final]);

        // 2. Validasi Umum
        $request->validate([
            'kategori_final' => 'required|string|max:100',
            'nama_layanan' => 'required|string|max:100',
            'satuan' => 'required|in:kg,pcs,m2',
        ]);

        // 3. Siapkan Data Update
        $data = [
            'kategori' => $kategori_final,
            'nama_layanan' => $request->nama_layanan,
            'satuan' => $request->satuan,
        ];

        // 4. Logika Mapping Harga Update
        if ($request->has('is_flexible')) {
            $request->validate([
                'harga_min' => 'required|numeric|min:0',
                'harga_max' => 'required|numeric|gt:harga_min',
            ]);
            $data['harga_satuan'] = $request->harga_min;
            $data['harga_maksimum'] = $request->harga_max;
        } else {
            $request->validate([
                'harga_tetap' => 'required|numeric|min:0',
            ]);
            $data['harga_satuan'] = $request->harga_tetap;
            $data['harga_maksimum'] = null;
        }

        // 5. Eksekusi Update
        $layanan->update($data);

        return redirect()->route('admin.layanan.index')
            ->with('success', 'Layanan berhasil diperbarui');
    }

    public function destroy($id)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.layanan.index')->with('error', 'Admin tidak diizinkan menghapus layanan.');
        }
        
        $layanan = Layanan::findOrFail($id);
        $layanan->delete();

        return redirect()->route('admin.layanan.index')
            ->with('success', 'Layanan dihapus');
    }
}
