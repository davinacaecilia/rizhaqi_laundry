<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    
    <title>Laporan Keuangan - Rizhaqi Laundry</title>
    
    <style>
        /* --- STYLE UMUM --- */
        .head-title { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .head-title .left h1 { font-size: 28px; font-weight: 600; color: var(--text-primary); }

        /* --- TABS --- */
        .tabs-box { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-light); }
        .tab-btn { padding: 10px 20px; background: none; border: none; cursor: pointer; font-size: 14px; font-weight: 600; color: var(--text-secondary); border-bottom: 3px solid transparent; transition: 0.2s; }
        .tab-btn.active { color: var(--accent-blue); border-bottom-color: var(--accent-blue); }
        .tab-btn:hover { color: var(--accent-blue); }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }

        /* --- FILTER CARD (MODIFIKASI POSISI) --- */
        .filter-card { 
            background: var(--primary-white); 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            border: 1px solid var(--border-light); 
            margin-bottom: 24px; 
            display: flex; 
            align-items: center; 
            gap: 10px; /* Jarak antar elemen */
            flex-wrap: wrap; 
        }
        
        .filter-group { display: flex; align-items: center; gap: 10px; }
        .filter-group label { font-size: 14px; font-weight: 600; margin-right: 5px; }
        .filter-group input, .filter-group select { padding: 8px; border: 1px solid #ddd; border-radius: 6px; outline: none; cursor: pointer; }
        
        .btn-filter { background: var(--accent-blue); color: white; padding: 8px 20px; border-radius: 6px; border:none; cursor: pointer; }
        
        /* TOMBOL PRINT */
        .btn-print { 
            background: var(--accent-orange); 
            color: white; 
            padding: 8px 20px; 
            border-radius: 6px; 
            border:none; 
            cursor: pointer; 
            /* Margin-left auto dihapus agar nempel sama search */
            margin-left: 0; 
        }

        /* --- SEARCH BAR (DORONG KE KANAN) --- */
        .table-search-wrapper { 
            position: relative; 
            display: flex; 
            align-items: center; 
            /* INI KUNCINYA: Dorong Search & Print ke kanan mentok */
            margin-left: auto; 
        }
        
        .table-search-input { width: 0; padding: 0; border: none; margin-left: 0; background: transparent; transition: width 0.3s ease, padding 0.3s ease; opacity: 0; pointer-events: none; height: 40px; border-radius: 20px; }
        .table-search-input.show { width: 200px; padding: 6px 12px; border: 1px solid var(--border-light); margin-right: 10px; opacity: 1; pointer-events: auto; background: var(--surface-white); }
        .table-search-input:focus { border-color: var(--accent-blue); outline: none; }
        .bx-search-toggle { cursor: pointer; font-size: 20px; color: #888; padding: 5px; }

        /* --- LAYOUT & TABLE --- */
        .report-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 768px) { .report-layout { grid-template-columns: 1fr; } }
        .report-section { background: white; border-radius: 10px; border: 1px solid var(--border-light); padding: 20px; height: fit-content;}
        .report-section h3 { font-size: 16px; font-weight: 600; margin-bottom: 15px; color: var(--accent-blue); border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { font-weight: 600; background: #f9f9f9; color: #555; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #2E7D32; font-weight: 600; }
        .text-red { color: #C62828; font-weight: 600; }
        .text-blue { color: #1565C0; font-weight: 600; }
        .row-total { background-color: #E3F2FD; font-weight: bold; border-top: 2px solid #90CAF9; }
        .row-total td { padding: 15px 10px; font-size: 14px; }
        .no-data-msg { text-align: center; color: #999; padding: 20px; font-style: italic; background: #fafafa; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        @media print {
            #sidebar, nav, .tabs-box, .filter-card, .breadcrumb, .btn-print, .head-title .btn-download { display: none !important; }
            body, section, main, #content { width: 100% !important; margin: 0 !important; padding: 0 !important; left: 0 !important; position: relative !important; box-shadow: none !important; overflow: visible !important; }
            .report-section { border: 1px solid #000 !important; margin-bottom: 20px !important; page-break-inside: avoid; }
            .tab-content { display: none !important; }
            .tab-content.active { display: block !important; }
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
                    <h1>Laporan Keuangan</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Laporan</a></li>
                    </ul>
                </div>
            </div>

            <div class="tabs-box">
                <button class="tab-btn active" onclick="openTab(event, 'tab-harian')">Laporan Harian (Detail)</button>
                <button class="tab-btn" onclick="openTab(event, 'tab-bulanan')">Rekap Bulanan (Summary)</button>
            </div>

            <!-- === TAB HARIAN === -->
            <div id="tab-harian" class="tab-content active">
                <div class="filter-card">
                    <!-- 1. Filter Tanggal (Di Kiri) -->
                    <div class="filter-group">
                        <label>Lihat Tanggal:</label>
                        <input type="date" id="laporanDate" style="cursor: pointer;">
                    </div>

                    <!-- 2. Search Bar (Di Kanan - didorong margin-left: auto) -->
                    <div class="table-search-wrapper">
                        <input type="text" id="laporanSearch" class="table-search-input" placeholder="Cari data...">
                        <i class='bx bx-search bx-search-toggle' id="tableSearchIcon"></i>
                    </div>
                    
                    <!-- 3. Tombol Cetak (Di Kanan - sebelah search) -->
                    <button class="btn-print" onclick="window.print()"><i class='bx bx-printer'></i> Cetak</button>
                </div>

                <div class="report-layout">
                    <!-- Tabel Kiri (Pemasukan) -->
                    <div class="report-section table-container">
                        <h3><i class='bx bx-basket'></i> Laporan Cucian Masuk</h3>
                        <table class="data-table" id="tablePemasukan">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal</th>
                                    <th>Berat</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data Hari Ini --}}
                                <tr class="data-row"><td>TRX-001</td><td>Pak Rahmat</td><td class="tgl-row">{{ date('Y-m-d') }}</td><td class="val-berat">4.5 Kg</td><td class="text-right val-total">45000</td></tr>
                                <tr class="data-row"><td>TRX-002</td><td>Ani Wijaya</td><td class="tgl-row">{{ date('Y-m-d') }}</td><td class="val-berat">15.0 Kg</td><td class="text-right val-total">150000</td></tr>
                                {{-- Data Kemarin --}}
                                <tr class="data-row"><td>TRX-003</td><td>Citra Lestari</td><td class="tgl-row">{{ date('Y-m-d', strtotime('-1 days')) }}</td><td class="val-berat">2.0 Kg</td><td class="text-right val-total">20000</td></tr>
                                {{-- Data Bulan Lalu --}}
                                <tr class="data-row"><td>TRX-004</td><td>Hotel Indah</td><td class="tgl-row">2025-11-01</td><td class="val-berat">20.0 Kg</td><td class="text-right val-total">200000</td></tr>
                            </tbody>
                            <tfoot>
                                <tr class="row-total">
                                    <td colspan="3">TOTAL</td>
                                    <td id="totalBerat">0 Kg</td>
                                    <td class="text-right text-blue" id="totalKiri">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Tabel Kanan (Arus Kas) -->
                    <div class="report-section table-container">
                        <h3><i class='bx bx-wallet'></i> Arus Kas (Cashflow)</h3>
                        <table class="data-table" id="tableArusKas">
                            <thead>
                                <tr>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                    <th class="text-right">Masuk</th>
                                    <th class="text-right">Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data Hari Ini --}}
                                <tr class="data-row"><td>Pelunasan Rahmat</td><td class="tgl-row">{{ date('Y-m-d') }}</td><td class="text-right text-green val-masuk">45000</td><td class="text-right val-keluar">0</td></tr>
                                <tr class="data-row"><td>DP Ani Wijaya</td><td class="tgl-row">{{ date('Y-m-d') }}</td><td class="text-right text-green val-masuk">75000</td><td class="text-right val-keluar">0</td></tr>
                                {{-- Data Kemarin --}}
                                <tr class="data-row"><td>Beli Deterjen</td><td class="tgl-row">{{ date('Y-m-d', strtotime('-1 days')) }}</td><td class="text-right val-masuk">0</td><td class="text-right text-red val-keluar">50000</td></tr>
                            </tbody>
                            <tfoot>
                                <tr class="row-total">
                                    <td colspan="2">SISA KAS</td>
                                    <td colspan="2" class="text-right text-green" id="totalKanan">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- === TAB BULANAN === -->
            <div id="tab-bulanan" class="tab-content">
                <div class="filter-card">
                    <div class="filter-group">
                        <label>Bulan:</label>
                        <select id="filterBulan">
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11" selected>November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Tahun:</label>
                        <select id="filterTahun" style="min-width: 100px;">
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                    <button class="btn-filter" id="btnTampilkanBulan"><i class='bx bx-search-alt'></i> Tampilkan</button>
                    <button class="btn-print" onclick="window.print()"><i class='bx bx-printer'></i> Cetak</button>
                </div>

                <div class="report-section">
                    <h3><i class='bx bx-calendar'></i> Rekapitulasi Pendapatan</h3>
                    <table id="tabelBulanan">
                        <thead>
                            <tr>
                                <th class="text-center" width="15%">Tanggal</th>
                                <th class="text-right" width="25%">Total Pemasukan (Rp)</th>
                                <th class="text-right" width="25%">Total Pengeluaran (Rp)</th>
                                <th class="text-right" width="35%">Pendapatan Bersih (Rp)</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyBulanan">
                            <tr class="row-bulan" data-bulan="11"><td class="text-center">01 Nov</td><td class="text-right text-green val-masuk">450000</td><td class="text-right text-red val-keluar">50000</td><td class="text-right text-blue">400000</td></tr>
                            <tr class="row-bulan" data-bulan="11"><td class="text-center">02 Nov</td><td class="text-right text-green val-masuk">600000</td><td class="text-right text-red val-keluar">0</td><td class="text-right text-blue">600000</td></tr>
                            <tr class="row-bulan" data-bulan="11"><td class="text-center">03 Nov</td><td class="text-right text-green val-masuk">550000</td><td class="text-right text-red val-keluar">250000</td><td class="text-right text-blue">300000</td></tr>
                            <tr class="row-bulan" data-bulan="12" style="display:none;"><td class="text-center">01 Des</td><td class="text-right text-green val-masuk">800000</td><td class="text-right text-red val-keluar">100000</td><td class="text-right text-blue">700000</td></tr>
                        </tbody>
                        <tfoot>
                            <tr class="row-total" style="font-size: 16px; background: #f0f0f0; border-top: 3px double #aaa;">
                                <td class="text-center">GRAND TOTAL</td>
                                <td class="text-right text-green" id="grandMasuk">Rp 0</td>
                                <td class="text-right text-red" id="grandKeluar">Rp 0</td>
                                <td class="text-right text-blue" id="grandBersih" style="font-size: 18px;">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </main>
    </section>

    <!-- SCRIPT JS -->
    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
    <script src="{{ asset('admin/script/laporan.js') }}"></script>

</body>
</html>