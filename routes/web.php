<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
