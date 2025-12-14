<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\Log;
=======
use App\Models\Log; 
>>>>>>> c2629edac7ee37008c45e095dd5164373cc0e0c0
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /**
     * Menampilkan halaman log aktivitas
     */
<<<<<<< HEAD
    public function index()
    {
        // Ambil semua data log, urutkan dari terbaru
        $logs = Log::with('user')
            ->orderBy('waktu', 'desc')
            ->get();

        // kirim ke view
        return view('admin.log-aktivitas', compact('logs'));
    }
}
=======
    public function index(Request $request)
    {
        // 1. Mulai Query
        $query = Log::with('user')->orderBy('waktu', 'desc');

        // 2. Tambahkan Filter Tanggal
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('waktu', $request->date);
        }

        $logs = $query->get();

        // 5. Kirim ke View
        return view('admin.log-aktivitas', compact('logs'));
    }
}
>>>>>>> c2629edac7ee37008c45e095dd5164373cc0e0c0
