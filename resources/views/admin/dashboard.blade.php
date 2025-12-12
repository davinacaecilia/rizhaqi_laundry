<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/chart.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/dashboard.css') }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <title>Dashboard - Rizhaqi Laundry</title>
    
</head>
<body>

    @include('partial.sidebar') 

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="dashboard-container">
                
                <header class="dashboard-header">
                    <div class="header-text">
                        <h1>Admin Dashboard - Rizhaqi Laundry</h1>
                        <p>Ringkasan Kinerja dan Status Bisnis</p>
                    </div>
                    
                    <a href="{{ route('admin.transaksi.create') }}" class="btn-shortcut">
                        <i class='bx bx-plus-circle'></i> 
                        Tambah Order Baru
                    </a>
                </header>

                <ul class="box-info">
                    <li>
                        <i class='bx bxs-folder-open'></i> 
                        <span class="text">
                            {{-- Panggil Variabel Total Transaksi --}}
                            <h3>{{ number_format($totalTransaksi) }}</h3>
                            <p>Total Transaksi</p>
                        </span>
                    </li>
                    
                    <li>
                        <i class='bx bx-calendar-check'></i>
                        <span class="text">
                            {{-- Panggil Variabel Transaksi Hari Ini --}}
                            <h3>{{ $transaksiHariIni }}</h3>
                            <p>Transaksi Hari Ini</p> 
                        </span>
                    </li>
                    
                    <li>
                        <i class='bx bx-package' ></i>
                        <span class="text">
                            {{-- Panggil Variabel Berat Hari Ini --}}
                            <h3>{{ $beratHariIni }} Kg</h3>
                            <p>Volume Cucian Hari Ini</p> 
                        </span>
                    </li>
                </ul>
                
                <div class="charts-container">
                    
                    <div class="chart-card">
                        <div class="chart-header">
                            <h2>Total Berat Cucian Masuk (Bulanan)</h2>
                            <p>Monitoring peningkatan volume</p>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="museumChart"></canvas>
                        </div>
                    </div>
                    
                    {{-- TABEL LOG AKTIVITAS (PENGGANTI CHART DONAT) --}}
                    <div class="chart-card" style="overflow: hidden;"> 
                        <div class="chart-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <h2>Aktivitas Terbaru</h2>
                                <p>5 Log aktivitas sistem terakhir</p>
                            </div>
                            <a href="{{ route('admin.log.index') }}" style="font-size: 12px; color: var(--accent-blue); text-decoration: none;">
                                Lihat Semua <i class='bx bx-right-arrow-alt'></i>
                            </a>
                        </div>
                        
                        <div class="recent-activity-table" style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <thead>
                                    <tr style="text-align: left; border-bottom: 1px solid #eee;">
                                        <th style="padding: 8px; color: #888; font-weight: 600;">User</th>
                                        <th style="padding: 8px; color: #888; font-weight: 600;">Aksi</th>
                                        <th style="padding: 8px; color: #888; font-weight: 600;">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentLogs as $log)
                                        <tr style="border-bottom: 1px solid #f9f9f9;">
                                            <td style="padding: 10px 8px;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    {{-- Avatar Bulat --}}
                                                    <div style="width: 24px; height: 24px; background: #e0f2f1; color: #00695c; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                                                        {{-- Ambil huruf depan nama user (misal: Admin -> A, Pegawai -> P) --}}
                                                        {{ substr($log->user->nama ?? 'T', 0, 1) }}
                                                    </div>
                                                    
                                                    {{-- Nama User --}}
                                                    <span style="font-weight: 500;">
                                                        {{ $log->user->nama ?? 'Tidak diketahui' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td style="padding: 10px 8px;">
                                                <span style="display: block; color: #333;">{{ $log->aksi }}</span>
                                                <small style="color: #999; font-size: 11px;">
                                                    {{ Str::limit($log->keterangan, 30) }} 
                                                </small>
                                            </td>
                                            <td style="padding: 10px 8px; color: #666; white-space: nowrap;">
                                                {{-- WAKTU BAHASA INDONESIA --}}
                                                {{ \Carbon\Carbon::parse($log->waktu)->locale('id')->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" style="text-align: center; padding: 20px; color: #999;">
                                                Belum ada aktivitas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/chart.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>
</html>