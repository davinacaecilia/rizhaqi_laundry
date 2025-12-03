<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;

class PegawaiDashboardController extends Controller
{
    public function index()
    {
        return view('pegawai.dashboard');
    }
}
