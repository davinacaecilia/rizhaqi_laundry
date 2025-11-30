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

    <title>Edit Pelanggan - Rizhaqi Laundry Admin</title>
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
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            font-size: 14px;
            background: var(--surface-white);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 24px;
        }

        .btn-submit, .btn-cancel {
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

        .btn-submit {
            background-color: var(--accent-blue);
            color: var(--primary-white);
        }
        .btn-submit:hover {
            background-color: var(--accent-blue-hover);
        }

        .btn-cancel {
            background-color: var(--text-tertiary);
            color: var(--primary-white);
        }
        .btn-cancel:hover {
            background-color: #7a8086;
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
                    <h1>Edit Pelanggan</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a href="admin/pelanggan">Manajemen Pelanggan</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="{{ url('admin/pelanggan/1/edit') }}">Edit Pelanggan</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <!-- Form dummy (frontend only, tidak konek DB) -->
                <form>
                    <div class="form-group">
                        <label for="nama_pelanggan">Nama Pelanggan</label>
                        <input type="text" id="nama_pelanggan" name="nama_pelanggan" placeholder="Masukkan nama pelanggan" value="Davin Ahmad" required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" placeholder="Masukkan alamat pelanggan" rows="4">Jl. Melati No. 12, Bandung</textarea>
                    </div>

                    <div class="form-group">
                        <label for="no_telepon">Nomor Telepon</label>
                        <input type="text" id="no_telepon" name="no_telepon" placeholder="Masukkan nomor telepon" value="081234567890">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='{{ url('admin/pelanggan') }}'">
                            <i class='bx bx-x'></i> Batal
                        </button>
                        <button type="button" class="btn-submit" onclick="simpanData()">
                            <i class='bx bx-save'></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </section>

    <script>
        function simpanData() {
            alert('Data layanan berhasil diperbarui (dummy).');
            window.location.href = '{{ url('admin/layanan') }}';
        }
    </script>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>
</html>
