<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/form-transaksi.css') }}" />

    <title>Edit Transaksi - Rizhaqi Laundry Admin</title>
    
    <style>
        /* HILANGKAN PANAH INPUT NUMBER */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Edit Transaksi ({{ $transaksi->kode_invoice }})</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Edit Transaksi</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <form id="formTransaksi" action="{{ route('admin.transaksi.update', $transaksi->id_transaksi) }}" method="POST" >
                    @csrf
                    @method('PUT')

                    <!-- BAGIAN 1: PELANGGAN -->
                    <div class="form-group">
                        <label for="pelanggan">Nama Pelanggan</label>
                        <input type="text" list="customer_list" id="pelanggan" name="nama_pelanggan" value="{{ $transaksi->pelanggan->nama }}" required>
                        <datalist id="customer_list">
                            @foreach($pelanggan as $p)
                                <option value="{{ $p->nama }}">{{ $p->telepon }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">Nomor WhatsApp</label>
                        <!-- INPUT NUMBER TANPA PANAH -->
                        <input type="number" id="no_hp" name="no_hp" value="{{ $transaksi->pelanggan->telepon }}" required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat Pelanggan (Opsional)</label>
                        <textarea id="alamat" name="alamat" rows="2">{{ $transaksi->pelanggan->alamat ?? '' }}</textarea>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <!-- BAGIAN 2: LAYANAN (BERTINGKAT) -->
                    <div class="form-group">
                        <label for="kategori_id">Kategori Layanan</label>

                        @php 
                            $kategoriDB = $transaksi->layanan->kategori ?? ''; 
                        @endphp

                        <select id="kategori_id" name="kategori_id" required onchange="updateLayananDropdown()">
                            <option value="">-- Pilih Kategori Layanan --</option>

                            @foreach($layanan->unique('kategori') as $l)
                                @if($l->kategori != 'Add On') 
                                    <option value="{{ $l->kategori }}" {{ $kategoriDB == $l->kategori ? 'selected' : '' }}>
                                        {{ ucfirst($l->kategori) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="layanan_id">Jenis Layanan</label>
                        <select id="layanan_id" name="layanan_id" required onchange="hitungTotal()">
                            <option value="" data-harga="0">-- Pilih Kategori Terlebih Dahulu --</option>
                        </select>
                    </div>

                    <!-- BAGIAN 3: ADD ON -->
                    <div class="form-group">
                        <label>Layanan Tambahan (Add On)</label>
                        <div class="addon-container">
                            
                            @foreach($layanan as $addon)
                                @if($addon->kategori == 'Add On')
                                    @php
                                        // LOGIKA CHECKBOX TERCENTANG
                                        // Sesuaikan logika ini dengan nama kolom di database lu
                                        $isActive = false; 
                                        $qtyVal = '';

                                        if (stripos($addon->nama_layanan, 'Ekspress') !== false && $transaksi->qty_ekspress > 0) {
                                            $isActive = true; $qtyVal = $transaksi->qty_ekspress;
                                        } 
                                        elseif (stripos($addon->nama_layanan, 'Hanger') !== false && $transaksi->qty_hanger > 0) {
                                            $isActive = true; $qtyVal = $transaksi->qty_hanger;
                                        }
                                        elseif (stripos($addon->nama_layanan, 'Plastik') !== false && $transaksi->qty_plastik > 0) {
                                            $isActive = true; $qtyVal = $transaksi->qty_plastik;
                                        }
                                    @endphp

                                    <div class="addon-row">
                                        <label class="addon-label">
                                            <input type="checkbox" class="addon-checkbox" 
                                                   data-id="{{ $addon->id_layanan }}"
                                                   data-harga="{{ $addon->harga_satuan }}"
                                                   data-satuan="{{ strtolower($addon->satuan) }}"
                                                   name="addons[{{ $addon->id_layanan }}][checked]" 
                                                   value="1"
                                                   {{ $isActive ? 'checked' : '' }}>
                                            <span>{{ $addon->nama_layanan }} (+Rp {{ number_format($addon->harga_satuan) }})</span>
                                        </label>

                                        <input type="number" id="qty_addon_{{ $addon->id_layanan }}"
                                               name="addons[{{ $addon->id_layanan }}][qty]"
                                               class="addon-qty {{ strtolower($addon->satuan) == 'kg' ? 'qty-readonly' : '' }}" 
                                               value="{{ $qtyVal }}" placeholder="{{ ucfirst($addon->satuan) }}" 
                                               min="1" {{ $isActive ? '' : 'readonly' }}> 
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <!-- BAGIAN 4: DETAIL -->
                    <div class="form-group">
                        <label class="toggle-detail-wrapper">
                            <input type="checkbox" id="toggleDetail" onchange="toggleDetailSection()" checked>
                            <span><i class='bx bx-list-plus'></i> Isi Rincian Pakaian? (Inventaris)</span>
                        </label>

                        <div id="detail-pakaian-section" style="display: block;">
                            <div class="clothing-grid">
                                <div>
                                    <div class="clothing-item"><label>Kemeja/Baju</label> <input type="number" min="0" step="1" class="qty-input" name="qty_baju" value="3"></div>
                                    <div class="clothing-item"><label>Kaos/T-Shirt</label> <input type="number" min="0" step="1" class="qty-input" name="qty_kaos" value="2"></div>
                                    <div class="clothing-item"><label>Celana Panjang</label> <input type="number" min="0" step="1" class="qty-input" name="qty_celana_panjang" value="5"></div>
                                    <div class="clothing-item"><label>Celana Pendek</label> <input type="number" min="0" step="1" class="qty-input" name="qty_celana_pendek" placeholder="0"></div>
                                </div>
                                <div>
                                    <div class="clothing-item"><label>Jilbab/Kerudung</label> <input type="number" min="0" step="1" class="qty-input" name="qty_jilbab" value="1"></div>
                                    <div class="clothing-item"><label>Jaket/Sweater</label> <input type="number" min="0" step="1" class="qty-input" name="qty_jaket" placeholder="0"></div>
                                    <div class="clothing-item"><label>Kaos Kaki (Psg)</label> <input type="number" min="0" step="1" class="qty-input" name="qty_kaos_kaki" placeholder="0"></div>
                                    <div class="clothing-item"><label>Sarung/Mukena</label> <input type="number" min="0" step="1" class="qty-input" name="qty_sarung" placeholder="0"></div>
                                    <div class="clothing-item"><label>Daleman/Lainnya</label> <input type="number" min="0" step="1" class="qty-input" name="qty_lainnya" placeholder="0"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 5: BERAT & TOTAL -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="berat">Berat (Kg) / Jumlah (Pcs)</label>
                            <!-- INPUT BERAT BULAT (STEP 1) -->
                            <input type="number" id="berat" name="berat" value="" step="1" min="1" required style="font-weight: bold;" oninput="hitungTotal()">
                            <small style="color: var(--text-secondary);">Masukkan angka bulat (tanpa koma).</small>
                        </div>

                        <div class="form-group">
                            <label for="total_biaya">Estimasi Total Biaya (Rp)</label>
                            <input type="text" id="total_biaya_tampil" value="Rp 0" readonly>
                            <input type="hidden" id="total_biaya" name="total_biaya" value="0">
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <!-- BAGIAN 6: STATUS -->
                    <div class="form-group">
                        <label for="tgl_selesai">Estimasi Selesai</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" value="{{ date('Y-m-d', strtotime($transaksi->tgl_selesai)) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="status_bayar">Status Pembayaran</label>
                        <select id="status_bayar" name="status_bayar" required onchange="toggleInputDP()">
                            <option value="belum" {{ $transaksi->status_bayar == 'belum' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="lunas" {{ $transaksi->status_bayar == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="dp"    {{ $transaksi->status_bayar == 'dp' ? 'selected' : '' }}>DP (Uang Muka)</option>
                        </select>
                    </div>

                    <div class="form-group fade-in" id="container_input_dp" 
                         style="{{ $transaksi->status_bayar == 'dp' ? 'display: block;' : 'display: none;' }}">
                        <label style="color: #f57c00;">Nominal DP (Rp)</label>
                        <input type="number" id="jumlah_dp" name="jumlah_dp" value="{{ $transaksi->jumlah_dp ?? 0 }}" min="0">
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Khusus (Opsional)</label>
                        <textarea id="catatan" name="catatan" rows="2">Jangan disetrika terlalu panas.</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='{{ route('admin.transaksi.index') }}'">
                            <i class='bx bx-x'></i> Batal
                        </button>
                        <button type="button" class="btn-submit" id="btnSimpan" onclick="prosesSimpan()">
                            <i class='bx bx-save'></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
    <script src="{{ asset('admin/script/form-transaksi.js') }}"></script>

</body>
</html>