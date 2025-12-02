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


Route::get('/', [UserController::class, 'home'])->name('home');
Route::get('/harga', [UserController::class, 'harga'])->name('harga');
Route::get('/status', [UserController::class, 'status'])->name('status');
Route::post('/status', [UserController::class, 'checkStatus'])->name('status.check');


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/transaksi/status', [TransaksiController::class, 'status'])->name('transaksi.status');
    Route::resource('transaksi', TransaksiController::class);

    Route::resource('pegawai', PegawaiController::class);

    Route::resource('pelanggan', PelangganController::class);

    Route::resource('layanan', LayananController::class);

    Route::get('/alat/stok', [AlatController::class, 'stok'])->name('alat.stok');
    Route::resource('alat', AlatController::class);

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::resource('pengeluaran', PengeluaranController::class);

});
