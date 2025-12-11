<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Edit Pelanggan - Rizhaqi Laundry Admin</title>
    <style>
        /* CSS HILANGKAN PANAH NUMBER */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        .form-card {
            background: var(--primary-white);
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-light);
            max-width: 800px;
            margin: 24px auto;
        }

        .form-group { margin-bottom: 16px; }

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

        .invalid-feedback { color: #dc3545; font-size: 12px; margin-top: 5px; }
        .is-invalid { border-color: #dc3545 !important; }

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
            text-decoration: none; /* Biar link batal gak ada garis bawah */
        }

        .btn-submit { background-color: var(--accent-blue); color: var(--primary-white); }
        .btn-submit:hover { background-color: var(--accent-blue-hover); }

        .btn-cancel { background-color: var(--text-tertiary); color: var(--primary-white); }
        .btn-cancel:hover { background-color: #7a8086; }
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
                        <li><a href="{{ route('admin.pelanggan.index') }}">Manajemen Pelanggan</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Edit Pelanggan</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <form action="{{ route('admin.pelanggan.update', $pelanggan->id_pelanggan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nama">Nama Pelanggan</label>
                        <input type="text" id="nama" name="nama" 
                               class="@error('nama') is-invalid @enderror"
                               placeholder="Masukkan nama pelanggan" 
                               value="{{ old('nama', $pelanggan->nama) }}" required>
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="telepon">Nomor Telepon / WhatsApp</label>
                        <input type="number" id="telepon" name="telepon" 
                               class="@error('telepon') is-invalid @enderror"
                               placeholder="Masukkan nomor telepon" 
                               value="{{ old('telepon', $pelanggan->telepon) }}" required>
                        @error('telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" 
                                  class="@error('alamat') is-invalid @enderror"
                                  placeholder="Masukkan alamat pelanggan" rows="4">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                        @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.pelanggan.index') }}" class="btn-cancel">
                            <i class='bx bx-x'></i> Batal
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class='bx bx-save'></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>
</html>