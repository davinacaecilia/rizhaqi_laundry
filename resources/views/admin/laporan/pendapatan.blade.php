<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Laporan Pendapatan Harian - Rizhaqi Laundry Admin</title>
    
    <style>
        /* CSS yang sudah ada */
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container th, .table-container td { padding: 15px; border: 1px solid var(--border-light); text-align: left; font-size: 14px; color: var(--text-primary); }
        .table-container th { font-weight: 600; color: var(--text-secondary); font-family: var(--google-sans); background-color: var(--surface-white); }
        .table-container tbody tr:nth-child(even) { background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }
        
        /* Styling Khusus Laporan */
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
        /* Kotak Total Pendapatan */
        .report-summary {
            font-size: 18px;
            font-weight: 700;
            margin-top: 15px;
            padding: 20px;
            background-color: var(--accent-green);
            color: var(--primary-white);
            border-radius: 8px;
            box-shadow: var(--shadow-dark);
            text-align: center;
        }
        .total-row strong {
            font-size: 16px;
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
                    <h1>Laporan Pendapatan</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="#">Laporan Harian</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="{{ route('admin.laporan.pendapatan') }}">Pendapatan Harian</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="report-filter">
                <form action="#" method="GET" style="display: flex; gap: 15px; align-items: center;">
                    <label for="date_filter">Tanggal Laporan:</label>
                    <input type="date" id="date_filter" name="date" value="2025-11-07" required>
                    
                    <button type="submit">Tampilkan</button>
                    <button type="button" class="btn-print" onclick="alert('Cetak Laporan belum aktif!')" style="background-color: var(--text-tertiary);">
                        <i class='bx bx-printer'></i> Cetak
                    </button>
                </form>
            </div>
            
            <div class="report-summary">
                Total Pendapatan Kotor (Tunai) Tanggal 07 Nov 2025: **Rp 1.112.500** </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Detail Transaksi dengan Pemasukan</h3>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="padding: 10px; border: 1px solid #ccc;">No.</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">ID Transaksi</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Pelanggan</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Tgl Bayar</th>
                                    <th style="padding: 10px; border: 1px solid #ccc; text-align: right;">Total Biaya (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">1</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">B3420</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">SANTI</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">17-10-25</td>
                                    <td style="padding: 10px; border: 1px solid #ccc; text-align: right;">Rp 42.000</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">2</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">B3479</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">MASLO</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">17-10-25</td>
                                    <td style="padding: 10px; border: 1px solid #ccc; text-align: right;">Rp 17.500</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">3</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">B3481</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">RIRIK AREVO</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">17-10-25</td>
                                    <td style="padding: 10px; border: 1px solid #ccc; text-align: right;">Rp 33.500</td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="4" style="padding: 10px; border: 1px solid #ccc; text-align: right;">
                                        <strong>TOTAL PEMASUKAN TANGGAL INI</strong>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc; text-align: right; background-color: #e6f7ff;">
                                        <strong>Rp 93.000</strong> (Contoh)
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