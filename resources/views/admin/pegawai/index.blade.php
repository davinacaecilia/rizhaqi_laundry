<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Manajemen Pegawai - Rizhaqi Laundry Admin</title>
    
    <style>
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container thead tr { background-color: var(--surface-white); border-bottom: 1px solid var(--border-light); }
        .table-container th, .table-container td { padding: 15px; border: 1px solid var(--border-light); text-align: left; font-size: 14px; color: var(--text-primary); }
        .table-container th { font-weight: 600; color: var(--text-secondary); font-family: var(--google-sans); background-color: var(--surface-white); }
        .table-container tbody tr:nth-child(even) { background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }
        .table-container .btn-action-group { display: flex; gap: 5px; flex-wrap: wrap; }
        .table-container .btn-action-group .btn-detail { padding: 6px 12px; font-size: 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; transition: all 0.2s ease; font-weight: 500; }
        .table-container .btn-action-group .btn-detail.edit { background-color: var(--accent-blue); color: var(--primary-white); border: 1px solid var(--accent-blue); }
        .table-container .btn-action-group .btn-detail.edit:hover { background-color: var(--accent-blue-hover); border-color: var(--accent-blue-hover); }
        .table-container .btn-action-group .btn-detail.delete { background-color: var(--accent-red); color: var(--primary-white); border: 1px solid var(--accent-red); }
        .table-container .btn-action-group .btn-detail.delete:hover { background-color: #c52c20; border-color: #c52c20; }
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
                    <h1>Manajemen Pegawai</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="{{ route('admin.pegawai.index') }}">Data Pegawai</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Data Pegawai</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari pegawai...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="padding: 10px; border: 1px solid #ccc;">ID Pegawai</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Nama Pegawai</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Username</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Jabatan (Role)</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">PEG-001</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Pegawai A (Dummy)</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">pegawai_a</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Kasir</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.pegawai.edit', ['pegawai' => 1]) }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            <form action="{{ route('admin.pegawai.destroy', ['pegawai' => 1]) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-detail delete" onclick="return confirm('Yakin hapus pegawai ini?')">
                                                    <i class='bx bx-trash'></i> Delete
                                                </button>
                                            </form>
                                        </div>
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