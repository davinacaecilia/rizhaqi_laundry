<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    
    <title>Detail Transaksi - Rizhaqi Laundry</title>
    
    <style>
        /* Style Khusus Halaman Detail/Invoice */
        .detail-card {
            background: var(--primary-white);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-light);
            max-width: 800px;
            margin: 24px auto;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px dashed var(--border-light);
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .invoice-title h2 { color: var(--accent-blue); margin-bottom: 5px; }
        .invoice-title p { color: var(--text-secondary); font-size: 14px; }
        
        .invoice-info { text-align: right; }
        .invoice-info h3 { font-size: 18px; color: var(--text-primary); }
        .invoice-info span { display: block; font-size: 13px; color: var(--text-secondary); margin-top: 4px; }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .info-group h4 { font-size: 14px; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-group p { font-size: 16px; font-weight: 500; color: var(--text-primary); }
        .address-box { background: #f9f9f9; padding: 10px; border-radius: 6px; font-size: 14px; color: var(--text-secondary); }

        /* Table Rincian */
        .rincian-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .rincian-table th { text-align: left; padding: 12px; background: #f0f4f8; color: var(--text-secondary); font-size: 13px; font-weight: 600; }
        .rincian-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .rincian-table td.price { text-align: right; font-weight: 600; }
        
        /* Total Box */
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .total-box { width: 300px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .total-row.final { border-top: 2px solid var(--border-light); padding-top: 10px; font-size: 18px; font-weight: 700; color: var(--accent-blue); }

        /* Status Badges */
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; }
        .bg-green { background: #E8F5E9; color: #2E7D32; } /* Lunas/Siap */
        .bg-yellow { background: #FFF3E0; color: #EF6C00; } /* DP */
        .bg-blue { background: #E3F2FD; color: #1565C0; } /* Proses */

        .action-buttons { margin-top: 40px; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; }
        .btn-back { background: #e0e0e0; color: #424242; }
        .btn-print { background: var(--accent-blue); color: white; }
        .btn-print:hover { background: var(--accent-blue-hover); }

        /* =========================================
           SETTINGAN PRINT KHUSUS INVOICE
           ========================================= */
        @media print {
            /* 1. Sembunyikan Elemen Dashboard */
            #sidebar, nav, .head-title, .action-buttons { 
                display: none !important; 
            }

            /* 2. Reset Layout Utama */
            body, section, main, #content {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                left: 0 !important;
                overflow: visible !important;
                position: relative !important;
            }

            /* 3. Atur Tampilan Invoice Card agar Full Page */
            .detail-card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
            }

            /* 4. Paksa Cetak Warna Background (untuk badge status) */
            .badge, .address-box, .rincian-table th, div[style*="background"] {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 5. Sedikit penyesuaian font agar hitam pekat */
            h1, h2, h3, h4, p, span, td {
                color: #000 !important;
            }
            .invoice-title h2 {
                color: var(--accent-blue) !important; /* Kecuali judul toko */
            }
        }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <!-- Judul Halaman Dashboard (Akan hilang saat diprint) -->
            <div class="head-title">
                <div class="left">
                    <h1>Detail Transaksi</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Detail Invoice</a></li>
                    </ul>
                </div>
            </div>

            <div class="detail-card">
                <div class="invoice-header">
                    <div class="invoice-title">
                        <h2>Rizhaqi Laundry</h2>
                        <p>Jalan Taman Setia Budi Indah No. 49B<br>Medan Sunggal, Sumatera Utara</p>
                    </div>
                    <div class="invoice-info">
                        <h3>INVOICE #TRX-001</h3>
                        <span>Tgl Masuk: 24 Nov 2025</span>
                        <span>Est. Selesai: 26 Nov 2025</span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-group">
                        <h4>Pelanggan</h4>
                        <p>Budi Santoso</p>
                        <span style="font-size: 14px; color: var(--text-secondary);">0812-3456-7890</span>
                        <div class="address-box" style="margin-top: 8px;">
                            Jl. Merpati No. 1, Medan Sunggal
                        </div>
                    </div>
                    <div class="info-group" style="text-align: right;">
                        
                        <h4 style="margin-top: 20px;">Status Pembayaran</h4>
                        <span class="badge bg-green">LUNAS</span>
                    </div>
                </div>

                <h4 style="margin-bottom: 10px; color: var(--text-secondary);">Rincian Layanan</h4>
                <table class="rincian-table">
                    <thead>
                        <tr>
                            <th>Deskripsi Layanan</th>
                            <th>Harga Satuan</th>
                            <th>Qty / Berat</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Cuci Kering Setrika Pakaian</strong><br>
                                <span style="font-size: 12px; color: #888;">Layanan Reguler (Kiloan)</span>
                            </td>
                            <td>Rp 10.000</td>
                            <td>4.5 Kg</td>
                            <td class="price">Rp 45.000</td>
                        </tr>

                        <tr>
                            <td>
                                Add On: Plastik
                            </td>
                            <td>Rp 3.000</td>
                            <td>1 Pcs</td>
                            <td class="price">Rp 3.000</td>
                        </tr>
                    </tbody>
                </table>

                <div style="background: #fff3e0; padding: 15px; border-radius: 8px; border: 1px solid #ffe0b2; margin-bottom: 20px;">
                    <h4 style="font-size: 13px; color: #ef6c00; margin-bottom: 8px; font-weight: 700;">RINCIAN PAKAIAN (INVENTARIS):</h4>
                    <p style="font-size: 14px; margin: 0; color: #333;">
                        3 Kemeja, 2 Kaos, 5 Celana Panjang, 1 Jilbab.
                    </p>
                </div>

                <div class="total-section">
                    <div class="total-box">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>Rp 48.000</span>
                        </div>
                        <div class="total-row final">
                            <span>Total Tagihan</span>
                            <span>Rp 48.000</span>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('admin.transaksi.index') }}" class="btn btn-back">
                        <i class='bx bx-arrow-back'></i> Kembali
                    </a>
                    <button class="btn btn-print" onclick="window.print()">
                        <i class='bx bx-printer'></i> Cetak Invoice
                    </button>
                </div>

            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>
</html>