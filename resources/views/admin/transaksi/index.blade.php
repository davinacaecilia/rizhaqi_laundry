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

        /* BADGES STATUS ORDER (Tetap disimpan barangkali butuh di detail) */
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

        /* --- MODIFIKASI HEADER UNTUK FILTER --- */
        .table-data .order .head { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            flex-wrap: wrap; /* Supaya rapi di HP */
        }
        .table-data .order .head h3 { margin-right: auto; } /* Judul di kiri mentok */

        /* Style Input Filter */
        .filter-input {
            padding: 8px 12px;
            border: 1px solid var(--border-light);
            border-radius: 20px;
            font-size: 13px;
            outline: none;
            background: #fff;
            color: var(--text-primary);
        }
        
        /* Search Bar Animasi */
        .table-search-wrapper { position: relative; display: flex; align-items: center; }
        .table-search-input {
            width: 0; padding: 0; border: none; transition: width 0.3s ease;
            background: var(--surface-white); border-radius: 20px; outline: none; height: 40px; 
        }
        .table-search-input.show { width: 200px; padding: 8px 12px; border: 1px solid var(--border-light); }
        .bx-search { cursor: pointer; font-size: 20px; padding: 5px; }

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
                        
                        <div class="table-search-wrapper">
                            <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Nama Pelanggan...">
                            <i class='bx bx-search' id="tableSearchIcon"></i>
                        </div>

                        <input type="date" id="dateFilter" class="filter-input" title="Filter Tanggal Masuk">
                        
                        
                        <select id="statusFilter" class="filter-input">
                            <option value="">Semua Status</option>
                            <option value="LUNAS">Lunas</option>
                            <option value="BELUM BAYAR">Belum Bayar</option>
                            <option value="DP">DP (Uang Muka)</option>
                            <option value="CANCEL">Dibatalkan</option>
                        </select>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th>Kode Invoice</th>
                                    <th>Nama Pelanggan</th> <!-- Index 1 -->
                                    <th>Tanggal Masuk</th>  <!-- Index 2 -->
                                    <th>Estimasi Selesai</th>
                                    <th>Status Pembayaran</th> <!-- Index 4 (Karena Status Order dihapus) -->
                                    <th>Total Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DATA 1: Budi (Lunas) --}}
                                <tr>
                                    <td><strong>TRX-001</strong></td>
                                    <td>Budi Santoso</td>
                                    <td>24 Nov 2025</td>
                                    <td>26 Nov 2025</td>
                                    <td><span class="badge-bayar bayar-lunas">LUNAS</span></td>
                                    <td>Rp 45.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.transaksi.show', 1) }}" class="btn-detail show"><i class='bx bx-show'></i></a>
                                            <a href="#" class="btn-detail edit disabled"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail delete" onclick="return confirm('Hapus?')"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                                
                                {{-- DATA 2: Ani (DP) --}}
                                <tr>
                                    <td><strong>TRX-002</strong></td>
                                    <td>Ani Wijaya</td>
                                    <td>24 Nov 2025</td>
                                    <td>26 Nov 2025</td>
                                    <td><span class="badge-bayar bayar-dp">DP 50.000</span></td>
                                    <td>Rp 150.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.transaksi.show', 2) }}" class="btn-detail show"><i class='bx bx-show'></i></a>
                                            <a href="{{ route('admin.transaksi.edit', 2) }}" class="btn-detail edit"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail delete"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- DATA 3: Citra (Belum Bayar) --}}
                                <tr>
                                    <td><strong>TRX-003</strong></td>
                                    <td>Citra Lestari</td>
                                    <td>25 Nov 2025</td>
                                    <td>27 Nov 2025</td>
                                    <td><span class="badge-bayar bayar-belum">BELUM BAYAR</span></td>
                                    <td>Rp 20.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.transaksi.show', 3) }}" class="btn-detail show"><i class='bx bx-show'></i></a>
                                            <a href="{{ route('admin.transaksi.edit', 3) }}" class="btn-detail edit"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail delete"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- DATA 6: Mbak Rini (Lunas) --}}
                                <tr>
                                    <td><strong>TRX-006</strong></td>
                                    <td>Mbak Rini</td>
                                    <td>24 Nov 2025</td>
                                    <td>26 Nov 2025</td>
                                    <td><span class="badge-bayar bayar-lunas">LUNAS</span></td>
                                    <td>Rp 75.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.transaksi.show', 6) }}" class="btn-detail show"><i class='bx bx-show'></i></a>
                                            <a href="{{ route('admin.transaksi.edit', 6) }}" class="btn-detail edit"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail delete"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- DATA 4: Rina (Lunas) --}}
                                <tr>
                                    <td><strong>TRX-004</strong></td>
                                    <td>Rina Nose</td>
                                    <td>22 Nov 2025</td>
                                    <td>24 Nov 2025</td>
                                    <td><span class="badge-bayar bayar-lunas">LUNAS</span></td>
                                    <td>Rp 35.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.transaksi.show', 4) }}" class="btn-detail show"><i class='bx bx-show'></i></a>
                                            <a href="#" class="btn-detail edit disabled"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail delete" onclick="return confirm('Hapus arsip?')"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- DATA 5: Doni (Cancel) --}}
                                <tr>
                                    <td><strong>TRX-005</strong></td>
                                    <td>Doni</td>
                                    <td>25 Nov 2025</td>
                                    <td>-</td>
                                    <td><span class="badge-bayar bayar-batal">Cancel</span></td>
                                    <td>Rp 0</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.transaksi.show', 5) }}" class="btn-detail show"><i class='bx bx-show'></i></a>
                                            <a href="#" class="btn-detail edit disabled"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail delete" onclick="return confirm('Hapus data batal?')"><i class='bx bx-trash'></i></a>
                                        </div>
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
    <script src="{{ asset('admin/script/chart.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
    
</body>
</html>