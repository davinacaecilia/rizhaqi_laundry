<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Ringkasan Transaksi - Rizhaqi Laundry Admin</title>
    
    <style>
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container th, .table-container td { padding: 15px; border: 1px solid var(--border-light); text-align: left; font-size: 14px; color: var(--text-primary); }
        .table-container th { font-weight: 600; color: var(--text-secondary); font-family: var(--google-sans); background-color: var(--surface-white); }
        .table-container tbody tr:nth-child(even) { background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }
        
        .report-filter {
            background: var(--primary-white);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-light);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .report-filter label, .report-filter input, .report-filter button {
            font-size: 14px;
        }
        .report-filter input[type="date"] {
            padding: 8px 10px;
            border: 1px solid var(--border-light);
            border-radius: 6px;
        }
        .report-filter button {
            padding: 10px 15px;
            background-color: var(--accent-blue);
            color: var(--primary-white);
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .report-summary {
            font-size: 16px;
            font-weight: 600;
            margin-top: 15px;
            padding: 15px;
            background-color: var(--surface-white);
            border-radius: 8px;
            border: 1px solid var(--border-light);
        }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Ringkasan Transaksi</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="#">Laporan Harian</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="{{ route('admin.laporan.transaksi') }}">Ringkasan Transaksi</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="report-filter">
                <form action="#" method="GET" style="display: flex; gap: 15px; align-items: center;">
                    <label for="date_start">Dari Tanggal:</label>
                    <input type="date" id="date_start" name="start" value="2025-11-01" required>
                    
                    <label for="date_end">Sampai Tanggal:</label>
                    <input type="date" id="date_end" name="end" value="2025-11-07" required>
                    
                    <button type="submit">Tampilkan</button>
                    <button type="button" class="btn-print" onclick="alert('Cetak Laporan belum aktif!')" style="background-color: var(--text-tertiary);">
                        <i class='bx bx-printer'></i> Cetak
                    </button>
                </form>
            </div>
            
            <div class="report-summary">
                Total Berat Cucian Terkumpul Periode Ini: **51.9 Kg** (Dummy Data)
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Daftar Pesanan</h3>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="padding: 10px; border: 1px solid #ccc;">No.</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">ID Transaksi</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Pelanggan</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Berat (Kg)</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Total Biaya (Rp)</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Status Terakhir</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Tgl Masuk</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Tgl Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">1</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">D3420</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">IRNA</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">4.2 Kg</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Rp 42.000</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Selesai</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">17-10-25</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">27-10-25</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">2</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">D3438</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">BU IRMA</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">5.8 Kg</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Rp 58.000</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Dicuci</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">18-10-25</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">-</td>
                                </tr>
                                
                                <tr>
                                    <td colspan="8" style="padding: 10px; border: 1px solid #ccc; text-align: center;">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
        </section>

    <div id="pagination" class="pagination-container"></div>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/pagination.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>