<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index()
    {
        // Ambil data pengeluaran dan nama user yang mencatatnya
        $pengeluaran = Pengeluaran::with('user')
            ->orderBy('tanggal', 'desc')
            ->get();

        $total_pengeluaran = $pengeluaran->sum('jumlah');

        return view('admin.pengeluaran.index', compact('pengeluaran', 'total_pengeluaran'));
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
        // validasi data
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
        ]);

        // simpan
        Pengeluaran::create([
            'id_user' => Auth::id(), // ID pegawai yang sedang login
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dicatat!');
    }

    public function edit($id)
    {
        $data = Pengeluaran::findOrFail($id);

        return view('admin.pengeluaran.edit', compact('data'));
    }

    // Menyimpan perubahan data (Update)
    public function update(Request $request, $id)
    {
        // validasi
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
        ]);

        // update data
        $data = Pengeluaran::findOrFail($id);
        $data->update([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            // id_user tidak diubah karena itu adalah pencatat awal
        ]);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Pengeluaran::destroy($id);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil dihapus!');
    }
}