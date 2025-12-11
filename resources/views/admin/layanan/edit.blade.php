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
        /* CSS TIDAK DIUBAH SAMA SEKALI */
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

        .form-group input[type="text"], 
        .form-group input[type="number"], 
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

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { 
            border-color: var(--accent-blue); 
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1); 
        }

        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

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
            text-decoration: none;
        }

        .btn-submit { background-color: var(--accent-blue); color: var(--primary-white); }
        .btn-submit:hover { background-color: var(--accent-blue-hover); }
        .btn-submit:disabled { background-color: #ccc; cursor: not-allowed; }

        .btn-cancel { background-color: var(--text-tertiary); color: var(--primary-white); }
        .btn-cancel:hover { background-color: #7a8086; }

        .error-msg { color: #dc2626; font-size: 12px; margin-top: 5px; display: none; }
        .invalid-feedback { color: #dc2626; font-size: 12px; margin-top: 5px; }
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
                        <li><a href="{{ route('admin.layanan.index') }}">Data Layanan</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Edit Layanan</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <form action="{{ route('admin.layanan.update', $layanan->id_layanan) }}" method="POST" id="formLayanan">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label>Kategori <span style="color:red">*</span></label>
                        
                        <div style="display: flex; gap: 20px; margin-bottom: 10px;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px;">
                                <input type="radio" name="kategori_mode" value="existing" checked> Pilih dari daftar
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px;">
                                <input type="radio" name="kategori_mode" value="new"> Buat kategori baru
                            </label>
                        </div>

                        <div id="container-kat-existing">
                            <select name="kategori_existing" id="input-kat-existing">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategori_list as $kat)
                                    <option value="{{ $kat }}" {{ $layanan->kategori == $kat ? 'selected' : '' }}>
                                        {{ $kat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="container-kat-new" style="display: none;">
                            <input type="text" name="kategori_baru" id="input-kat-new" placeholder="Ketik nama kategori baru">
                        </div>
                        
                        @error('kategori_final') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label>Nama Layanan <span style="color:red">*</span></label>
                        <input type="text" name="nama_layanan" value="{{ old('nama_layanan', $layanan->nama_layanan) }}" required>
                        @error('nama_layanan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label>Satuan <span style="color:red">*</span></label>
                        <select name="satuan" required>
                            <option value="kg" {{ $layanan->satuan == 'kg' ? 'selected' : '' }}>Kg</option>
                            <option value="pcs" {{ $layanan->satuan == 'pcs' ? 'selected' : '' }}>Pcs</option>
                            <option value="m2" {{ $layanan->satuan == 'm2' ? 'selected' : '' }}>m²</option>
                        </select>
                    </div>

                    @php
                        // Deteksi: Jika harga_maksimum tidak NULL, berarti Harga Rentang
                        $isFlexible = !is_null($layanan->harga_maksimum);
                    @endphp

                    <div class="form-group" style="background: #f8f9fa; padding: 10px; border-radius: 8px; border: 1px solid var(--border-light);">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin:0;">
                            <input type="checkbox" name="is_flexible" id="check-flexible" value="1" {{ $isFlexible ? 'checked' : '' }}>
                            <strong>Aktifkan Harga Rentang (Min - Max)</strong>
                        </label>
                    </div>

                    <div class="form-group" id="group-harga-tetap">
                        <label>Harga Satuan (Rp) <span style="color:red">*</span></label>
                        <input type="number" name="harga_tetap" id="input-harga-tetap" 
                               value="{{ $isFlexible ? '' : $layanan->harga_satuan }}" 
                               placeholder="Contoh: 15000">
                    </div>

                    <div id="group-harga-flexible" style="display: none;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label>Harga Minimum (Rp) <span style="color:red">*</span></label>
                                <input type="number" name="harga_min" id="input-min" 
                                       value="{{ $isFlexible ? $layanan->harga_satuan : '' }}" 
                                       placeholder="Contoh: 20000">
                            </div>
                            <div class="form-group">
                                <label>Harga Maksimum (Rp) <span style="color:red">*</span></label>
                                <input type="number" name="harga_max" id="input-max" 
                                       value="{{ $isFlexible ? $layanan->harga_maksimum : '' }}" 
                                       placeholder="Contoh: 35000">
                            </div>
                        </div>
                        <div id="error-range" class="error-msg">
                            Harga Minimum tidak boleh lebih besar dari Harga Maksimum!
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.layanan.index') }}" class="btn-cancel">Batal</a>
                        <button type="submit" class="btn-submit" id="btn-simpan">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ELEMENT SELECTORS
            const radiosKat = document.querySelectorAll('input[name="kategori_mode"]');
            const boxExisting = document.getElementById('container-kat-existing');
            const boxNew = document.getElementById('container-kat-new');
            const inputExisting = document.getElementById('input-kat-existing');
            const inputNew = document.getElementById('input-kat-new');

            const checkFlex = document.getElementById('check-flexible');
            const groupTetap = document.getElementById('group-harga-tetap');
            const inputTetap = document.getElementById('input-harga-tetap');
            const groupFlex = document.getElementById('group-harga-flexible');
            const inputMin = document.getElementById('input-min');
            const inputMax = document.getElementById('input-max');
            const errorMsg = document.getElementById('error-range');
            const btnSimpan = document.getElementById('btn-simpan');

            // --- 1. LOGIC KATEGORI (TOGGLE) ---
            function toggleKategori() {
                const mode = document.querySelector('input[name="kategori_mode"]:checked').value;
                if (mode === 'existing') {
                    boxExisting.style.display = 'block';
                    boxNew.style.display = 'none';
                    inputExisting.required = true;
                    inputNew.required = false;
                } else {
                    boxExisting.style.display = 'none';
                    boxNew.style.display = 'block';
                    inputExisting.required = false;
                    inputNew.required = true;
                }
            }
            radiosKat.forEach(r => r.addEventListener('change', toggleKategori));
            toggleKategori(); // Run on load

            // --- 2. LOGIC HARGA (TOGGLE) ---
            function toggleHarga() {
                if (checkFlex.checked) {
                    // Mode Rentang
                    groupTetap.style.display = 'none';
                    groupFlex.style.display = 'block';
                    
                    inputTetap.required = false;
                    inputMin.required = true;
                    inputMax.required = true;
                } else {
                    // Mode Tetap
                    groupTetap.style.display = 'block';
                    groupFlex.style.display = 'none';

                    inputTetap.required = true;
                    inputMin.required = false;
                    inputMax.required = false;
                    
                    errorMsg.style.display = 'none';
                    btnSimpan.disabled = false;
                }
            }

            checkFlex.addEventListener('change', toggleHarga);
            toggleHarga(); // PENTING: Run on load agar form menyesuaikan data DB saat dibuka

            // --- 3. VALIDASI HARGA ---
            function validateRange() {
                const min = parseFloat(inputMin.value) || 0;
                const max = parseFloat(inputMax.value) || 0;

                if (checkFlex.checked && inputMin.value && inputMax.value) {
                    if (min >= max) {
                        errorMsg.style.display = 'block';
                        btnSimpan.disabled = true;
                    } else {
                        errorMsg.style.display = 'none';
                        btnSimpan.disabled = false;
                    }
                }
            }
            inputMin.addEventListener('input', validateRange);
            inputMax.addEventListener('input', validateRange);
        });
    </script>
</body>
</html>