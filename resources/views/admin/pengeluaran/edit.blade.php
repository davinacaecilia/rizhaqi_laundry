<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />

    <title>Edit Pengeluaran - Rizhaqi Laundry</title>

    <style>
        /* STYLE FORM KONSISTEN */
        .form-card {
            background: var(--primary-white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-light);
            max-width: 600px;
            margin: 24px auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            font-family: var(--roboto);
            font-size: 14px;
            outline: none;
            background: var(--surface-white);
            color: var(--text-primary);
        }

        .form-group input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            border-top: 1px solid var(--border-light);
            padding-top: 20px;
        }

        .btn-submit {
            background: var(--accent-blue);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--accent-blue-hover);
        }

        .btn-cancel {
            background: #e0e0e0;
            color: #424242;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel:hover {
            background: #d6d6d6;
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
                    <h1>Edit Data Pengeluaran</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a href="{{ route('admin.pengeluaran.index') }}">Pengeluaran</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Edit</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <!-- Method PUT untuk Update -->
                <form id="formPengeluaran" action="{{ route('admin.pengeluaran.update', $data->id_pengeluaran) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- TANGGAL -->
                    <div class="form-group">
                        <label>Tanggal Pengeluaran</label>
                        <input type="date" name="tanggal" required value="{{ old('tanggal', $data->tanggal) }}">
                    </div>

                    <!-- KETERANGAN -->
                    <div class="form-group">
                        <label>Keterangan / Nama Barang</label>
                        <input type="text" name="keterangan" value="{{ old('keterangan', $data->keterangan) }}" required
                            autocomplete="off">
                    </div>

                    <!-- JUMLAH -->
                    <div class="form-group">
                        <label>Total Biaya (Rp)</label>
                        <input type="number" name="jumlah" value="{{ old('jumlah', $data->jumlah) }}" min="0" required>
                    </div>
                    <div class="form-actions">
                        <a href="{{ route('admin.pengeluaran.index') }}" class="btn-cancel">
                            <i class='bx bx-x'></i> Batal
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class='bx bx-save'></i> Simpan Perubahan
                        </button>
                    </div>
            </div>

            </form>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

    <script>
        function updatePengeluaran() {
            const form = document.getElementById('formPengeluaran');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const btn = document.querySelector('.btn-submit');
            btn.disabled = true;
            btn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Menyimpan...";

            setTimeout(() => {
                alert("Sukses! Data pengeluaran berhasil diperbarui.");
                window.location.href = "{{ route('admin.pengeluaran.index') }}";
            }, 1000);
        }
    </script>

</body>

</html>