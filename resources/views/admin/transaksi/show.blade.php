<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    
    <title>Invoice #{{ $transaksi->kode_invoice }} - Rizhaqi Laundry</title>
    
    <style>
        /* CSS KHUSUS INVOICE */
        .detail-card { background: var(--primary-white); padding: 40px; border-radius: 12px; box-shadow: var(--shadow-light); border: 1px solid var(--border-light); max-width: 800px; margin: 24px auto; }
        
        .invoice-header { display: flex; justify-content: space-between; border-bottom: 2px dashed var(--border-light); padding-bottom: 20px; margin-bottom: 20px; }
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

        /* PRINT MODE */
        @media print {
            #sidebar, nav, .head-title, .action-buttons { display: none !important; }
            body, section, main, #content { background: white !important; margin: 0 !important; padding: 0 !important; width: 100% !important; left: 0 !important; }
            .detail-card { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; max-width: 100% !important; }
            .badge, .address-box, .rincian-table th, div[style*="background"] { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
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
                <div class="invoice-header">
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
                        <h4>Pelanggan</h4>
                        <p>{{ $transaksi->pelanggan->nama }}</p>
                        <span style="font-size: 14px; color: var(--text-secondary);">{{ $transaksi->pelanggan->telepon }}</span>
                        @if($transaksi->pelanggan->alamat)
                            <div class="address-box" style="margin-top: 8px;">
                                {{ $transaksi->pelanggan->alamat }}
                            </div>
                        @endif
                    </div>
                    <div class="info-group" style="text-align: right;">
                        <h4 style="margin-top: 20px;">Status Pembayaran</h4>
                        @if($transaksi->status_bayar == 'lunas')
                            <span class="badge bg-green">LUNAS</span>
                        @elseif($transaksi->status_bayar == 'dp')
                            <span class="badge bg-yellow">DP (Kurang: Rp {{ number_format($transaksi->sisa_tagihan, 0, ',', '.') }})</span>
                        @else
                            <span class="badge bg-red">BELUM BAYAR</span>
                        @endif
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
                        {{-- LOOPING DETAIL TRANSAKSI --}}
                        @foreach($transaksi->detailTransaksi as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->layanan->nama_layanan }}</strong><br>
                                    <span style="font-size: 12px; color: #888;">{{ $item->layanan->kategori }}</span>
                                </td>
                                <td>Rp {{ number_format($item->harga_saat_transaksi, 0, ',', '.') }}</td>
                                <td>
                                    {{ $item->jumlah }} 
                                    {{ ucfirst($item->layanan->satuan ?? 'Pcs') }}
                                </td>
                                <td class="price">
                                    Rp {{ number_format($transaksi->sisa_tagihan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- RINCIAN INVENTARIS (HANYA MUNCUL JIKA ADA) --}}
                @if($transaksi->inventaris->count() > 0)
                    <div style="background: #fff3e0; padding: 15px; border-radius: 8px; border: 1px solid #ffe0b2; margin-bottom: 20px;">
                        <h4 style="font-size: 13px; color: #ef6c00; margin-bottom: 8px; font-weight: 700;">RINCIAN PAKAIAN (INVENTARIS):</h4>
                        <p style="font-size: 14px; margin: 0; color: #333;">
                            @foreach($transaksi->inventaris as $inv)
                                {{ $inv->jumlah }} {{ $inv->nama_barang }}@if(!$loop->last), @endif
                            @endforeach.
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
                                <span>Sudah Dibayar (DP/Lunas)</span>
                                <span style="color: green;">- Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="total-row final">
                            <span>Sisa Tagihan</span>
                            <span>Rp {{ number_format($transaksi->sisa_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                @if($transaksi->catatan)
                    <div style="margin-top: 20px; font-size: 13px; color: #666; font-style: italic;">
                        <strong>Catatan:</strong> {{ $transaksi->catatan }}
                    </div>
                @endif

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