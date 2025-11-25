<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Status Order - Rizhaqi Laundry Admin</title>
    
    <style>
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
            border: 1px solid var(--border-light);
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

        .table-container tbody tr:nth-child(even) {
            background-color: var(--surface-white);
        }

        .table-container tbody tr:hover {
            background-color: rgba(26, 115, 232, 0.04);
        }

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
            background-color: var(--surface-white);
            border: 1px solid var(--border-light);
            color: var(--text-secondary);
            cursor: pointer;
        }

        /* Styling for Status buttons */
        .table-container .btn-action-group .btn-approve {
            background-color: var(--accent-green);
            color: var(--primary-white);
            border: 1px solid var(--accent-green);
        }
        .table-container .btn-action-group .btn-approve:hover {
            background-color: #288a42;
            border-color: #288a42;
        }

        .table-container .btn-action-group .btn-reject {
            background-color: var(--accent-red);
            color: var(--primary-white);
            border: 1px solid var(--accent-red);
        }
        .table-container .btn-action-group .btn-reject:hover {
            background-color: #c52c20;
            border-color: #c52c20;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-pending {
            background-color: rgba(251, 188, 4, 0.1);
            color: #F8B500;
        }
        .status-approved {
            background-color: rgba(52, 168, 83, 0.1);
            color: var(--accent-green);
        }
        .status-rejected {
            background-color: rgba(234, 67, 53, 0.1);
            color: var(--accent-red);
        }
        .status-processing {
            background-color: rgba(26, 115, 232, 0.1);
            color: var(--accent-blue);
        }

        /* Search */
        .table-data .order .head {
            position: relative;
        }
        .table-search-input {
            width: 0;
            padding: 0;
            border: none;
            transition: width 0.3s ease, padding 0.3s ease, border 0.3s ease;
            box-sizing: border-box;
            background: var(--surface-white);
            color: var(--text-primary);
            font-size: 14px;
            border-radius: 20px;
            margin-left: auto;
            outline: none;
            height: 40px;
        }
        .table-search-input.show {
            width: 200px;
            padding: 8px 12px;
            border: 1px solid var(--border-light);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1);
        }
        .table-search-input.show:focus {
            border-color: var(--accent-blue);
        }
        .table-data .order .head .bx-search {
            margin-left: 10px;
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
                    <h1>Status Order</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="{{ route('admin.transaksi.status') }}">Status Order</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Update Status Cucian (Real-time)</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Kode Invoice...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="padding: 10px; border: 1px solid #ccc;">Kode Invoice</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Nama Pelanggan</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Status Sekarang</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Aksi Selanjutnya</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- SKENARIO 1: Status DITERIMA -> Tombol "Mulai Cuci" --}}
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;"><strong>D0225</strong></td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Ibu Ratna</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <span class="status-badge" style="background-color: #E0E0E0; color: #424242;">Diterima</span>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <div class="btn-action-group">
                                            <button type="button" class="btn-detail" style="background-color: var(--accent-blue); color: white; border: none;">
                                                <i class='bx bx-water'></i> Mulai Cuci
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- SKENARIO 2: Status DICUCI -> Tombol "Ke Pengeringan" --}}
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;"><strong>D0224</strong></td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Kak Dinda</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <span class="status-badge status-processing">Dicuci</span>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <div class="btn-action-group">
                                            <button type="button" class="btn-detail" style="background-color: #FF9800; color: white; border: none;">
                                                <i class='bx bx-wind'></i> Ke Pengeringan
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- SKENARIO 3: Status DIKERINGKAN -> Tombol "Mulai Setrika" --}}
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;"><strong>D0223</strong></td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Bu Siti</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <span class="status-badge status-pending" style="color: #EF6C00;">Dikeringkan</span>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <div class="btn-action-group">
                                            <button type="button" class="btn-detail" style="background-color: #9C27B0; color: white; border: none;">
                                                <i class='bx bx-iron'></i> Mulai Setrika
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- SKENARIO 4: Status DISETRIKA -> Tombol "Selesai/Siap" --}}
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;"><strong>D0222</strong></td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Pak Rahmat</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <span class="status-badge status-processing" style="background-color: #F3E5F5; color: #7B1FA2;">Disetrika</span>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <div class="btn-action-group">
                                            <button type="button" class="btn-detail btn-approve">
                                                <i class='bx bx-check-circle'></i> Tandai Siap
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- SKENARIO 5: Status SIAP DIAMBIL -> Tombol "Sudah Diambil" --}}
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;"><strong>D0221</strong></td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Mas Tono</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <span class="status-badge status-approved">Siap Diambil</span>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <div class="btn-action-group">
                                            <button type="button" class="btn-detail" style="background-color: #455A64; color: white; border: none;">
                                                <i class='bx bx-package'></i> Konfirmasi Ambil
                                            </button>
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