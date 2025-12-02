<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Data Layanan - Rizhaqi Laundry Admin</title>
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
            color: var(--primary-white);
            border: 1px solid var(--accent-blue);
        }

        .btn-detail.edit:hover {
            background-color: var(--accent-blue-hover);
            border-color: var(--accent-blue-hover);
        }

        .btn-detail.delete {
            background-color: var(--accent-red);
            color: var(--primary-white);
            border: 1px solid var(--accent-red);
        }

        .btn-detail.delete:hover {
            background-color: #c52c20;
            border-color: #c52c20;
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
                    <h1>Data Layanan</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="{{ url('admin/layanan') }}">Data Layanan</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Data Layanan</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari layanan...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Layanan</th>
                                    <th>Kategori</th>
                                    <th>Nama Layanan</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>LYN-001</td>
                                    <td>Regular</td>
                                    <td>Cuci Kering Lipat</td>
                                    <td>Kg</td>
                                    <td>Rp 7.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ url('admin/layanan/1/edit') }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            <button type="submit" class="btn-detail delete" onclick="return confirm('Are you sure you want to delete this artwork?')">
                                                    <i class='bx bx-trash'></i> Delete
                                                </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>LYN-002</td>
                                    <td>Paket</td>
                                    <td>Cuci + Setrika Express</td>
                                    <td>Kg</td>
                                    <td>Rp 10.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ url('admin/layanan/1/edit') }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            <button type="submit" class="btn-detail delete" onclick="return confirm('Are you sure you want to delete this artwork?')">
                                                    <i class='bx bx-trash'></i> Delete
                                                </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>LYN-003</td>
                                    <td>Karpet</td>
                                    <td>Cuci Karpet Tebal</td>
                                    <td>m²</td>
                                    <td>Rp 25.000</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ url('admin/layanan/1/edit') }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            <button type="submit" class="btn-detail delete" onclick="return confirm('Are you sure you want to delete this artwork?')">
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

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/pagination.js') }}"></script>
    <script src="{{ asset('admin/script/chart.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

    <script>
        // Buat animasi search bar
        const searchIcon = document.getElementById('tableSearchIcon');
        const searchInput = document.getElementById('tableSearchInput');

        searchIcon.addEventListener('click', () => {
            searchInput.classList.toggle('show');
            searchInput.focus();
        });
    </script>
</body>
</html>
