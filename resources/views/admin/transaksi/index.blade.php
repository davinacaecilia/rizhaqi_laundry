<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Manajemen Transaksi - Rizhaqi Laundry Admin</title>
    
    <style>
        /* CSS BAWAAN TEMPLATE */
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container thead tr { background-color: var(--surface-white); border-bottom: 1px solid var(--border-light); }
        
        .table-container th, .table-container td { padding: 15px; border: 1px solid var(--border-light); text-align: left; font-size: 14px; color: var(--text-primary); }
        .table-container th { font-weight: 600; color: var(--text-secondary); font-family: var(--google-sans); background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }

        .table-container .btn-action-group { display: flex; gap: 5px; flex-wrap: wrap; }
        .table-container .btn-action-group .btn-detail { padding: 6px 12px; font-size: 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; transition: all 0.2s ease; font-weight: 500; border: none; cursor: pointer; }
        
        /* WARNA TOMBOL */
        .btn-detail.show { background-color: var(--accent-green); color: white; }
        .btn-detail.edit { background-color: var(--accent-blue); color: white; }
        .btn-detail.delete { background-color: var(--accent-red); color: white; }
        .btn-detail.disabled { background-color: #e0e0e0 !important; color: #999 !important; cursor: not-allowed; pointer-events: none; }
        .btn-detail:hover { opacity: 0.9; }

        /* BADGES STATUS ORDER */
        .badge-status { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .st-siap { background: #E8F5E9; color: #2E7D32; }
        .st-dicuci { background: #E3F2FD; color: #1565C0; }
        .st-diterima { background: #E0E0E0; color: #424242; }
        .st-selesai { background: #1B5E20; color: #fff; }
        .st-batal { background: #FFEBEE; color: #C62828; }
        .st-packing { background: #E0F2F1; color: #00695C; }

        /* STATUS PEMBAYARAN TEXT */
        .badge-bayar { font-weight: 700; font-size: 12px; }
        .bayar-lunas { color: #2E7D32; }
        .bayar-dp { color: #F9A825; }
        .bayar-belum { color: #C62828; }
        .bayar-batal { color: #C62828; text-decoration: line-through; }

        /* MODIFIKASI HEADER UNTUK FILTER */
        .table-data .order .head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .table-data .order .head h3 { margin-right: auto; }

        .filter-input { padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 20px; font-size: 13px; outline: none; background: #fff; color: var(--text-primary); cursor: pointer; }
        .table-search-wrapper { position: relative; display: flex; align-items: center; }
        /* Perbaikan CSS Search Input biar konsisten sama JS */
        .table-search-input { width: 0; padding: 0; border: none; margin-left: 0; background: transparent; transition: width 0.3s ease, padding 0.3s ease; opacity: 0; pointer-events: none; height: 40px; border-radius: 20px; }
        .table-search-input.show { width: 200px; padding: 6px 12px; border: 1px solid var(--border-light); margin-right: 10px; opacity: 1; pointer-events: auto; background: var(--surface-white); }
        
        .bx-search-toggle { cursor: pointer; font-size: 20px; color: #888; padding: 5px; }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Manajemen Transaksi</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="{{ route('admin.transaksi.index') }}">Data Transaksi</a></li>
                    </ul>
                </div>
                
                <a href="{{ route('admin.transaksi.create') }}" class="btn-download">
                    <i class='bx bx-plus'></i> Tambah Order
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Data Transaksi</h3>
                        
                        {{-- 1. SEARCH --}}
                        <div class="table-search-wrapper">
                            {{-- ID disamakan dengan script.js (tableSearchInput) --}}
                            <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Pelanggan/Invoice...">
                            {{-- ID Icon disamakan (tableSearchIcon) --}}
                            <i class='bx bx-search bx-search-toggle' id="tableSearchIcon"></i>
                        </div>

                        {{-- 2. DROPDOWN STATUS BAYAR --}}
                        {{-- PERBAIKAN UTAMA: ID diubah jadi 'statusFilter' (bukan statusBayarFilter) & HAPUS onchange --}}
                        <select id="statusFilter" class="filter-input">
                            <option value="">Status Bayar</option>
                            <option value="lunas">Lunas</option>
                            <option value="belum">Belum Bayar</option>
                            <option value="dp">DP</option>
                        </select>

                        {{-- 3. FILTER TANGGAL --}}
                        {{-- HAPUS onchange applyFilter --}}
                        <input type="date" id="dateFilter" class="filter-input" title="Filter Tanggal Masuk">
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th>Kode Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Estimasi Selesai</th>
                                    <th>Status Pembayaran</th>
                                    <th>Total Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi as $item)
                                    <tr>
                                        {{-- SCRIPT JS BACA INI SBG DATA --}}
                                        <td><strong>{{ $item->kode_invoice }}</strong></td>
                                        <td>{{ $item->pelanggan->nama ?? 'Umum' }}</td>
                                        {{-- PENTING: Format Tanggal harus konsisten biar dibaca JS --}}
                                        <td>{{ date('d M Y', strtotime($item->tgl_masuk)) }}</td>
                                        <td>{{ date('d M Y', strtotime($item->tgl_selesai)) }}</td>
                                        
                                        {{-- KOLOM KE-5 (Index 4) DI BACA OLEH JS BUAT FILTER STATUS --}}
                                        <td>
                                            @if($item->status_bayar == 'lunas')
                                                <span class="badge-bayar bayar-lunas">LUNAS</span>
                                            @elseif($item->status_bayar == 'dp')
                                                <span class="badge-bayar bayar-dp">DP (Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }})</span>
                                            @else
                                                <span class="badge-bayar bayar-belum">BELUM BAYAR</span>
                                            @endif
                                        </td>
                                        
                                        <td>Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                                        
                                        <td>
                                            <div class="btn-action-group">
                                                <a href="{{ route('admin.transaksi.show', $item->id_transaksi) }}" class="btn-detail show" title="Lihat Detail">
                                                    <i class='bx bx-show'></i>
                                                </a>

                                                <a href="{{ route('admin.transaksi.edit', $item->id_transaksi) }}" class="btn-detail edit" title="Edit Data">
                                                    <i class='bx bx-edit'></i>
                                                </a>

                                                <form action="{{ route('admin.transaksi.destroy', $item->id_transaksi) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    
                                                    {{-- LOGIKA TOMBOL: Kalau sudah batal/selesai, tombol mati --}}
                                                    @if(in_array($item->status_pesanan, ['dibatalkan', 'selesai']))
                                                        <button type="button" class="btn-detail delete disabled" title="Sudah Selesai/Batal" style="opacity: 0.5; cursor: not-allowed;">
                                                            <i class='bx bx-x-circle'></i> {{-- Ganti ikon jadi X --}}
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn-detail delete" onclick="return confirm('Yakin ingin MEMBATALKAN transaksi ini? Data tidak akan bisa diubah lagi.')" title="Batalkan Transaksi">
                                                            <i class='bx bx-x-circle'></i> {{-- Ganti ikon jadi X biar beda sama trash --}}
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        {{ $transaksi->links() }}
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
    {{-- HAPUS script inline applyFilter, karena kita pakai logic JS dari script.js --}}
</body>
</html>