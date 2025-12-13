<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log; 
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /**
     * Menampilkan halaman log aktivitas
     */
    public function index(Request $request)
    {
        // 1. Mulai Query
        $query = Log::with('user')->orderBy('waktu', 'desc');

        // 2. Tambahkan Filter Tanggal
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('waktu', $request->date);
        }

        // 3. PAGINATION (DIKOMEN DULU SEPERTI REQUEST ANDA)
        // $logs = $query->paginate(20);
        // $logs->appends($request->all());

        // 4. SHOW ALL (WAJIB DITAMBAHKAN SEBAGAI GANTINYA)
        // Kalau baris ini tidak ada, variable $logs tidak dikenali
        $logs = $query->get();

        // 5. Kirim ke View
        return view('admin.log-aktivitas', compact('logs'));
    }
}