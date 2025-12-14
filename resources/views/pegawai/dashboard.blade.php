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
    /* --- CSS LAYOUT DASHBOARD --- */
    .charts-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        align-items: flex-start;
    }

    .chart-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 20px;
        flex: 1 1 60%;
    }

    .chart-card .chart-header h2 {
        font-size: 16px;
        color: #333;
    }

    .chart-card .chart-header p {
        font-size: 14px;
        color: #666;
    }

    .right-wrapper {
        flex: 1 1 35%;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .box-info.single-card {
        padding: 0px;
        margin-top: 0px;
        margin-bottom: 0px;
        display: grid;
        grid-template-columns: 1fr;
    }

    /* --- CSS TABLE CARD --- */
    .table-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 20px;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .table-card .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
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
        padding: 12px 10px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
        text-align: left;
    }

    .table-card th {
        background-color: #f9f9f9;
        color: #555;
        font-weight: 600;
    }

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

    /* --- CSS STATUS BADGE --- */
    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        display: inline-block;
    }

    .st-disetrika { background: #F3E5F5; color: #7B1FA2; border: 1px solid #E1BEE7; }
    .st-packing   { background: #E0F2F1; color: #00695C; border: 1px solid #B2DFDB; }
    .st-diproses  { background: #E3F2FD; color: #1565C0; border: 1px solid #BBDEFB; }
    .st-selesai   { background: #E8F5E9; color: #2E7D32; border: 1px solid #C8E6C9; }

    /* Responsif */
    @media screen and (max-width: 768px) {
        .charts-container {
            flex-direction: column;
        }
        .chart-card, .right-wrapper {
            flex: 1 1 100%;
        }
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
                    <div class="chart-card">
                        <div class="chart-header">
                            <h2>Kinerja Anda (Total Kg) Tahun Ini</h2>
                            <p>Akumulasi berat cucian yang Anda selesaikan per bulan</p>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="museumChart"></canvas>
                        </div>
                    </div>

                    <div class="right-wrapper">
                        <ul class="box-info single-card">
                            <li>
                                <i class='bx bxs-folder-open'></i>
                                <span class="text">
                                    {{-- Mengambil data dari LaporanHarianPegawai (Controller) --}}
                                    <h3>{{ $selesaiHariIni ?? 0 }}</h3>
                                    <p>Pekerjaan Selesai Hari Ini</p>
                                </span>
                            </li>
                        </ul>

                        <div class="table-card">
                            <div class="card-header">
                                <h2>Antrian Tugas</h2>
                                <a href="{{ route('pegawai.transaksi.status') }}" class="btn-see-all">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Pelanggan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $item)
                                            <tr>
                                                <td style="font-weight: bold; color: #333;">
                                                    {{ $item->kode_invoice }}
                                                </td>
                                                <td>
                                                    {{ optional($item->pelanggan)->nama ?? 'Umum' }}
                                                </td>
                                                <td>
                                                    @if($item->status_pesanan == 'disetrika')
                                                        <span class="status-badge st-disetrika">Disetrika</span>
                                                    @elseif($item->status_pesanan == 'packing')
                                                        <span class="status-badge st-packing">Packing</span>
                                                    @else
                                                        <span class="status-badge">{{ $item->status_pesanan }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" style="text-align: center; color: #888; padding: 20px;">
                                                    Tidak ada antrian saat ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartDataValues = @json($chartData); 

            const museumData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Total Berat (Kg)',
                    data: chartDataValues, // Data Dinamis
                    backgroundColor: [
                        '#4ECDC4', '#45B7D1', '#96CEB4', '#FF6B6B', '#FFEAA7', '#DDA0DD', 
                        '#98D8C8', '#F8B500', '#4ECDC4', '#45B7D1', '#96CEB4', '#FF6B6B'
                    ],
                    borderRadius: 5
                }]
            };

            const museumConfig = {
                type: 'bar',
                data: museumData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f0f0f0' },
                            title: { display: true, text: 'Berat (Kg)' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            };

            const ctx = document.getElementById('museumChart');
            if(ctx) {
                new Chart(ctx.getContext('2d'), museumConfig);
            }
        });
    </script>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>