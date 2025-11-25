<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Jumlah Alat Tersedia - Rizhaqi Laundry Admin</title>
    
    <style>
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container thead tr { background-color: var(--surface-white); border-bottom: 1px solid var(--border-light); }
        .table-container th, .table-container td { padding: 15px; border: 1px solid var(--border-light); text-align: left; font-size: 14px; color: var(--text-primary); }
        .table-container th { font-weight: 600; color: var(--text-secondary); font-family: var(--google-sans); background-color: var(--surface-white); }
        .table-container tbody tr:nth-child(even) { background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-ready {
            background-color: rgba(52, 168, 83, 0.1);
            color: var(--accent-green);
        }
        .status-unavailable {
            background-color: rgba(234, 67, 53, 0.1);
            color: var(--accent-red);
        }
        .status-warning {
            background-color: rgba(251, 188, 4, 0.1);
            color: #F8B500;
        }
        .table-data .order .head { position: relative; }
        .table-search-input { width: 0; padding: 0; border: none; transition: width 0.3s ease, padding 0.3s ease, border 0.3s ease; box-sizing: border-box; background: var(--surface-white); color: var(--text-primary); font-size: 14px; border-radius: 20px; margin-left: auto; outline: none; height: 40px; }
        .table-search-input.show { width: 200px; padding: 8px 12px; border: 1px solid var(--border-light); box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1); }
        .table-search-input.show:focus { border-color: var(--accent-blue); }
        .table-data .order .head .bx-search { margin-left: 10px; }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Jumlah Alat Tersedia</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.alat.index') }}">Manajemen Alat</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="{{ route('admin.alat.stok') }}">Stok Alat</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Ringkasan Ketersediaan Alat</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari alat...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="padding: 10px; border: 1px solid #ccc;">Nama Alat</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Total Unit</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Unit Terpakai</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Unit Tersedia</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Mesin Cuci 10 Kg</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">5 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">3 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">2 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <span class="status-badge status-ready">Tersedia</span>
                                    </td>
                                </tr> 
                                
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Mesin Pengering</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">4 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">4 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc; font-weight: 600;">0 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <span class="status-badge status-unavailable">Habis Dipakai</span>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Setrika Uap</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">3 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">1 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">2 Unit</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <span class="status-badge status-ready">Tersedia</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="5" style="padding: 10px; border: 1px solid #ccc; text-align: center;">
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
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>