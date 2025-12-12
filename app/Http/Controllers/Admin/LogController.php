<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Atau use App\Models\Log;

class LogController extends Controller
{
    public function index()
    {
        // Ambil data log dari database (sesuai struktur tabel 'log' di sql kamu)
        // Pastikan model Log sudah ada, atau pakai DB builder
        $logs = DB::table('log')
                  ->join('users', 'log.id_user', '=', 'users.id_user')
                  ->select('log.*', 'users.nama as nama_user')
                  ->orderBy('waktu', 'desc')
                  ->paginate(10);

        return view('admin.log.index', compact('logs'));
    }
}