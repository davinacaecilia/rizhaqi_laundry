<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = User::whereIn('role', ['pegawai', 'owner', 'admin'])
            // Menggunakan FIELD() untuk custom order
            ->orderByRaw("FIELD(role, 'owner', 'admin', 'pegawai')")
            ->get();
        return view('admin.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.pegawai.index')->with('error', 'Admin tidak diizinkan menambah data pegawai/owner.');
        }

        return view('admin.pegawai.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.pegawai.index')->with('error', 'Admin tidak diizinkan untuk menambah data pegawai/owner.');
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:pegawai,admin',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password), // Hash password
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai baru berhasil ditambahkan!');
    }
    public function show(string $id)
    {
        abort(404);
    }
    public function destroy(string $id)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.pegawai.index')->with('error', 'Admin tidak diizinkan untuk menghapus data pegawai/owner.');
        }

        $pegawai = User::findOrFail($id);

        if (auth()->id() == $pegawai->id_user) {
            return redirect()->route('admin.pegawai.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $pegawai->delete();

        return redirect()->route('admin.pegawai.index')->with('success', 'Pegawai berhasil dihapus!');
    }

    public function toggleStatus(string $id)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.pegawai.index')->with('error', 'Admin tidak diizinkan untuk mengubah status akun pegawai/owner.');
        }
        
        $pegawai = User::findOrFail($id);

        // Cek agar user tidak menonaktifkan dirinya sendiri
        if (auth()->id() == $pegawai->id_user) {
            return redirect()->route('admin.pegawai.index')->with('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');
        }

        $pegawai->is_active = !$pegawai->is_active;
        $pegawai->save();

        $status_text = $pegawai->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.pegawai.index')->with('success', 'Akun pegawai berhasil ' . $status_text . '!');
    }
}