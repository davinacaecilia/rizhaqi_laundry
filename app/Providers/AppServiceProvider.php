<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Log;       
use App\Models\Transaksi; 

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('partial.navbar', function ($view) {
            $user = Auth::user();
            $notifData = [];
            $notifCount = 0;
            $notifType = ''; 

            if ($user) {
                if ($user->role === 'owner') {
                    // OWNER: Log Aktivitas
                    // PERBAIKAN: Kita ambil 20 data terakhir (biar listnya panjang ada scroll)
                    $notifData = Log::with('user')
                        ->orderBy('waktu', 'desc')
                        ->limit(20) // Naikkan limit dari 5 ke 20
                        ->get();
                    
                    $notifType = 'log';
                    
                    // PERBAIKAN SINKRONISASI:
                    // Hitung jumlah dari data yang diambil saja.
                    // Jadi kalau list ada 20, angka juga 20. Kalau list 3, angka 3.
                    $notifCount = $notifData->count();

                } elseif ($user->role === 'pegawai') {
                    // PEGAWAI: Tugas Setrika (Status 'dikeringkan')
                    $notifData = Transaksi::where('status_pesanan', 'disetrika')
                        ->orderBy('updated_at', 'desc')
                        ->get();
                        
                    $notifType = 'task';
                    
                    // Ini sudah sinkron karena menghitung hasil get()
                    $notifCount = $notifData->count();
                }
            }

            $view->with('notifData', $notifData)
                 ->with('notifCount', $notifCount)
                 ->with('notifType', $notifType);
        });
    }
}