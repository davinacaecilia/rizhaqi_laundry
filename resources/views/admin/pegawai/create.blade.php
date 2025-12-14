<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    
    <title>Tambah Pegawai - Rizhaqi Laundry Admin</title>
    
    <style>
        /* CSS FORM (Kita tambah ini aja, ga ganggu CSS tabel) */
        .form-card {
            background: var(--primary-white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-light);
            max-width: 600px; /* Lebih kecil dari form transaksi biar pas */
            margin: 24px auto;
        }

        .form-group { margin-bottom: 20px; position: relative; }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            font-family: var(--roboto);
            font-size: 14px;
            background: var(--surface-white);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
        }

        /* Tombol Mata (Show Password) */
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 20px;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            border-top: 1px solid var(--border-light);
            padding-top: 20px;
        }

        .btn-submit {
            background-color: var(--accent-blue);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex; align-items: center; gap: 8px;
        }
        
        .btn-cancel {
            background-color: #e0e0e0;
            color: #424242;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
        }
        
        .btn-submit:hover { background-color: var(--accent-blue-hover); }
        .btn-cancel:hover { background-color: #d6d6d6; }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Tambah Pegawai Baru</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.pegawai.index') }}">Data Pegawai</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Tambah</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <form id="formPegawai" action="{{ route('admin.pegawai.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" placeholder="Contoh: Siti Aminah" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="email">Email (Username Login)</label>
                        <input type="email" id="email" name="email" placeholder="Contoh: siti@rizhaqi.com" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="role">Jabatan / Role</label>
                        <select id="role" name="role" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="pegawai">Pegawai (Karyawan)</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">Password Default</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password..." required>
                        <i class='bx bx-hide toggle-password' onclick="togglePassword()"></i>
                    </div>

                    <input type="hidden" name="is_active" value="1">

                    <div class="form-actions">
                        <a href="{{ route('admin.pegawai.index') }}" class="btn-cancel">
                            <i class='bx bx-x'></i> Batal
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class='bx bx-save'></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

    <script>
        // Fitur Show/Hide Password
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            } else {
                input.type = "password";
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            }
        }
    </script>

</body>
</html>