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

    <title>Tambah Layanan - Rizhaqi Laundry Admin</title>
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

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group input[type="file"],
        .form-group textarea,
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
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
    </style>
</head>
<body>

     @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Tambah Layanan</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a href="layanan.html">Data Layanan</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="{{ url('admin/layanan/create') }}">Tambah Layanan</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <form>
                    <div class="form-group">
                    <label for="kategori_select">Kategori</label>

                    <div style="display: flex; gap: 20px; margin-bottom: 10px;">
                        <label style="display:flex; align-items:center; gap:6px;">
                            <input type="radio" name="kategori_mode" value="existing">
                            Pilih kategori yang sudah ada
                        </label>

                        <label style="display:flex; align-items:center; gap:6px;">
                            <input type="radio" name="kategori_mode" value="new">
                            Buat kategori baru
                        </label>
                    </div>

                    <!-- Input dinamis -->
                    <div id="kategori_container">
                        <input
                            type="text"
                            disabled
                            placeholder="Pilih salah satu opsi di atas"
                            style="background:#f1f1f1; cursor:not-allowed;"
                        >
                    </div>
                </div>

                    <div class="form-group">
                        <label for="nama_layanan">Nama Layanan</label>
                        <input type="text" id="nama_layanan" name="nama_layanan" placeholder="Masukkan nama layanan">
                    </div>

                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <select id="satuan" name="satuan">
                            <option value="">Pilih satuan</option>
                            <option value="kg">kg</option>
                            <option value="pcs">pcs</option>
                            <option value="m2">m²</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="harga_satuan">Harga Satuan</label>
                        <input type="text" id="harga_satuan" name="harga_satuan" placeholder="Contoh: 15.000">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" placeholder="Tambahkan deskripsi layanan"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='{{ url('admin/layanan') }}'">
                            <i class='bx bx-x'></i> Batal
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class='bx bx-save'></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </main>
        <!-- MAIN -->
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

    <script>
    const radios = document.querySelectorAll('input[name="kategori_mode"]');
    const container = document.getElementById('kategori_container');

    function updateKategoriInput() {
        const selected = document.querySelector('input[name="kategori_mode"]:checked');

        // Jika belum ada radio yang dipilih → tetap disabled
        if (!selected) {
            container.innerHTML = `
                <input
                    type="text"
                    disabled
                    placeholder="Pilih salah satu opsi di atas"
                    style="background:#f1f1f1; cursor:not-allowed;"
                >
            `;
            return;
        }

        // Kalau pilih kategori lama
        if (selected.value === "existing") {
            container.innerHTML = `
                <select id="kategori_select" name="kategori">
                    <option value="">Pilih kategori</option>
                    <option value="regular">Regular</option>
                    <option value="paket">Paket</option>
                    <option value="satuan">Satuan</option>
                    <option value="karpet">Karpet</option>
                    <option value="add_on">Add On</option>
                </select>
            `;
        }

        // Kalau pilih kategori baru
        else if (selected.value === "new") {
            container.innerHTML = `
                <input
                    type="text"
                    id="kategori_baru"
                    name="kategori_baru"
                    placeholder="Masukkan kategori baru"
                >
            `;
        }
    }

    radios.forEach(r => r.addEventListener('change', updateKategoriInput));

    // Kondisi awal → disable
    updateKategoriInput();
</script>

</body>
</html>
