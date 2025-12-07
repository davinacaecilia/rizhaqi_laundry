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

    <title>Data Cucian - Rizhaqi Laundry Pegawai</title>

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

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
        }

        /* Warna untuk status */
        .status-proses {
            background-color: #e3f0ff;
            color: #1a73e8;
        }

        .status-selesai {
            background-color: #e6f4ea;
            color: #188038;
        }

        .status-menunggu {
            background-color: #f2f2f2;
            color: #6e6e6e;
        }

    </style>
</head>
<body>
    @include('layout.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Data Cucian</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('pegawai/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="{{ url('pegawai/cucian') }}">Data Cucian</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Data Cucian</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari cucian...">
                        <button id="tableSearchBtn" style="background:none; border:none; cursor:pointer; font-size:22px; display:flex; align-items:center;">
                            <i class='bx bx-search'></i>
                        </button>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Berat (Kg)</th>
                                    <th>Status</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                <td>C001</td>
                                <td>Budi Santoso</td>
                                <td>4 Kg</td>
                                <td><span class="status-badge status-proses">Proses</span></td>
                                <td>2025-01-02</td>
                                <td>
                                    <div class="btn-action-group">
                                        <button type="button" class="btn-detail edit" onclick="markAsDone('C001', this)">
                                            <i class='bx bx-check'></i> Selesai
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>C002</td>
                                <td>Ani Lestari</td>
                                <td>3 Kg</td>
                                <td><span class="status-badge status-selesai">Selesai</span></td>
                                <td>2025-01-01</td>
                                <td>
                                    <div class="btn-action-group">
                                        <button class="btn-detail edit" onclick="markAsDone('C002', this)">
                                            <i class='bx bx-check'></i> Selesai
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>C003</td>
                                <td>Putri Meliana</td>
                                <td>6 Kg</td>
                                <td><span class="status-badge status-menunggu">Menunggu</span></td>
                                <td>2025-01-03</td>
                                <td>
                                    <div class="btn-action-group">
                                        <button class="btn-detail edit" onclick="markAsDone('C003', this)">
                                            <i class='bx bx-check'></i> Selesai
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
    // Toggle search input
    const searchBtn = document.getElementById('tableSearchBtn');
    const searchInput = document.getElementById('tableSearchInput');

    searchBtn.addEventListener('click', () => {
        searchInput.classList.toggle('show');
        if (searchInput.classList.contains('show')) searchInput.focus();
    });

    // Live search table
    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('.table-container tbody tr');

        rows.forEach(row => {
            const rowText = row.innerText.toLowerCase();
            row.style.display = rowText.includes(filter) ? '' : 'none';
        });
    });

    function markAsDone(id, btn) {

        // ambil row tempat tombol ditekan
        let row = btn.closest("tr");
        let statusBadge = row.querySelector(".status-badge");

        // popup konfirmasi
        if (confirm(`Yakin ingin tandai cucian ${id} sebagai selesai ?`)) {

            // ubah status badge
            statusBadge.classList.remove("status-proses", "status-menunggu");
            statusBadge.classList.add("status-selesai");
            statusBadge.innerText = "Selesai";

            // disable tombol
            btn.disabled = true;
            btn.style.opacity = "0.5";
            btn.style.cursor = "not-allowed";

            alert(`Cucian ${id} berhasil ditandai selesai!`);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {

        document.querySelectorAll(".table-container tbody tr").forEach(row => {
            let statusBadge = row.querySelector(".status-badge");
            let btn = row.querySelector(".btn-detail.edit");

            if (statusBadge && btn) {
                if (statusBadge.innerText.trim().toLowerCase() === "selesai") {
                    btn.disabled = true;
                    btn.style.opacity = "0.5";
                    btn.style.cursor = "not-allowed";
                }
            }
        });

    });
</script>

    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>
