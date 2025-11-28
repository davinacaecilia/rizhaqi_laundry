<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />


    <title>Data Pelanggan - Rizhaqi Laundry Admin</title>
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

    <!-- Main content -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Data Pelanggan</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="{{ url('admin/pelanggan') }}">Data Pelanggan</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Data Pelanggan</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari nama pelanggan...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Pelanggan</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>No Telepon</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PL001</td>
                                    <td>Agus Salim</td>
                                    <td>Jl. Kenanga No. 3</td>
                                    <td>082233445566</td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="{{ route('admin.pelanggan.edit', ['pelanggan' => 1]) }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            <form action="{{ route('admin.pelanggan.destroy', ['pelanggan' => 1]) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-detail delete" onclick="return confirm('Yakin hapus pelanggan ini?')">
                                                    <i class='bx bx-trash'></i> Delete
                                                </button>
                                            </form>
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
