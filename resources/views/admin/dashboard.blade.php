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
                            <h3>1,245</h3>
                            <p>Total Transaksi</p>
                        </span>
                    </li>
                    
                    <li>
                        <i class='bx bx-calendar-check'></i>
                        <span class="text">
                            <h3>24</h3>
                            <p>Transaksi Hari Ini</p> 
                        </span>
                    </li>
                    
                    <li>
                        <i class='bx bx-package' ></i>
                        <span class="text">
                            <h3>215 Kg</h3>
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
                    
                    <div class="chart-card">
                        <div class="chart-header">
                            <h2>Distribusi Jenis Layanan</h2>
                            <p>Persentase Cuci-Setrika vs Satuan</p>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="mediumChart"></canvas>
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