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
        $query = Log::with('user')->orderBy('waktu', 'desc');
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('waktu', $request->date);
        }
        $logs = $query->get();

        return view('admin.log-aktivitas', compact('logs'));
    }
}
