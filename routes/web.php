<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\LaporanController;

Route::get('/', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->group(function () {

    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Manajemen Transaksi
    Route::get('/transaksi/status', [TransaksiController::class, 'status'])->name('transaksi.status');
    // 'Route::resource' otomatis membuatkan rute untuk:
    // index, create, store, show (untuk detail), edit, update, destroy
    Route::resource('transaksi', TransaksiController::class);

    // 3. Manajemen Pegawai
    Route::resource('pegawai', PegawaiController::class);

    // 4. Manajemen Pelanggan
    Route::resource('pelanggan', PelangganController::class);

    // 5. Manajemen Layanan
    Route::resource('layanan', LayananController::class);

    // 6. Manajemen Alat
    Route::get('/alat/stok', [AlatController::class, 'stok'])->name('alat.stok');
    Route::resource('alat', AlatController::class);

    // 7. Laporan Harian
    Route::get('/laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    Route::get('/laporan/pendapatan', [LaporanController::class, 'pendapatan'])->name('laporan.pendapatan');

});
