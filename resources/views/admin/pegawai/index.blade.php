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
        /* CSS TABEL (VERSI AWAL / KOTAK-KOTAK) */
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
            /* BORDER DIKEMBALIKAN (KOTAK) */
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
            background-color: var(--surface-white);
            border: 1px solid var(--border-light);
            color: var(--text-secondary);
            cursor: pointer;
        }

        /* Warna Tombol Edit */
        .table-container .btn-action-group .btn-detail.btn-edit {
            background-color: var(--accent-blue);
            color: var(--primary-white);
            border: 1px solid var(--accent-blue);
        }
        .table-container .btn-action-group .btn-detail.btn-edit:hover {
            background-color: var(--accent-blue-hover);
            border-color: var(--accent-blue-hover);
        }

        /* Warna Tombol Hapus */
        .table-container .btn-action-group .btn-detail.btn-delete {
            background-color: var(--accent-red);
            color: var(--primary-white);
            border: 1px solid var(--accent-red);
        }
        .table-container .btn-action-group .btn-detail.btn-delete:hover {
            background-color: #c52c20;
            border-color: #c52c20;
        }
        
        /* Warna Tombol Status (Power) */
        .table-container .btn-action-group .btn-detail.btn-status-toggle {
            background-color: #F9A825;
            color: var(--primary-white);
            border: 1px solid #F9A825;
        }
        .table-container .btn-action-group .btn-detail.btn-status-toggle:hover {
            background-color: #F57F17;
            border-color: #F57F17;
        }

        /* CSS SEARCH */
        .table-data .order .head { position: relative; }
        .table-search-input {
            width: 0; padding: 0; border: none; transition: width 0.3s ease;
            background: var(--surface-white); border-radius: 20px; margin-left: auto; outline: none; height: 40px; 
        }
        .table-search-input.show { width: 200px; padding: 8px 12px; border: 1px solid var(--border-light); }
        .table-data .order .head .bx-search { margin-left: 10px; cursor: pointer; }

        /* BADGES */
        .badge-status { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .st-active { background: #E8F5E9; color: #2E7D32; }      /* Hijau */
        .st-inactive { background: #FFEBEE; color: #C62828; }    /* Merah */
        
        .badge-role { font-weight: 700; font-size: 12px; text-transform: uppercase; }
        .role-owner { color: #F9A825; }
        .role-pegawai { color: #1565C0; }
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
                
                <a href="{{ route('admin.pegawai.create') }}" class="btn-download">
                    <i class='bx bx-user-plus'></i> Tambah Pegawai
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Daftar Pengguna (User)</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Nama/Email...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email (Username)</th>
                                    <th>Role / Jabatan</th>
                                    <th>Status Akun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DUMMY 1: OWNER --}}
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <strong>Kak Elvira</strong><br>
                                        <span style="font-size: 12px; color: #888;">Pemilik</span>
                                    </td>
                                    <td>elvira@rizhaqi.com</td>
                                    <td>
                                        <span class="badge-role role-owner">OWNER</span>
                                    </td>
                                    <td>
                                        <span class="badge-status st-active">Aktif</span>
                                    </td>
                                    <td>
                                        <div class="btn-action-group">
                                            <button class="btn-detail edit" disabled style="background:#ccc; cursor:not-allowed;"><i class='bx bx-lock'></i></button>
                                        </div>
                                    </td>
                                </tr>
                                
                                {{-- DUMMY 2: Pegawai Aktif --}}
                                <tr>
                                    <td>2</td>
                                    <td>Budi Santoso</td>
                                    <td>budi@rizhaqi.com</td>
                                    <td>
                                        <span class="badge-role role-pegawai">Pegawai</span>
                                    </td>
                                    <td>
                                        <span class="badge-status st-active">Aktif</span>
                                    </td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.pegawai.edit', 2) }}" class="btn-detail btn-edit" title="Edit Data"><i class='bx bx-edit'></i></a>
                                            <button class="btn-detail btn-status-toggle" onclick="return confirm('Nonaktifkan akun pegawai ini?')" title="Nonaktifkan Akun"><i class='bx bx-power-off'></i></button>
                                            <button class="btn-detail btn-delete" onclick="return confirm('Hapus permanen pegawai ini?')" title="Hapus"><i class='bx bx-trash'></i></button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- DUMMY 3: Pegawai Non-Aktif --}}
                                <tr>
                                    <td>3</td>
                                    <td>Siti Aminah</td>
                                    <td>siti@rizhaqi.com</td>
                                    <td>
                                        <span class="badge-role role-pegawai">Pegawai</span>
                                    </td>
                                    <td>
                                        <span class="badge-status st-inactive">Non-Aktif</span>
                                    </td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.pegawai.edit', 3) }}" class="btn-detail btn-edit" title="Edit Data"><i class='bx bx-edit'></i></a>
                                            <button class="btn-detail btn-status-toggle" style="background-color: #2E7D32; border-color:#2E7D32;" onclick="return confirm('Aktifkan kembali akun ini?')" title="Aktifkan Akun"><i class='bx bx-check'></i></button>
                                            <button class="btn-detail btn-delete" onclick="return confirm('Hapus permanen pegawai ini?')" title="Hapus"><i class='bx bx-trash'></i></button>
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
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>