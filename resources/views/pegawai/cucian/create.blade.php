<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet" />
    <!-- My CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Tambah Cucian - Rizhaqi Laundry Pegawai</title>
    <style>
        .form-card {
            background: var(--primary-white);
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-light);
            max-width: 800px;
            margin: 24px auto;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            font-family: var(--roboto);
            font-size: 14px;
            background: var(--surface-white);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 24px;
        }

        .form-actions .btn-submit,
        .form-actions .btn-cancel {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .form-actions .btn-submit {
            background-color: var(--accent-blue);
            color: var(--primary-white);
        }

        .form-actions .btn-submit:hover {
            background-color: var(--accent-blue-hover);
        }

        .form-actions .btn-cancel {
            background-color: var(--text-tertiary);
            color: var(--primary-white);
        }

        .form-actions .btn-cancel:hover {
            background-color: #7a8086;
        }

        .custom-dropdown {
            position: relative;
            width: 100%;
            font-family: var(--roboto);
        }

        .dropdown-selected {
            padding: 10px 12px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            cursor: pointer;
            background: var(--surface-white);
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.2s;
        }

        .dropdown-selected:hover {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(26,115,232,0.1);
        }

        .dropdown-list {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            width: 100%;
            max-height: 180px;
            overflow-y: auto;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: var(--surface-white);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 10;
            padding: 5px 0;
        }

        .dropdown-list input {
            width: 90%;
            margin: 5px auto;
            display: block;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-light);
            font-size: 14px;
            font-family: var(--roboto);
            outline: none;
        }

        .dropdown-list input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(26,115,232,0.1);
        }

        .dropdown-item {
            padding: 10px 12px;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }

        .dropdown-item:last-child {
            border-bottom: none; /* hapus garis di item terakhir */
        }

        .dropdown-item:hover {
            background: rgba(26, 115, 232, 0.08);
        }

        /* Scrollbar minimal */
        .dropdown-list::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-list::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
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
                    <h1>Tambah Cucian</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('pegawai/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a href="{{ url('pegawai/cucian') }}">Data Cucian</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="{{ url('pegawai/cucian/create') }}">Tambah Cucian</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <form id="formTambahCucian">
                    <div class="form-group">
                        <label for="pelanggan">Nama Pelanggan</label>
                        <div class="custom-dropdown">
                            <div class="dropdown-selected" onclick="toggleDropdown()">
                                Pilih Pelanggan
                            </div>
                            <div id="dropdownList" class="dropdown-list">
                                <input type="text" id="dropdownSearch" onkeyup="filterDropdown()" placeholder="Cari pelanggan...">
                                <div class="dropdown-item" onclick="selectItem(this)">Budi Santoso</div>
                                <div class="dropdown-item" onclick="selectItem(this)">Ani Lestari</div>
                                <div class="dropdown-item" onclick="selectItem(this)">Putri Meliana</div>
                                <div class="dropdown-item" onclick="selectItem(this)">Agus Salim</div>
                            </div>
                            <input type="hidden" name="pelanggan" id="pelanggan">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="berat">Berat Cucian (Kg)</label>
                        <input type="number" id="berat" name="berat" placeholder="Masukkan berat cucian" min="0" step="0.1" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_masuk">Tanggal Masuk</label>
                        <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="Menunggu" selected>Menunggu</option>
                            <option value="Proses">Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='{{ url('pegawai/cucian') }}'">
                            <i class='bx bx-x'></i> Batal
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class='bx bx-save'></i> Simpan Cucian
                        </button>
                    </div>
                </form>
            </div>

            <div id="dummyResult" style="max-width:800px; margin:20px auto;"></div>

        </main>
    </section>

<script>
    const form = document.getElementById('formTambahCucian');
    const dummyResult = document.getElementById('dummyResult');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const pelanggan = document.getElementById('pelanggan').value;
        const berat = document.getElementById('berat').value;
        const tanggal = document.getElementById('tanggal_masuk').value;
        const status = document.getElementById('status').value;

        // Tampilkan hasil dummy
        dummyResult.innerHTML = `
            <h3>Data Cucian Tersimpan (Dummy)</h3>
            <ul>
                <li><strong>Pelanggan:</strong> ${pelanggan}</li>
                <li><strong>Berat:</strong> ${berat} Kg</li>
                <li><strong>Tanggal Masuk:</strong> ${tanggal}</li>
                <li><strong>Status:</strong> ${status}</li>
            </ul>
        `;

        // Reset form
        form.reset();
        document.getElementById('status').value = 'Menunggu';
    });
</script>

<script>
function toggleDropdown() {
    const list = document.getElementById('dropdownList');
    list.style.display = list.style.display === 'block' ? 'none' : 'block';
    document.getElementById('dropdownSearch').value = '';
    filterDropdown();
}

function filterDropdown() {
    const input = document.getElementById('dropdownSearch').value.toLowerCase();
    const items = document.querySelectorAll('.dropdown-item');
    items.forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(input) ? '' : 'none';
    });
}

function selectItem(element) {
    document.querySelector('.dropdown-selected').innerText = element.innerText;
    document.getElementById('pelanggan').value = element.innerText;
    document.getElementById('dropdownList').style.display = 'none';
}

// Tutup dropdown jika klik di luar
window.addEventListener('click', function(e) {
    const dropdown = document.querySelector('.custom-dropdown');
    if (!dropdown.contains(e.target)) {
        document.getElementById('dropdownList').style.display = 'none';
    }
});
</script>

<script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>
</html>
