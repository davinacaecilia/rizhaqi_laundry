<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/form-transaksi.css') }}" />

    <title>Tambah Order Baru - Rizhaqi Laundry Admin</title>
    
    <style>
        /* CSS TAMBAHAN: HILANGKAN PANAH DI INPUT NUMBER */
        /* Chrome, Safari, Edge, Opera */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
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
                    <h1>Tambah Order Baru</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Input Order</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <form id="formTransaksi" action="{{ route('admin.transaksi.store') }}" method="POST" data-redirect-url="{{ route('admin.transaksi.index') }}">
                    @csrf

                    <!-- BAGIAN 1: DATA PELANGGAN -->
                    <div class="form-group">
                        <label for="pelanggan">Nama Pelanggan</label>
                        <input type="text" list="customer_list" id="pelanggan" name="nama_pelanggan" placeholder="Ketik nama pelanggan..." autocomplete="off" required>
                        <datalist id="customer_list">
                            <option value="Budi Santoso">08123456789</option>
                            <option value="Ani Wijaya">08134567890</option>
                            <option value="Citra Lestari">08571234567</option>
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">Nomor WhatsApp</label>
                        <!-- Type number tetap agar keyboard angka muncul di HP, tapi panah dihilangkan via CSS -->
                        <input type="number" id="no_hp" name="no_hp" placeholder="Contoh: 0812..." required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat Pelanggan (Opsional)</label>
                        <textarea id="alamat" name="alamat" placeholder="Contoh: Jl. Merpati No. 1..." rows="2"></textarea>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <!-- BAGIAN 2: PILIH LAYANAN (CASCADING) -->
                    <div class="form-group">
                        <label for="kategori_id">Kategori Layanan</label>
                        <select id="kategori_id" name="kategori_id" required onchange="updateLayananDropdown()">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="kiloan">Regular Services (Kiloan)</option>
                            <option value="promo_jumat">PROMO: Jumat Berkah</option>
                            <option value="promo_selasa">PROMO: Selasa Ceria</option>
                            <option value="paket">Package Services (Borongan)</option>
                            <option value="satuan">Cuci Satuan (Pcs)</option>
                            <option value="karpet">Cuci Karpet</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="layanan_id">Jenis Layanan</label>
                        <select id="layanan_id" name="layanan_id" required disabled onchange="hitungTotal()">
                            <option value="" data-harga="0">-- Pilih Kategori Terlebih Dahulu --</option>
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
                                <!-- Step 1 agar bulat -->
                                <input type="number" id="qty_ekspress" class="addon-qty" placeholder="Kg" readonly step="1">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger" data-harga="3000" onchange="toggleAddonQty(this, 'qty_hanger')">
                                    <span>Hanger (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger" class="addon-qty" placeholder="Pcs" min="1" value="1" oninput="hitungTotal()">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_plastik" data-harga="3000" onchange="toggleAddonQty(this, 'qty_plastik')">
                                    <span>Plastik (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_plastik" class="addon-qty" placeholder="Pcs" min="1" value="1" oninput="hitungTotal()">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger_plastik" data-harga="5000" onchange="toggleAddonQty(this, 'qty_hanger_plastik')">
                                    <span>Hanger + Plastik (+Rp 5.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger_plastik" class="addon-qty" placeholder="Pcs" min="1" value="1" oninput="hitungTotal()">
                            </div>
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <!-- BAGIAN 4: DETAIL PAKAIAN -->
                    <div class="form-group">
                        <label class="toggle-detail-wrapper">
                            <input type="checkbox" id="toggleDetail" onchange="toggleDetailSection()">
                            <span><i class='bx bx-list-plus'></i> Isi Rincian Pakaian? (Inventaris)</span>
                        </label>
                        <small style="display:block; margin-top:5px; color:var(--text-tertiary);">Data ini hanya untuk catatan nota (tidak mempengaruhi harga kiloan).</small>

                        <div id="detail-pakaian-section">
                            <div class="clothing-grid">
                                <div>
                                    <div class="clothing-item"><label>Kemeja/Baju</label> <input type="number" min="0" class="qty-input" name="qty_baju" placeholder="0"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Kaos/T-Shirt</label> <input type="number" min="0" class="qty-input" name="qty_kaos" placeholder="0"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Celana Panjang</label> <input type="number" min="0" class="qty-input" name="qty_celana_panjang" placeholder="0"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Celana Pendek</label> <input type="number" min="0" class="qty-input" name="qty_celana_pendek" placeholder="0"></div>
                                </div>
                                <div>
                                    <div class="clothing-item"><label>Jilbab/Kerudung</label> <input type="number" min="0" class="qty-input" name="qty_jilbab" placeholder="0"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Jaket/Sweater</label> <input type="number" min="0" class="qty-input" name="qty_jaket" placeholder="0"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Kaos Kaki (Psg)</label> <input type="number" min="0" class="qty-input" name="qty_kaos_kaki" placeholder="0"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Sarung/Mukena</label> <input type="number" min="0" class="qty-input" name="qty_sarung" placeholder="0"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Daleman/Lainnya</label> <input type="number" min="0" class="qty-input" name="qty_lainnya" placeholder="0"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 5: INPUT BERAT & TOTAL -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="berat">Berat (Kg) / Jumlah (Pcs)</label>
                            <!-- REVISI: step="1" biar bilangan bulat -->
                            <input type="number" id="berat" name="berat" placeholder="1" step="1" min="1" required style="font-weight: bold;" oninput="hitungTotal()">
                            <small style="color: var(--text-secondary);">Masukkan angka bulat (tanpa koma).</small>
                        </div>

                        <div class="form-group">
                            <label for="total_biaya">Estimasi Total Biaya (Rp)</label>
                            <input type="text" id="total_biaya_tampil" value="Rp 0" readonly>
                            <input type="hidden" id="total_biaya" name="total_biaya" value="0">
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <div class="form-group">
                        <label for="tgl_selesai">Estimasi Selesai</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" required>
                    </div>

                    <div class="form-group">
                        <label for="status_bayar">Status Pembayaran</label>
                        <select id="status_bayar" name="status_bayar" required onchange="toggleInputDP()">
                            <option value="belum">Belum Bayar (Bayar Nanti)</option>
                            <option value="lunas">Lunas (Bayar Sekarang)</option>
                            <option value="dp">DP (Uang Muka)</option>
                        </select>
                    </div>

                    <div class="form-group fade-in" id="container_input_dp" style="display: none;">
                        <label for="jumlah_dp" style="color: #f57c00;">Nominal DP (Rp)</label>
                        <input type="number" id="jumlah_dp" name="jumlah_dp" placeholder="Masukkan nominal uang muka" min="0">
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Khusus (Opsional)</label>
                        <textarea id="catatan" name="catatan" placeholder="Contoh: Jangan pakai pewangi..." rows="2"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='{{ route('admin.transaksi.index') }}'">
                            <i class='bx bx-x'></i> Batal
                        </button>
                        <button type="button" class="btn-submit" id="btnSimpan" onclick="prosesSimpan()">
                            <i class='bx bx-save'></i> Simpan Transaksi
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