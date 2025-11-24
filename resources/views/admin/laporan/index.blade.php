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

    <title>Product Management</title>
    <style>
        .table-data .order .head {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .table-data .order .head h3 {
            margin-right: auto;
            font-size: 20px;
            font-weight: 500;
            font-family: var(--google-sans);
            color: var(--text-primary);
        }

        .table-data .order .head .bx {
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            color: var(--text-secondary);
            transition: all 0.2s ease;
        }

        .table-data .order .head .bx:hover {
            background: var(--surface-white);
            color: var(--text-primary);
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

        .table-container td.hash-password {
            font-family: monospace;
            font-size: 0.85rem;
            word-break: break-all;
        }

    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        <!-- NAVBAR -->
        @include('partial.navbar')
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>User List</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="{{ url('admin/users') }}">User List</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>User List</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Search user...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                        <i class='bx bx-filter'></i>
                    </div>
                    <!-- Tabel Statis Daftar Pengguna -->
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="padding: 10px; border: 1px solid #ccc;">ID User</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Name</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Email</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Hash Password</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">User Level</th>
                                    <th style="padding: 10px; border: 1px solid #ccc;">Registered At</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dummy Data Statis --}}
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">1</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Igun</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">igun@example.com</td>
                                    <td class="hash-password" style="padding: 10px; border: 1px solid #ccc;">
                                        $2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Admin</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">01 Jan 2023, 10:00</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">2</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Budi Santoso</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">budi.santoso@example.com</td>
                                    <td class="hash-password" style="padding: 10px; border: 1px solid #ccc;">
                                        $2y$10$ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz789012
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">User</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">15 Feb 2023, 14:30</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ccc;">3</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Citra Dewi</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">citra.dewi@example.com</td>
                                    <td class="hash-password" style="padding: 10px; border: 1px solid #ccc;">
                                        $2y$10$QRSTUVWXYZabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOP1234567
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">Supervisor</td>
                                    <td style="padding: 10px; border: 1px solid #ccc;">20 Mar 2023, 09:15</td>
                                </tr>
                                {{-- Tambahkan baris <tr> lainnya untuk data statis jika diperlukan --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
        <!-- MAIN -->
    </section>

    <!-- Pagination di paling bawah -->
    <div id="pagination" class="pagination-container"></div>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/pagination.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
 
</body>
</html>
