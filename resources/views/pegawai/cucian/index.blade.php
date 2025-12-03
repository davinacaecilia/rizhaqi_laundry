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

    /* MODAL BACKDROP */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
            transition: all 0.3s ease;
        }

        /* MODAL BOX */
        .modal-content {
            background-color: var(--surface-white);
            margin: 10% auto;
            padding: 25px 30px;
            border-radius: 12px;
            width: 320px;
            max-width: 90%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            position: relative;
            animation: slideDown 0.3s ease;
        }

        /* MODAL ANIMATION */
        @keyframes slideDown {
            0% { transform: translateY(-20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        /* CLOSE BUTTON */
        .close {
            position: absolute;
            top: 12px;
            right: 15px;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            color: #555;
            transition: color 0.2s;
        }

        .close:hover {
            color: var(--accent-blue);
        }

        /* MODAL HEADING */
        .modal-content h3 {
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }

        /* DROPDOWN SELECT */
        #statusSelect {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-light);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        #statusSelect:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }

        /* SAVE BUTTON */
        .modal-content .btn-detail.edit {
            width: 100%;
            padding: 10px 0;
            margin-top: 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            background-color: var(--accent-blue);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-content .btn-detail.edit:hover {
            background-color: var(--accent-blue-hover);
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
                                        <button class="btn-detail edit" onclick="openStatusModal('C001', 'Proses')">
                                            <i class='bx bx-edit'></i> Edit Status
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
                                        <button class="btn-detail edit" onclick="openStatusModal('C002', 'Selesai')">
                                            <i class='bx bx-edit'></i> Edit Status
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
                                        <button class="btn-detail edit" onclick="openStatusModal('C003', 'Menunggu')">
                                            <i class='bx bx-edit'></i> Edit Status
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

    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStatusModal()">&times;</span>
            <h3>Ganti Status Cucian <span id="modalCucianId"></span></h3>
            <label for="statusSelect">Status:</label>
            <select id="statusSelect">
                <option value="Proses">Proses</option>
                <option value="Selesai">Selesai</option>
                <option value="Menunggu">Menunggu</option>
            </select>
            <button id="saveStatusBtn" class="btn-detail edit" onclick="saveStatus()">
                <i class='bx bx-check'></i> Simpan
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const statusSpan = row.children[3].querySelector('.status-badge');
                const statusText = statusSpan ? statusSpan.innerText.trim() : '';
                const editBtn = row.querySelector('.btn-detail.edit');

                // Hanya disable tombol jika status Selesai
                if (statusText === 'Selesai') {
                    editBtn.disabled = true;
                    editBtn.style.backgroundColor = '#ccc';
                    editBtn.style.cursor = 'not-allowed';
                } else {
                    editBtn.disabled = false;
                    editBtn.style.backgroundColor = 'var(--accent-blue)';
                    editBtn.style.cursor = 'pointer';
                }
            });
        });

        let currentRowId = null;

        function openStatusModal(id, currentStatus) {
            currentRowId = id;
            const select = document.getElementById('statusSelect');
            const saveBtn = document.getElementById('saveStatusBtn');

            document.getElementById('modalCucianId').innerText = id;
            select.value = currentStatus;

            // Disable dropdown & tombol jika Selesai
            if (currentStatus === 'Selesai') {
                select.disabled = true;
                saveBtn.disabled = true;
                saveBtn.style.backgroundColor = '#ccc';
                saveBtn.style.cursor = 'not-allowed';
            } else {
                select.disabled = false;
                saveBtn.disabled = false;
                saveBtn.style.backgroundColor = 'var(--accent-blue)';
                saveBtn.style.cursor = 'pointer';
            }

            document.getElementById('statusModal').style.display = 'block';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        function saveStatus() {
            const newStatus = document.getElementById('statusSelect').value;

            alert(`Status cucian ${currentRowId} diubah menjadi ${newStatus} (dummy).`);

            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (row.children[0].innerText === currentRowId) {
                    row.children[3].innerHTML = `<span class="status-badge status-${newStatus.toLowerCase()}">${newStatus}</span>`;

                    // Disable tombol jika status baru Selesai
                    const editBtn = row.querySelector('.btn-detail.edit');
                    if (newStatus === 'Selesai') {
                        editBtn.disabled = true;
                        editBtn.style.backgroundColor = '#ccc';
                        editBtn.style.cursor = 'not-allowed';
                    }
                }
            });

            closeStatusModal();
        }

        // Tutup modal jika klik di luar
        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
</script>

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
    </script>

    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>
</html>
