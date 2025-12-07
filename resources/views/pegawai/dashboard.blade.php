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

<style>
    .charts-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        align-items: flex-start; /* card kanan tetap di atas */
    }

    /* Chart kiri */
    .chart-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 20px;
        flex: 1 1 60%;
    }

    /* Judul chart */
    .chart-card .chart-header h2 {
        font-size: 16px; /* lebih kecil */
        color: #333;
    }

    .chart-card .chart-header p {
        font-size: 14px;
        color: #666;
    }

    /* Wrapper kanan */
    .right-wrapper {
    flex: 1 1 35%;
    display: flex;
    flex-direction: column;
    gap: 0px;
}

    /* Card status */
    .status-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 20px;
    }

    .status-card .card-header h2 {
        font-size: 16px;
        color: #333;
        margin-bottom: 10px;
    }

    .status-card h3 {
        font-size: 28px;
        color: #27ae60;
        margin-bottom: 5px;
    }

    .status-card p {
        font-size: 14px;
        color: #555;
    }

    /* Card tabel */
    .table-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 20px;
        width: 100%;
    }

    .table-card .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px; /* jarak judul dan tabel */
    }

    .table-card .card-header h2 {
        font-size: 16px;
        color: #333;
    }

    .table-card table {
        width: 100%;
        border-collapse: collapse;
    }

    .table-card th, .table-card td {
        padding: 17px 10px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }

    .table-card th {
        background-color: #f5f5f5;
    }

    /* Tombol See All */
    .btn-see-all {
        text-decoration: none;
        background-color: #27ae60;
        color: #fff;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        transition: background-color 0.2s;
    }

    .btn-see-all:hover {
        background-color: #219150;
    }

    /* Responsif */
    @media screen and (max-width: 768px) {
        .charts-container {
            flex-direction: column;
        }

        .chart-card, .right-wrapper {
            flex: 1 1 100%;
        }

        .table-card .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }

        .box-info.single-card {
            padding: 0px;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        /* Card tabel preview */
        .table-card {
            flex: 1; /* stretch kebawah, memanjang */
            display: flex;
            flex-direction: column;
        }
</style>

    @include('layout.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="dashboard-container">

                <header class="dashboard-header">
                    <div class="header-text">
                        <h1>Pegawai Dashboard - Rizhaqi Laundry</h1>
                    </div>
                </header>

                <div class="charts-container">
                <!-- Chart Batang di kiri -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h2>Total Berat Cucian Selesai Bulan ini</h2>
                        <p>Monitoring peningkatan volume</p>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="museumChart"></canvas>
                    </div>
                </div>

                <!-- Wrapper kanan -->
                <div class="right-wrapper">
                    <!-- Card Total Cucian Hari Ini -->
                    <ul class="box-info single-card">
                        <li>
                            <i class='bx bxs-folder-open'></i>
                            <span class="text">
                                <h3>24</h3>
                                <p>Total Cucian Selesai Hari Ini</p>
                            </span>
                        </li>
                    </ul>

                    <!-- Card Preview Tabel Status Order -->
                    <div class="table-card">
                        <div class="card-header">
                            <h2>Preview Status Order</h2>
                            <a href="{{ route('pegawai.transaksi.index') }}" class="btn-see-all">See All</a>
                        </div>
                        <div class="card-body">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Kode Invoice</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Status Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>TRX-005</td>
                                        <td>Rizhaqi</td>
                                        <td>diproses</td>
                                    </tr>
                                    <tr>
                                        <td>TRX-009</td>
                                        <td>Budi</td>
                                        <td>selesai</td>
                                    </tr>
                                    <tr>
                                        <td>TRX-009</td>
                                        <td>Budi</td>
                                        <td>selesai</td>
                                    </tr>
                                    <tr>
                                        <td>TRX-009</td>
                                        <td>Budi</td>
                                        <td>selesai</td>
                                    </tr>
                                    <tr>
                                        <td>TRX-009</td>
                                        <td>Budi</td>
                                        <td>selesai</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

        // ===== Data chart batang =====
        const museumData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Total Berat (Kg)',
                data: [1500, 1650, 1400, 1800, 2000, 1950, 2100, 2300, 2200, 2500, 2600, 2750],
                backgroundColor: [
                    '#4ECDC4', '#45B7D1', '#96CEB4', '#FF6B6B',
                    '#FFEAA7', '#DDA0DD', '#98D8C8', '#F8B500',
                    '#4ECDC4', '#45B7D1', '#96CEB4', '#FF6B6B'
                ],
                borderWidth: 0,
                borderRadius: 8
            }]
        };

        // ===== Config chart batang =====
        const museumConfig = {
            type: 'bar',
            data: museumData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: { color: '#666' },
                        title: { display: true, text: 'Berat Cucian (Kg)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#666', maxRotation: 45 },
                        title: { display: true, text: 'Bulan' }
                    }
                }
            }
        };

        // ===== Render chart =====
        const museumCtx = document.getElementById('museumChart').getContext('2d');
        new Chart(museumCtx, museumConfig);
     });
</script>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>
