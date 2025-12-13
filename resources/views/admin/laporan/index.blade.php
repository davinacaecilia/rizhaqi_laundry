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

        /* --- FILTER CARD --- */
        .filter-card { 
            background: var(--primary-white); 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            border: 1px solid var(--border-light); 
            margin-bottom: 24px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
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
            margin-left: 0; 
        }
        
        /* TOMBOL EXCEL */
        .btn-excel { 
            background: #217346; 
            color: white; 
            padding: 8px 20px; 
            border-radius: 6px; 
            border:none; 
            cursor: pointer; 
            margin-left: 0; 
        }
        
        .btn-excel:hover {
            background: #1a5c37;
        }

        /* --- SEARCH BAR --- */
        .table-search-wrapper { 
            position: relative; 
            display: flex; 
            align-items: center; 
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
        
        /* ========================================= */
        /* ELEMEN KHUSUS PRINT (hidden di layar) */
        /* ========================================= */
        .print-header {
            display: none;
        }
        
        .print-footer {
            display: none;
        }
        
        /* ========================================= */
        /* CSS KHUSUS UNTUK CETAK */
        /* ========================================= */
        @media print {
            /* Sembunyikan elemen yang tidak perlu */
            #sidebar, nav, .tabs-box, .filter-card, .breadcrumb, 
            .btn-print, .head-title .btn-download, .bx-search-toggle,
            .head-title {
                display: none !important;
            }
            
            /* HILANGKAN SCROLLBAR */
            body, html {
                overflow: visible !important;
                height: auto !important;
            }
            
            * {
                overflow: visible !important;
            }
            
            /* Reset layout untuk print */
            body {
                margin: 0;
                padding: 0;
                background: white;
                font-size: 12pt;
            }
            
            #content {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                left: 0 !important;
                position: relative !important;
                overflow: visible !important;
            }
            
            main {
                padding: 20mm 15mm !important;
                overflow: visible !important;
            }
            
            /* ===== HEADER LAPORAN ===== */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 3px double #000;
            }
            
            .print-header .company-name {
                font-size: 20pt;
                font-weight: bold;
                color: #000;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            
            .print-header .company-info {
                font-size: 10pt;
                color: #333;
                margin-bottom: 3px;
            }
            
            .print-header .report-title {
                font-size: 16pt;
                font-weight: bold;
                margin-top: 15px;
                margin-bottom: 5px;
                text-transform: uppercase;
                color: #000;
            }
            
            .print-header .report-period {
                font-size: 11pt;
                color: #555;
                font-style: italic;
            }
            
            /* Tampilkan hanya tab aktif */
            .tab-content {
                display: none !important;
            }
            
            .tab-content.active {
                display: block !important;
            }
            
            /* Layout untuk print */
            .report-layout {
                display: block !important;
                page-break-inside: avoid;
            }
            
            .report-section {
                border: 2px solid #000 !important;
                margin-bottom: 25px !important;
                padding: 15px !important;
                page-break-inside: avoid;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            
            .report-section h3 {
                font-size: 14pt !important;
                font-weight: bold !important;
                color: #000 !important;
                border-bottom: 2px solid #000 !important;
                padding-bottom: 8px !important;
                margin-bottom: 15px !important;
            }
            
            /* ===== TABLE STYLING ===== */
            table {
                border-collapse: collapse !important;
                width: 100% !important;
                font-size: 10pt !important;
            }
            
            th {
                background: #e0e0e0 !important;
                color: #000 !important;
                font-weight: bold !important;
                border: 1px solid #000 !important;
                padding: 8px 6px !important;
                text-align: left !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            td {
                border: 1px solid #666 !important;
                padding: 6px !important;
                color: #000 !important;
            }
            
            /* Total row styling */
            .row-total {
                background: #d0d0d0 !important;
                font-weight: bold !important;
                border: 2px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .row-total td {
                padding: 10px 6px !important;
                font-size: 11pt !important;
                font-weight: bold !important;
                border: 2px solid #000 !important;
            }
            
            /* Warna text untuk print (hitam semua untuk printer friendly) */
            .text-green {
                color: #000 !important;
            }
            
            .text-red {
                color: #000 !important;
            }
            
            .text-blue {
                color: #000 !important;
                font-weight: bold !important;
            }
            
            /* No data message */
            .no-data-msg {
                text-align: center !important;
                padding: 15px !important;
                font-style: italic !important;
                background: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* ===== FOOTER PRINT ===== */
            .print-footer {
                display: block !important;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #000;
                page-break-inside: avoid;
            }
            
            .signature-section {
                display: flex !important;
                justify-content: flex-end;
                margin-top: 60px;
                page-break-inside: avoid;
            }
            
            .signature-box {
                text-align: center;
                width: 200px;
            }
            
            .signature-line {
                border-top: 1px solid #000;
                margin-top: 80px;
                padding-top: 5px;
                font-weight: bold;
            }
            
            .print-date {
                text-align: right;
                font-size: 9pt;
                color: #000 !important;
                margin-top: 10px;
            }
            
            /* Page break control */
            .report-section {
                page-break-inside: avoid;
            }
            
            /* Untuk laporan bulanan */
            #tabelBulanan {
                page-break-inside: auto;
            }
            
            #tabelBulanan tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <!-- ===== HEADER PRINT (hanya muncul saat cetak) ===== -->
            <div class="print-header">
                <div class="company-name">RIZHAQI LAUNDRY</div>
                <div class="company-info">Jalan Taman Setia Budi Indah No. 49B</div>
                <div class="company-info">Medan Sunggal, Sumatera Utara</div>
                <div class="report-title" id="printReportTitle">LAPORAN KEUANGAN</div>
                <div class="report-period" id="printReportPeriod">Periode: -</div>
            </div>
            
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

            {{-- LOGIC TAB (PHP) --}}
            <div class="tabs-box">
                <button class="tab-btn {{ $activeTab == 'harian' ? 'active' : '' }}" onclick="openTab(event, 'tab-harian')" style="{{ $activeTab == 'harian' ? 'color: var(--accent-blue); border-bottom-color: var(--accent-blue);' : '' }}">Laporan Harian (Detail)</button>
                <button class="tab-btn {{ $activeTab == 'bulanan' ? 'active' : '' }}" onclick="openTab(event, 'tab-bulanan')" style="{{ $activeTab == 'bulanan' ? 'color: var(--accent-blue); border-bottom-color: var(--accent-blue);' : '' }}">Rekap Bulanan (Summary)</button>
            </div>

            <div id="tab-harian" class="tab-content {{ $activeTab == 'harian' ? 'active' : '' }}" style="display: {{ $activeTab == 'harian' ? 'block' : 'none' }};">
                <div class="filter-card">
                    <form action="{{ route('admin.laporan.index') }}" method="GET" class="filter-group" id="formHarian">
                        <input type="hidden" name="tab" value="harian">
                        <label>Lihat Tanggal:</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="document.getElementById('formHarian').submit()" id="laporanDate" style="cursor: pointer;">
                    </form>
                    
                    <button class="btn-excel" onclick="exportToExcel('harian')"><i class='bx bx-download'></i> Export Excel</button>
                    <button class="btn-print" onclick="window.print()"><i class='bx bx-printer'></i> Cetak</button>
                </div>

                <div class="report-layout">
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
                                @forelse($dataCucian as $item)
                                <tr class="data-row">
                                    <td>{{ $item->kode_invoice }}</td>
                                    <td>{{ $item->nama_pelanggan }}</td>
                                    <td class="tgl-row">{{ date('d-m-Y', strtotime($item->tgl_masuk)) }}</td>
                                    <td class="val-berat">{{ number_format($item->berat, 0, ',', '.') }} Kg</td>
                                    <td class="text-right val-total">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="no-data-msg">Tidak ada cucian masuk.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="row-total">
                                    <td colspan="3">TOTAL</td>
                                    <td id="totalBerat">{{ number_format($dataCucian->sum('berat'), 0, ',', '.') }} Kg</td>
                                    <td class="text-right text-blue" id="totalKiri">Rp {{ number_format($dataCucian->sum('total_harga'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

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
                                @forelse($arusKas as $kas)
                                <tr class="data-row">
                                    <td>{{ $kas->keterangan }}</td>
                                    <td class="tgl-row">{{ date('d-m-Y', strtotime($kas->tanggal)) }}</td>
                                    <td class="text-right {{ $kas->masuk > 0 ? 'text-green' : '' }} val-masuk">
                                        {{ $kas->masuk > 0 ? number_format($kas->masuk, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-right {{ $kas->keluar > 0 ? 'text-red' : '' }} val-keluar">
                                        {{ $kas->keluar > 0 ? number_format($kas->keluar, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="no-data-msg">Belum ada transaksi kas.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="row-total">
                                    <td colspan="2">SISA KAS (NET)</td>
                                    @php $sisaKas = $arusKas->sum('masuk') - $arusKas->sum('keluar'); @endphp
                                    <td colspan="2" class="text-right {{ $sisaKas >= 0 ? 'text-green' : 'text-red' }}" id="totalKanan">
                                        Rp {{ number_format($sisaKas, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tab-bulanan" class="tab-content {{ $activeTab == 'bulanan' ? 'active' : '' }}" style="display: {{ $activeTab == 'bulanan' ? 'block' : 'none' }};">
                <div class="filter-card">
                    <form action="{{ route('admin.laporan.index') }}" method="GET" style="display:flex; gap:10px; align-items:center; width:100%;">
                        <input type="hidden" name="tab" value="bulanan">
                        
                        <div class="filter-group">
                            <label>Bulan:</label>
                            <select name="bulan" id="filterBulan">
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}" {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Tahun:</label>
                            <select name="tahun" id="filterTahun" style="min-width: 100px;">
                                @for($y=date('Y'); $y>=2023; $y--)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button class="btn-filter" id="btnTampilkanBulan"><i class='bx bx-search-alt'></i> Tampilkan</button>
                        <button type="button" class="btn-excel" onclick="exportToExcel('bulanan')"><i class='bx bx-download'></i> Export Excel</button>
                        <button type="button" class="btn-print" onclick="window.print()" style="margin-left: auto;"><i class='bx bx-printer'></i> Cetak</button>
                    </form>
                </div>

                <div class="report-section">
                    <h3><i class='bx bx-calendar'></i> Rekapitulasi Pendapatan: {{ date('F Y', mktime(0, 0, 0, $bulan, 10, $tahun)) }}</h3>
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
                            @forelse($rekapBulanan as $rekap)
                            <tr class="row-bulan">
                                <td class="text-center">{{ date('d M', strtotime($rekap->tanggal)) }}</td>
                                <td class="text-right text-green val-masuk">{{ number_format($rekap->total_masuk, 0, ',', '.') }}</td>
                                <td class="text-right text-red val-keluar">{{ number_format($rekap->total_keluar, 0, ',', '.') }}</td>
                                <td class="text-right text-blue">{{ number_format($rekap->bersih, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="no-data-msg">Belum ada data rekapitulasi untuk bulan ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="row-total" style="font-size: 16px; background: #f0f0f0; border-top: 3px double #aaa;">
                                <td class="text-center">GRAND TOTAL</td>
                                <td class="text-right text-green" id="grandMasuk">Rp {{ number_format($rekapBulanan->sum('total_masuk'), 0, ',', '.') }}</td>
                                <td class="text-right text-red" id="grandKeluar">Rp {{ number_format($rekapBulanan->sum('total_keluar'), 0, ',', '.') }}</td>
                                <td class="text-right text-blue" id="grandBersih" style="font-size: 18px;">Rp {{ number_format($rekapBulanan->sum('bersih'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- ===== FOOTER PRINT (hanya muncul saat cetak) ===== -->
            <div class="print-footer">
                <div class="print-date">
                    Dicetak pada: <span id="printDateTime"></span>
                </div>
                <div class="signature-section">
                    <div class="signature-box" style="margin-left: auto; margin-right: 0;">
                        <div>Medan, <span id="printDateLocation"></span></div>
                        <div class="signature-line">Pemilik</div>
                    </div>
                </div>
            </div>

        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
    <script src="{{ asset('admin/script/print-laporan.js') }}"></script>
    
    <!-- SheetJS Library untuk Export Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</body>
</html>