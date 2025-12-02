<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <!-- My CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Data Alat - Rizhaqi Laundry Admin</title>

    <style>
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .table-container th,
        .table-container td {
            padding: 15px;
            border: 1px solid var(--border-light);
            text-align: left;
            font-size: 14px;
        }

        .table-container th {
            background-color: var(--surface-white);
            font-weight: 600;
        }

        .table-container tbody tr:nth-child(even) {
            background-color: var(--surface-white);
        }

        .table-container tbody tr:hover {
            background-color: rgba(26, 115, 232, 0.04);
        }

        .btn-action-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-detail {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .btn-detail.edit {
            background-color: var(--accent-blue);
            color: white;
        }

        .btn-detail.edit:hover {
            background-color: var(--accent-blue-hover);
        }

        .btn-detail.delete {
            background-color: var(--accent-red);
            color: white;
        }

        .btn-detail.delete:hover {
            background-color: #c52c20;
        }

        /* SEARCH */
        .table-search-input {
            width: 0;
            padding: 0;
            border: none;
            transition: width 0.3s ease, padding 0.3s ease, border 0.3s ease;
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
        }

        .table-search-input.show:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1);
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
                    <h1>Data Alat</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="{{ url('admin/alat') }}">Data Alat</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Data Alat</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari alat...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Alat</th>
                                    <th>Nama Alat</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Maintenance Terakhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>AL001</td>
                                    <td>Mesin Cuci LG Turbo 10kg</td>
                                    <td>4</td>
                                    <td>2025-10-20</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ url('admin/alat/1/edit') }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            <button class="btn-detail delete" onclick="hapusData('AL001')">
                                                <i class='bx bx-trash'></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>AL002</td>
                                    <td>Setrika Uap Philips</td>
                                    <td>6</td>
                                    <td>2025-09-15</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ url('admin/alat/edit/2') }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            <button class="btn-detail delete" onclick="hapusData('AL002')">
                                                <i class='bx bx-trash'></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>AL003</td>
                                    <td>Pengering Otomatis Panasonic</td>
                                    <td>3</td>
                                    <td>2025-08-05</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ url('admin/alat/edit/3') }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            <button class="btn-detail delete" onclick="hapusData('AL003')">
                                                <i class='bx bx-trash'></i> Delete
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

    <script>
        // Dummy delete
        function hapusData(id) {
            if (confirm(`Yakin ingin menghapus data alat ${id}?`)) {
                alert(`Data alat ${id} berhasil dihapus (dummy).`);
            }
        }

        // Search bar animation
        document.getElementById('tableSearchIcon').addEventListener('click', function() {
            const input = document.getElementById('tableSearchInput');
            input.classList.toggle('show');
            if (input.classList.contains('show')) input.focus();
        });
    </script>

    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>
</html>
