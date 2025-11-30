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

        /* --- TABS NAVIGATION --- */
        .tabs-box { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-light); }
        .tab-btn {
            padding: 10px 20px; background: none; border: none; cursor: pointer;
            font-size: 14px; font-weight: 600; color: var(--text-secondary);
            border-bottom: 3px solid transparent; transition: 0.2s;
        }
        .tab-btn.active { color: var(--accent-blue); border-bottom-color: var(--accent-blue); }
        .tab-btn:hover { color: var(--accent-blue); }
        
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }

        /* --- FILTER CARD --- */
        .filter-card {
            background: var(--primary-white); padding: 20px; border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid var(--border-light);
            margin-bottom: 24px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap;
        }
        .filter-group label { font-size: 14px; font-weight: 600; margin-right: 5px; }
        .filter-group input, .filter-group select { padding: 8px; border: 1px solid #ddd; border-radius: 6px; outline: none; }
        
        .btn-filter { background: var(--accent-blue); color: white; padding: 8px 20px; border-radius: 6px; border:none; cursor: pointer; }
        .btn-print { background: var(--accent-orange); color: white; padding: 8px 20px; border-radius: 6px; border:none; cursor: pointer; margin-left: auto; }

        /* --- LAYOUT HARIAN (KIRI-KANAN) --- */
        .report-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 768px) { .report-layout { grid-template-columns: 1fr; } }

        .report-section { background: white; border-radius: 10px; border: 1px solid var(--border-light); padding: 20px; height: fit-content;}
        .report-section h3 { font-size: 16px; font-weight: 600; margin-bottom: 15px; color: var(--accent-blue); border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }

        /* --- TABLE STYLING --- */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { font-weight: 600; background: #f9f9f9; color: #555; }
        
        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #2E7D32; font-weight: 600; }
        .text-red { color: #C62828; font-weight: 600; }
        .text-blue { color: #1565C0; font-weight: 600; }
        
        /* TOTAL ROW */
        .row-total { background-color: #E3F2FD; font-weight: bold; border-top: 2px solid #90CAF9; }
        .row-total td { padding: 15px 10px; font-size: 14px; }

        /* --- EMPTY STATE STYLE (DISIMPAN JIKA PERLU) --- */
        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: var(--text-secondary);
            background-color: #fcfcfc;
            border-radius: 8px;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
            display: block;
        }
        .empty-state h4 {
            font-size: 18px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }
        .empty-state p {
            font-size: 13px;
            color: #999;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        
        @media print {
            #sidebar, nav, .tabs-box, .filter-card, .breadcrumb, .btn-print { display: none; }
            .dashboard-container { margin: 0; padding: 0; }
            .tab-content { display: block !important; margin-bottom: 50px; page-break-after: always; } 
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

            <div id="tab-harian" class="tab-content active">
                
                <div class="filter-card">
                    <div class="filter-group">
                        <label>Lihat Tanggal:</label>
                        <input type="date" value="{{ date('Y-m-d') }}">
                    </div>
                    <button class="btn-filter"><i class='bx bx-search-alt'></i> Lihat Data</button>
                    <button class="btn-print" onclick="window.print()"><i class='bx bx-printer'></i> Cetak</button>
                </div>

                <div class="report-layout">
                    <div class="report-section">
                        <h3><i class='bx bx-basket'></i> Laporan Cucian Masuk</h3>
                        <p style="font-size: 12px; color: #888; margin-bottom: 10px;">*Mencatat nilai transaksi berdasarkan nota (Accrual Basis)</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Berat</th>
                                    <th class="text-right">Total Tagihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TRX-001</td>
                                    <td>Pak Rahmat</td>
                                    <td>4.5 Kg</td>
                                    <td class="text-right">Rp 45.000</td>
                                </tr>
                                <tr>
                                    <td>TRX-002</td>
                                    <td>Ani Wijaya</td>
                                    <td>15.0 Kg</td>
                                    <td class="text-right">Rp 150.000</td>
                                </tr>
                                <tr>
                                    <td>TRX-003</td>
                                    <td>Citra Lestari</td>
                                    <td>-</td>
                                    <td class="text-right">Rp 20.000</td>
                                </tr>
                                <tr class="row-total">
                                    <td colspan="2">TOTAL HARI INI</td>
                                    <td>19.5 Kg</td>
                                    <td class="text-right text-blue">Rp 215.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="report-section">
                        <h3><i class='bx bx-wallet'></i> Arus Kas (Cashflow)</h3>
                        <p style="font-size: 12px; color: #888; margin-bottom: 10px;">*Mencatat uang fisik yang diterima/keluar hari ini (Cash Basis)</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Keterangan</th>
                                    <th class="text-right">Masuk (+)</th>
                                    <th class="text-right">Keluar (-)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pelunasan Pak Rahmat</td>
                                    <td class="text-right text-green">Rp 45.000</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>DP Ani Wijaya</td>
                                    <td class="text-right text-green">Rp 75.000</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>Beli Deterjen 5L</td>
                                    <td>-</td>
                                    <td class="text-right text-red">Rp 50.000</td>
                                </tr>
                                <tr class="row-total">
                                    <td>TOTAL KAS</td>
                                    <td class="text-right text-green">Rp 120.000</td>
                                    <td class="text-right text-red">Rp 50.000</td>
                                </tr>
                                <tr style="background: #2E7D32; color: white; font-weight: bold;">
                                    <td colspan="2">SISA UANG DI KASIR</td>
                                    <td class="text-right">Rp 70.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tab-bulanan" class="tab-content">
                
                <div class="filter-card">
                    <div class="filter-group">
                        <label>Bulan:</label>
                        <select>
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
                        <select style="min-width: 100px;">
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>

                    <button class="btn-filter"><i class='bx bx-search-alt'></i> Tampilkan</button>
                    <button class="btn-print" onclick="window.print()"><i class='bx bx-printer'></i> Cetak</button>
                </div>

                <div class="report-section">
                    <h3><i class='bx bx-calendar'></i> Rekapitulasi Pendapatan</h3>
                    <table>
                        <thead>
                            <tr>
                                <th class="text-center" width="15%">Tanggal</th>
                                <th class="text-right" width="25%">Total Pemasukan (Rp)</th>
                                <th class="text-right" width="25%">Total Pengeluaran (Rp)</th>
                                <th class="text-right" width="35%">Pendapatan Bersih (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">01 Nov</td>
                                <td class="text-right text-green">450.000</td>
                                <td class="text-right text-red">50.000</td>
                                <td class="text-right text-blue">400.000</td>
                            </tr>
                            <tr>
                                <td class="text-center">02 Nov</td>
                                <td class="text-right text-green">600.000</td>
                                <td class="text-right text-red">0</td>
                                <td class="text-right text-blue">600.000</td>
                            </tr>
                            <tr>
                                <td class="text-center">03 Nov</td>
                                <td class="text-right text-green">550.000</td>
                                <td class="text-right text-red">250.000</td>
                                <td class="text-right text-blue">300.000</td>
                            </tr>
                            <tr style="background-color: #fff8e1;">
                                <td class="text-center"><strong>Hari Ini</strong></td>
                                <td class="text-right text-green"><strong>120.000</strong></td>
                                <td class="text-right text-red"><strong>50.000</strong></td>
                                <td class="text-right text-blue"><strong>70.000</strong></td>
                            </tr>

                            <tr class="row-total" style="font-size: 16px; background: #f0f0f0; border-top: 3px double #aaa;">
                                <td class="text-center">GRAND TOTAL</td>
                                <td class="text-right text-green">Rp 1.720.000</td>
                                <td class="text-right text-red">Rp 350.000</td>
                                <td class="text-right text-blue" style="font-size: 18px;">Rp 1.370.000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

    <script>
        function openTab(evt, tabName) {
            // Sembunyikan semua konten
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }

            // Hapus class active dari semua tombol
            tablinks = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }

            // Tampilkan tab yang dipilih
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
    </script>

</body>
</html>