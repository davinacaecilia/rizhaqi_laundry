<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    
    <title>Invoice #{{ $transaksi->kode_invoice }} - Rizhaqi Laundry</title>
    
    <style>
        /* ========================================= */
        /* STYLE TAMPILAN WEB (SCREEN) */
        /* ========================================= */
        .detail-card { 
            background: var(--primary-white); 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: var(--shadow-light); 
            border: 1px solid var(--border-light); 
            max-width: 800px; 
            margin: 24px auto; 
        }
        
        .web-invoice-header { display: flex; justify-content: space-between; border-bottom: 2px dashed var(--border-light); padding-bottom: 20px; margin-bottom: 20px; }
        .invoice-title h2 { color: var(--accent-blue); margin-bottom: 5px; }
        .invoice-title p { color: var(--text-secondary); font-size: 14px; }
        
        .invoice-info { text-align: right; }
        .invoice-info h3 { font-size: 18px; color: var(--text-primary); }
        .invoice-info span { display: block; font-size: 13px; color: var(--text-secondary); margin-top: 4px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .info-group h4 { font-size: 14px; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-group p { font-size: 16px; font-weight: 500; color: var(--text-primary); }
        .address-box { background: #f9f9f9; padding: 10px; border-radius: 6px; font-size: 14px; color: var(--text-secondary); }

        .rincian-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .rincian-table th { text-align: left; padding: 12px; background: #f0f4f8; color: var(--text-secondary); font-size: 13px; font-weight: 600; }
        .rincian-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .rincian-table td.price { text-align: right; font-weight: 600; }
        
        .total-section { display: flex; justify-content: flex-end; margin-top: 20px; }
        .total-box { width: 300px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .total-row.final { border-top: 2px solid var(--border-light); padding-top: 10px; font-size: 18px; font-weight: 700; color: var(--accent-blue); }

        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; }
        .bg-green { background: #E8F5E9; color: #2E7D32; }
        .bg-yellow { background: #FFF3E0; color: #EF6C00; }
        .bg-red { background: #FFEBEE; color: #C62828; }

        .action-buttons { margin-top: 40px; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; }
        .btn-back { background: #e0e0e0; color: #424242; }
        .btn-print { background: var(--accent-blue); color: white; }
        .btn-print:hover { background: var(--accent-blue-hover); }

        .print-header, .print-footer { display: none; }

        /* ========================================= */
        /* STYLE KHUSUS PRINT (PERBAIKAN MARGIN) */
        /* ========================================= */
        @media print {
            /* Kita set margin 0 di @page, tapi kita kasih padding di body */
            /* Ini trik supaya header tidak terpotong printer */
            @page {
                size: landscape; 
                margin: 0mm; 
            }

            body {
                background: white !important;
                /* Padding ini yang akan jadi margin aman, supaya header gak kepotong */
                padding: 10mm 15mm !important; 
                font-family: "Times New Roman", serif;
                font-size: 10pt;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
            }

            /* Sembunyikan elemen Web */
            #sidebar, nav, .head-title, .action-buttons, .breadcrumb, .btn, .web-invoice-header { 
                display: none !important; 
            }

            /* Reset Container */
            section, main, #content, .detail-card { 
                width: 100% !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                border: none !important; 
                box-shadow: none !important; 
                left: 0 !important;
                position: relative !important;
            }

            /* ===== HEADER CETAK ===== */
            .print-header {
                display: flex !important;
                justify-content: space-between;
                align-items: flex-start; /* Ubah jadi flex-start biar aman */
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
                margin-bottom: 15px;
                width: 100%;
            }
            
            .print-header .company-brand { flex: 1; }
            .print-header .company-brand h1 { 
                font-size: 20pt; 
                font-weight: 900; 
                margin: 0 0 5px 0; 
                text-transform: uppercase; 
                line-height: 1;
            }
            .print-header .company-brand p { font-size: 10pt; margin: 2px 0; }
            
            .print-header .invoice-tag { text-align: right; }
            .print-header .invoice-tag h2 { font-size: 16pt; margin: 0; text-decoration: underline; }

            /* Grid Info */
            .info-grid {
                display: flex !important;
                width: 100%;
                justify-content: space-between;
                margin-bottom: 15px;
                gap: 20px;
                border: none;
            }
            .info-group { flex: 1; }
            .info-group h4 { font-size: 9pt; font-weight: bold; text-decoration: underline; margin-bottom: 4px; color: #000 !important; text-transform: uppercase;}
            .info-group p { font-size: 11pt; font-weight: bold; margin: 0; color: #000 !important; }
            .info-group span { font-size: 10pt; color: #000 !important; }
            .address-box { display: none; } 

            /* Tabel */
            .rincian-table { 
                width: 100% !important;
                border: 2px solid #000 !important; 
                margin-bottom: 10px; 
                font-size: 10pt; 
            }
            .rincian-table th { 
                background: #ccc !important; 
                padding: 5px 8px !important; 
                border: 1px solid #000 !important;
                color: #000 !important;
                font-weight: bold;
            }
            .rincian-table td { 
                padding: 5px 8px !important; 
                border: 1px solid #000 !important;
                color: #000 !important;
            }

            /* Total */
            .total-section { margin-top: 5px; width: 100%; display: flex; justify-content: flex-end; }
            .total-box { width: 40%; }
            .total-row { display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 10pt; }
            .total-row.final { font-size: 12pt; border-top: 2px solid #000 !important; padding-top: 2px; margin-top: 2px; font-weight: bold; }

            /* Inventaris Box */
            .inventaris-box { border: 1px dashed #000 !important; padding: 5px !important; background: none !important; margin-bottom: 10px; }
            .inventaris-box h4 { color: #000 !important; margin: 0 0 2px 0 !important; }
            .inventaris-box p { color: #000 !important; margin: 0 !important; }

            /* Footer */
            .print-footer {
                display: flex !important;
                justify-content: space-between;
                margin-top: 30px;
                width: 100%;
            }
            .signature-box { width: 200px; text-align: center; font-size: 10pt; }
            .signature-line { margin-top: 50px; border-top: 1px solid #000; font-weight: bold; }
            
            .badge { border: none; padding: 0; color: #000 !important; background: none !important; font-weight: bold; }
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
                    <h1>Detail Transaksi</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Invoice #{{ $transaksi->kode_invoice }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="detail-card">
                
                <div class="print-header">
                    <div class="company-brand">
                        <h1>RIZHAQI LAUNDRY</h1>
                        <p>Jl. Taman Setia Budi Indah No. 49B, Medan</p>
                        <p>HP: 0812-3456-7890</p>
                    </div>
                    <div class="invoice-tag">
                        <h2>INVOICE</h2>
                        <p>#{{ $transaksi->kode_invoice }}</p>
                        <p style="font-size: 10pt;">Tgl: {{ date('d/m/Y', strtotime($transaksi->tgl_masuk)) }}</p>
                    </div>
                </div>

                <div class="web-invoice-header">
                    <div class="invoice-title">
                        <h2>Rizhaqi Laundry</h2>
                        <p>Jalan Taman Setia Budi Indah No. 49B<br>Medan Sunggal, Sumatera Utara</p>
                    </div>
                    <div class="invoice-info">
                        <h3>INVOICE #{{ $transaksi->kode_invoice }}</h3>
                        <span>Tgl Masuk: {{ date('d M Y H:i', strtotime($transaksi->tgl_masuk)) }}</span>
                        <span>Est. Selesai: {{ date('d M Y', strtotime($transaksi->tgl_selesai)) }}</span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-group">
                        <h4>Data Pelanggan</h4>
                        <p>{{ $transaksi->pelanggan->nama }}</p>
                        <span style="display:block;">{{ $transaksi->pelanggan->telepon }}</span>
                        @if($transaksi->pelanggan->alamat)
                            <div class="address-box" style="margin-top: 8px;">
                                {{ $transaksi->pelanggan->alamat }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="info-group" style="text-align: right;">
                        <h4 style="margin-top: 0;">Status Pembayaran</h4>
                        @if($transaksi->status_bayar == 'lunas')
                            <span class="badge bg-green">LUNAS</span>
                        @elseif($transaksi->status_bayar == 'dp')
                            <span class="badge bg-yellow">DP (Kurang: Rp {{ number_format($transaksi->sisa_tagihan, 0, ',', '.') }})</span>
                        @else
                            <span class="badge bg-red">BELUM BAYAR</span>
                        @endif
                    </div>
                </div>

                <h4 style="margin-bottom: 5px; color: var(--text-secondary); padding-bottom:5px;" class="layanan-title">Rincian Layanan</h4>
                <style> @media print { .layanan-title { display: none; } } </style>
                
                <table class="rincian-table">
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th>Harga</th>
                            <th style="width: 15%;">Qty</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi->detailTransaksi as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->layanan->nama_layanan }}</strong>
                                    <div style="font-size: 9pt; font-style:italic;">{{ $item->layanan->kategori }}</div>
                                </td>
                                <td>Rp {{ number_format($item->harga_saat_transaksi, 0, ',', '.') }}</td>
                                <td>
                                    {{ $item->jumlah }} {{ ucfirst($item->layanan->satuan ?? 'Pcs') }}
                                </td>
                                <td class="price">
                                    Rp {{ number_format($item->harga_saat_transaksi * $item->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($transaksi->inventaris->count() > 0)
                    <div style="background: #fff3e0; padding: 15px; border-radius: 8px; border: 1px solid #ffe0b2; margin-bottom: 10px;" class="inventaris-box">
                        <h4 style="font-size: 13px; color: #ef6c00; margin-bottom: 8px; font-weight: 700;">RINCIAN PAKAIAN:</h4>
                        <p style="font-size: 14px; margin: 0; color: #333;">
                            @foreach($transaksi->inventaris as $inv)
                                {{ $inv->jumlah }} {{ $inv->nama_barang }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    </div>
                @endif

                <div class="total-section">
                    <div class="total-box">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($transaksi->jumlah_bayar > 0)
                            <div class="total-row">
                                <span>Dibayar</span>
                                <span>Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="total-row final">
                            <span>Sisa Tagihan</span>
                            <span>Rp {{ number_format($transaksi->sisa_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                @if($transaksi->catatan)
                    <div class="notes-print" style="margin-top:10px; border:1px dashed #000; padding:5px; font-size:10pt;">
                        <strong>Catatan:</strong> {{ $transaksi->catatan }}
                    </div>
                @endif

                <div class="print-footer">
                    <div class="signature-box">
                        <p>Penerima,</p>
                        <div class="signature-line">{{ $transaksi->pelanggan->nama }}</div>
                    </div>
                    <div class="signature-box">
                        <p>Hormat Kami,</p>
                        <div class="signature-line">Rizhaqi Laundry</div>
                    </div>
                </div>
                <div class="print-footer" style="margin-top: 10px; font-size: 9pt; justify-content: center; font-style: italic;">
                    <p>Terima kasih atas kepercayaannya.</p>
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