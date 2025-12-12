<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
use App\Http\Controllers\Pegawai\PegawaiTransaksiController;
use App\Http\Controllers\Pegawai\PegawaiPelangganController;
use App\Http\Controllers\Pegawai\PegawaiCucianController;


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', [UserController::class, 'home'])->name('home');
Route::get('/harga', [UserController::class, 'harga'])->name('harga');
Route::get('/status', [UserController::class, 'status'])->name('status');
Route::post('/status', [UserController::class, 'checkStatus'])->name('status.check');


Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,owner'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transaksi/status', [TransaksiController::class, 'status'])->name('transaksi.status');
    // routes/web.php
    Route::put('/transaksi/{id}/update-status', [TransaksiController::class, 'updateStatus'])->name('transaksi.updateStatus');
    Route::resource('transaksi', TransaksiController::class);

    Route::post('pegawai/{pegawai}/status', [PegawaiController::class, 'toggleStatus'])->name('pegawai.status.toggle');
    Route::resource('pegawai', PegawaiController::class);

    Route::resource('pelanggan', PelangganController::class);

    Route::resource('layanan', LayananController::class);

    Route::get('/alat/stok', [AlatController::class, 'stok'])->name('alat.stok');
    Route::resource('alat', AlatController::class);

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::resource('pengeluaran', PengeluaranController::class);

    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])
    ->name('log.aktivitas');

});

Route::prefix('pegawai')->name('pegawai.')->middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])
        ->name('dashboard');

    // Transaksi Pegawai
    Route::get('/transaksi', [PegawaiTransaksiController::class, 'index'])
        ->name('transaksi.index');

    // Tambah order
    Route::get('/transaksi/create', [PegawaiTransaksiController::class, 'create'])
        ->name('transaksi.create');

    // List status
    Route::get('/transaksi/status', [PegawaiTransaksiController::class, 'status'])
        ->name('transaksi.status');

    // Detail transaksi
    Route::get('/transaksi/{id}', [PegawaiTransaksiController::class, 'show'])
        ->name('transaksi.show');

    // Pelanggan Pegawai
    Route::get('/pelanggan', [PegawaiPelangganController::class, 'index'])
        ->name('pelanggan.index');

    // Halaman list cucian
    Route::get('/cucian', [PegawaiCucianController::class, 'index'])
        ->name('cucian.index');
});
