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
                    <h1>Edit Transaksi (TRX-001)</h1>
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
                <form id="formTransaksi" action="{{ route('admin.transaksi.update', 1) }}" method="POST" data-redirect-url="{{ route('admin.transaksi.index') }}">
                    @csrf
                    @method('PUT')

                    <!-- BAGIAN 1: PELANGGAN -->
                    <div class="form-group">
                        <label for="pelanggan">Nama Pelanggan</label>
                        <input type="text" list="customer_list" id="pelanggan" name="nama_pelanggan" value="Budi Santoso" required>
                        <datalist id="customer_list">
                            <option value="Budi Santoso">08123456789</option>
                            <option value="Ani Wijaya">08134567890</option>
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">Nomor WhatsApp</label>
                        <!-- INPUT NUMBER TANPA PANAH -->
                        <input type="number" id="no_hp" name="no_hp" value="08123456789" required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat Pelanggan (Opsional)</label>
                        <textarea id="alamat" name="alamat" rows="2">Jl. Merpati No. 1, Medan</textarea>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <!-- BAGIAN 2: LAYANAN (BERTINGKAT) -->
                    <div class="form-group">
                        <label for="kategori_id">Kategori Layanan</label>
                        <select id="kategori_id" name="kategori_id" required onchange="updateLayananDropdown()">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="kiloan" selected>Regular Services (Kiloan)</option>
                            <option value="promo_jumat">PROMO: Jumat Berkah</option>
                            <option value="promo_selasa">PROMO: Selasa Ceria</option>
                            <option value="paket">Package Services (Borongan)</option>
                            <option value="satuan">Cuci Satuan (Pcs)</option>
                            <option value="karpet">Cuci Karpet</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="layanan_id">Jenis Layanan</label>
                        <select id="layanan_id" name="layanan_id" required onchange="hitungTotal()">
                            <!-- Option Dummy Terpilih -->
                            <option value="1" data-harga="10000" selected>Cuci Kering Setrika Pakaian (Rp 10.000)</option>
                        </select>
                    </div>

                    <!-- BAGIAN 3: ADD ON -->
                    <div class="form-group">
                        <label>Layanan Tambahan (Add On)</label>
                        <div class="addon-container">
                            
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_ekspress" data-harga="5000" onchange="toggleAddonQty(this, 'qty_ekspress')">
                                    <span>Layanan Ekspress (+Rp 5.000/kg)</span>
                                </label>
                                <input type="number" id="qty_ekspress" class="addon-qty" placeholder="Kg" readonly step="1">
                            </div>

                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger" data-harga="3000" onchange="toggleAddonQty(this, 'qty_hanger')" checked>
                                    <span>Hanger (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger" class="addon-qty" style="display:block;" value="5" min="1" step="1" oninput="hitungTotal()">
                            </div>

                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_plastik" data-harga="3000" onchange="toggleAddonQty(this, 'qty_plastik')">
                                    <span>Plastik (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_plastik" class="addon-qty" placeholder="Pcs" min="1" step="1" value="1" oninput="hitungTotal()">
                            </div>

                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger_plastik" data-harga="5000" onchange="toggleAddonQty(this, 'qty_hanger_plastik')">
                                    <span>Hanger + Plastik (+Rp 5.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger_plastik" class="addon-qty" placeholder="Pcs" min="1" step="1" value="1" oninput="hitungTotal()">
                            </div>
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
                            <input type="number" id="berat" name="berat" value="5" step="1" min="1" required style="font-weight: bold;" oninput="hitungTotal()">
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
                        <input type="date" id="tgl_selesai" name="tgl_selesai" value="2025-11-26" required>
                    </div>

                    <div class="form-group">
                        <label for="status_bayar">Status Pembayaran</label>
                        <select id="status_bayar" name="status_bayar" required onchange="toggleInputDP()">
                            <option value="belum">Belum Bayar</option>
                            <option value="lunas">Lunas</option>
                            <option value="dp" selected>DP (Uang Muka)</option>
                        </select>
                    </div>

                    <div class="form-group fade-in" id="container_input_dp" style="display: block;">
                        <label for="jumlah_dp" style="color: #f57c00;">Nominal DP (Rp)</label>
                        <input type="number" id="jumlah_dp" name="jumlah_dp" value="20000" min="0">
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