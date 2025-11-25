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
        /* CSS TABEL BERSIH (TANPA GARIS DOUBLE) */
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .table-container thead tr {
            background-color: var(--surface-white);
            border-bottom: 1px solid var(--border-light);
        }

        .table-container th,
        .table-container td {
            padding: 15px;
            border: none; 
            border-bottom: 1px solid var(--border-light);
            text-align: left;
            font-size: 14px;
            color: var(--text-primary);
        }

        .table-container th {
            font-weight: 600;
            color: var(--text-secondary);
            font-family: var(--google-sans);
            background-color: var(--surface-white);
        }

        .table-container tbody tr:hover {
            background-color: rgba(26, 115, 232, 0.04);
        }

        /* TOMBOL AKSI */
        .table-container .btn-action-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .table-container .btn-action-group .btn-detail {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
            border: none; /* Hapus border default */
            cursor: pointer;
        }

        .btn-detail.show { background-color: var(--accent-green); color: white; }
        .btn-detail.edit { background-color: var(--accent-blue); color: white; }
        .btn-detail.delete { background-color: var(--accent-red); color: white; }
        .btn-detail:hover { opacity: 0.9; }

        /* BADGES STATUS ORDER */
        .badge-status { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .st-siap { background: #E8F5E9; color: #2E7D32; }      /* Hijau */
        .st-dicuci { background: #E3F2FD; color: #1565C0; }    /* Biru */
        .st-diterima { background: #E0E0E0; color: #424242; }  /* Abu */

        /* STATUS PEMBAYARAN TEXT */
        .badge-bayar { font-weight: 700; font-size: 12px; }
        .bayar-lunas { color: #2E7D32; }
        .bayar-dp { color: #F9A825; }
        .bayar-belum { color: #C62828; }

        /* SEARCH BAR */
        .table-data .order .head { position: relative; }
        .table-search-input {
            width: 0; padding: 0; border: none; transition: width 0.3s ease;
            background: var(--surface-white); border-radius: 20px; margin-left: auto; outline: none; height: 40px; 
        }
        .table-search-input.show { width: 200px; padding: 8px 12px; border: 1px solid var(--border-light); }
        .table-data .order .head .bx-search { margin-left: 10px; cursor: pointer; }
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
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
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
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Invoice...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Kode Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Estimasi Selesai</th>
                                    <th>Status Order</th>
                                    <th>Status Pembayaran</th>
                                    <th>Total Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DATA 1: Lunas & Siap --}}
                                <tr>
                                    <td><strong>D0222</strong></td>
                                    <td>Pak Rahmat</td>
                                    <td>24 Nov 2025</td>
                                    <td>26 Nov 2025</td>
                                    <td><span class="badge-status st-siap">Siap Diambil</span></td>
                                    <td><span class="badge-bayar bayar-lunas">LUNAS</span></td>
                                    <td>Rp 45.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.transaksi.show', 1) }}" class="btn-detail show" title="Lihat Invoice">
                                                <i class='bx bx-show'></i>
                                            </a>
                                            <a href="{{ route('admin.transaksi.edit', 1) }}" class="btn-detail edit" title="Edit">
                                                <i class='bx bx-edit'></i>
                                            </a>
                                            <a href="#" class="btn-detail delete" onclick="return confirm('Hapus data ini?')" title="Hapus">
                                                <i class='bx bx-trash'></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                {{-- DATA 2: DP & Dicuci --}}
                                <tr>
                                    <td><strong>D0223</strong></td>
                                    <td>Bu Siti</td>
                                    <td>24 Nov 2025</td>
                                    <td>26 Nov 2025</td>
                                    <td><span class="badge-status st-dicuci">Dicuci</span></td>
                                    <td><span class="badge-bayar bayar-dp">DP (50%)</span></td>
                                    <td>Rp 150.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.transaksi.show', 2) }}" class="btn-detail show"><i class='bx bx-show'></i></a>
                                            <a href="{{ route('admin.transaksi.edit', 2) }}" class="btn-detail edit"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail delete"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- DATA 3: Belum Bayar & Diterima --}}
                                <tr>
                                    <td><strong>D0224</strong></td>
                                    <td>Kak Dinda</td>
                                    <td>25 Nov 2025</td>
                                    <td>27 Nov 2025</td>
                                    <td><span class="badge-status st-diterima">Diterima</span></td>
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