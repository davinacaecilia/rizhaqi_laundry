<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Edit Layanan - Rizhaqi Laundry Admin</title>
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

        .btn-submit:disabled {
            background-color: #ccc;
            cursor: not-allowed;
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
                    <h1>Edit Layanan</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a href="{{ url('admin/layanan') }}">Data Layanan</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Edit Layanan</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <form id="editLayananForm" action="{{ route('admin.layanan.update', $layanan->id_layanan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="kategori_select">Kategori</label>

                        <div style="display: flex; gap: 20px; margin-bottom: 10px;">
                            <label style="display:flex; align-items:center; gap:6px;">
                                <input type="radio" name="kategori_mode" value="existing" checked>
                                Pilih kategori yang sudah ada
                            </label>

                            <label style="display:flex; align-items:center; gap:6px;">
                                <input type="radio" name="kategori_mode" value="new">
                                Buat kategori baru
                            </label>
                        </div>
                        <div id="kategori_container"></div>
                    </div>

                    <div class="form-group">
                        <label for="nama_layanan">Nama Layanan</label>
                        <input type="text" id="nama_layanan" name="nama_layanan" value="{{ old('nama_layanan', $layanan->nama_layanan) }}" required />
                    </div>

                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <select id="satuan" name="satuan">
                            <option value="kg" {{ $layanan->satuan == 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="pcs" {{ $layanan->satuan == 'pcs' ? 'selected' : '' }}>pcs</option>
                            <option value="m2" {{ $layanan->satuan == 'm2' ? 'selected' : '' }}>m²</option>
                        </select>
                    </div>

                    <div class="form-group" style="background: #f8f9fa; padding: 10px; border-radius: 8px; border: 1px solid var(--border-light);">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin:0;">
                            <input type="checkbox" name="is_flexible" id="check-flexible" value="1" {{ $layanan->is_flexible ? 'checked' : '' }}>
                            <span style="font-weight: 600;">Aktifkan Harga Rentang (Min - Max)</span>
                        </label>
                    </div>

                    <div class="form-group" id="group-harga-tetap" style="display: {{ $layanan->is_flexible ? 'none' : 'block' }}">
                        <label for="harga_satuan">Harga Satuan</label>
                        <input type="number" id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan', $layanan->harga_satuan) }}" />
                    </div>

                    <div id="group-harga-flexible" style="display: {{ $layanan->is_flexible ? 'block' : 'none' }}">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label for="harga_min">Harga Minimum</label>
                                <input type="number" id="harga_min" name="harga_min" value="{{ old('harga_min', $layanan->harga_min) }}" placeholder="Min">
                            </div>
                            <div class="form-group">
                                <label for="harga_max">Harga Maksimum</label>
                                <input type="number" id="harga_max" name="harga_max" value="{{ old('harga_max', $layanan->harga_max) }}" placeholder="Max">
                            </div>
                        </div>
                        <small id="error-range" style="color: #dc2626; display: none;">Harga Minimum tidak boleh lebih besar dari Maksimum!</small>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='{{ route('admin.layanan.index') }}'">
                            <i class='bx bx-x'></i> Batal
                        </button>
                        <button type="submit" class="btn-submit" id="btn-simpan">
                            <i class='bx bx-save'></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- 1. LOGIC KATEGORI ---
            const radios = document.querySelectorAll('input[name="kategori_mode"]');
            const container = document.getElementById('kategori_container');
            
            // Simpan opsi select dalam variabel JS (menggunakan PHP blade loop)
            const existingOptions = `
                <select id="kategori_select" name="kategori">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori_list as $kat)
                        <option value="{{ $kat }}" {{ $layanan->kategori == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>`;
            
            function updateKategoriInput() {
                const selected = document.querySelector('input[name="kategori_mode"]:checked');
                if (selected && selected.value === "existing") {
                    container.innerHTML = existingOptions;
                } else {
                    container.innerHTML = `<input type="text" id="kategori_baru" name="kategori_baru" placeholder="Masukkan kategori baru">`;
                }
            }
            
            radios.forEach(r => r.addEventListener('change', updateKategoriInput));
            // Jalankan sekali saat load agar terisi
            updateKategoriInput();


            // --- 2. LOGIC HARGA RENTANG & VALIDASI ---
            const checkFlex = document.getElementById('check-flexible');
            const groupTetap = document.getElementById('group-harga-tetap');
            const groupFlex = document.getElementById('group-harga-flexible');
            
            // Input elements
            const inputTetap = document.getElementById('harga_satuan');
            const inputMin = document.getElementById('harga_min');
            const inputMax = document.getElementById('harga_max');
            
            const btnSimpan = document.getElementById('btn-simpan');
            const errorRange = document.getElementById('error-range');

            function toggleInputs() {
                if (checkFlex.checked) {
                    // Mode Flexible
                    groupTetap.style.display = 'none';
                    groupFlex.style.display = 'block';
                    
                    // Reset required
                    inputTetap.removeAttribute('required');
                    inputMin.setAttribute('required', true);
                    inputMax.setAttribute('required', true);
                } else {
                    // Mode Tetap
                    groupTetap.style.display = 'block';
                    groupFlex.style.display = 'none';
                    
                    inputTetap.setAttribute('required', true);
                    inputMin.removeAttribute('required');
                    inputMax.removeAttribute('required');
                    
                    // Reset error visual saat kembali ke mode tetap
                    btnSimpan.disabled = false;
                    errorRange.style.display = 'none';
                }
            }

            function validateRange() {
                const minVal = parseFloat(inputMin.value) || 0;
                const maxVal = parseFloat(inputMax.value) || 0;

                // Hanya validasi jika kedua input terisi
                if (inputMin.value && inputMax.value) {
                    if (minVal >= maxVal) {
                        errorRange.style.display = 'block';
                        btnSimpan.disabled = true;
                        inputMin.style.borderColor = '#dc2626';
                        inputMax.style.borderColor = '#dc2626';
                    } else {
                        errorRange.style.display = 'none';
                        btnSimpan.disabled = false;
                        inputMin.style.borderColor = 'var(--border-light)';
                        inputMax.style.borderColor = 'var(--border-light)';
                    }
                } else {
                    btnSimpan.disabled = false;
                }
            }

            checkFlex.addEventListener('change', toggleInputs);
            inputMin.addEventListener('input', validateRange);
            inputMax.addEventListener('input', validateRange);
        });
    </script>

</body>
</html>