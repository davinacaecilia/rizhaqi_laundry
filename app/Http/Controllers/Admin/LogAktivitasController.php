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
