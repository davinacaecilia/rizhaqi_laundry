<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'home'])->name('home');
Route::get('/harga', [UserController::class, 'harga'])->name('harga');
Route::get('/status', [UserController::class, 'status'])->name('status');
Route::post('/status', [UserController::class, 'checkStatus'])->name('status.check');

