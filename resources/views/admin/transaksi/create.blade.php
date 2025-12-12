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
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
        /* Style buat input harga kalau dilock/unlock */
        .locked-input { background-color: #e9ecef; cursor: not-allowed; }
        .unlocked-input { background-color: #fff; border: 1px solid #007bff; }
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

                    <div class="form-group">
                        <label for="pelanggan">Nama Pelanggan</label>
                        <input type="text" list="customer_list" id="pelanggan" name="nama_pelanggan" placeholder="Ketik nama pelanggan..." autocomplete="off" required>
                        <datalist id="customer_list">
                            {{-- DATA PELANGGAN DARI DB --}}
                            @foreach($pelanggan as $cust)
                                <option value="{{ $cust->nama }}">{{ $cust->telepon }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">Nomor WhatsApp</label>
                        <input type="number" id="no_hp" name="no_hp" placeholder="Contoh: 0812..." required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat Pelanggan (Opsional)</label>
                        <textarea id="alamat" name="alamat" placeholder="Contoh: Jl. Merpati No. 1..." rows="2"></textarea>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <div class="form-group">
                        <label for="kategori_id">Kategori Layanan</label>
                        <select id="kategori_id" name="kategori_id" required onchange="updateLayananDropdown()">
                            <option value="">-- Pilih Kategori --</option>
                            {{-- LOOPING KATEGORI DARI DB --}}
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->kategori }}">{{ $kat->kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="layanan_id">Jenis Layanan</label>
                        <select id="layanan_id" name="layanan_id" required disabled onchange="setHargaOtomatis()">
                            <option value="" data-harga="0">-- Pilih Kategori Terlebih Dahulu --</option>
                            
                            {{-- LOAD SEMUA LAYANAN TAPI DISEMBUNYIKAN JS DULU --}}
                            @foreach($layanan as $item)
                                <option value="{{ $item->id_layanan }}" 
                                        data-kategori="{{ $item->kategori }}"
                                        data-tipe="{{ $item->is_flexible ? 'range' : 'fixed' }}"
                                        data-harga="{{ $item->harga_satuan }}"
                                        data-min="{{ $item->harga_min }}"
                                        data-max="{{ $item->harga_max }}">
                                    {{ $item->nama_layanan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="harga_satuan">Harga Satuan / Per Item (Rp)</label>
                        <input type="number" id="harga_satuan" name="harga_satuan" 
                               placeholder="0" required 
                               oninput="hitungTotal()" 
                               class="locked-input" readonly>
                        <small id="info_range" style="color: blue; display: none; margin-top: 5px;"></small>
                    </div>

                    <div class="form-group">
                        <label>Layanan Tambahan (Add On)</label>
                        <div class="addon-container">
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_ekspress" name="addon_ekspress" data-harga="5000" onchange="toggleAddonQty(this, 'qty_ekspress')">
                                    <span>Layanan Ekspress (+Rp 5.000/kg)</span>
                                </label>
                                <input type="number" id="qty_ekspress" name="qty_ekspress" class="addon-qty" placeholder="Kg" readonly step="1">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger" name="addon_hanger" data-harga="3000" onchange="toggleAddonQty(this, 'qty_hanger')">
                                    <span>Hanger (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger" name="qty_hanger" class="addon-qty" placeholder="Pcs" min="1" value="1" oninput="hitungTotal()">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_plastik" name="addon_plastik" data-harga="3000" onchange="toggleAddonQty(this, 'qty_plastik')">
                                    <span>Plastik (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_plastik" name="qty_plastik" class="addon-qty" placeholder="Pcs" min="1" value="1" oninput="hitungTotal()">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger_plastik" name="addon_hanger_plastik" data-harga="5000" onchange="toggleAddonQty(this, 'qty_hanger_plastik')">
                                    <span>Hanger + Plastik (+Rp 5.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger_plastik" name="qty_hanger_plastik" class="addon-qty" placeholder="Pcs" min="1" value="1" oninput="hitungTotal()">
                            </div>
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    <div class="form-group">
                        <label class="toggle-detail-wrapper">
                            <input type="checkbox" id="toggleDetail" name="toggleDetail" onchange="toggleDetailSection()">
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="berat">Berat (Kg) / Jumlah (Pcs)</label>
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

    <script>
        // FUNGSI UTAMA: Mengatur Status Input Harga (Dipanggil saat dropdown berubah)
        function setHargaOtomatis() {
            const select = document.getElementById('layanan_id');
            const inputHarga = document.getElementById('harga_satuan');
            const infoRange = document.getElementById('info_range');
            
            if (!select || !inputHarga) return;

            const selectedOption = select.options[select.selectedIndex];
            const tipe = selectedOption.getAttribute('data-tipe');
            const harga = selectedOption.getAttribute('data-harga');
            const min = selectedOption.getAttribute('data-min');
            const max = selectedOption.getAttribute('data-max');

            // Reset dulu class dan atribut
            inputHarga.classList.remove('locked-input', 'unlocked-input');
            infoRange.style.display = 'none';

            if (tipe === 'range') {
                // KASUS 1: HARGA RENTANG (BISA DIEDIT)
                inputHarga.readOnly = false;
                inputHarga.value = ""; // Kosongkan biar user isi sendiri (atau isi min kalau mau)
                inputHarga.placeholder = `Min: ${formatRupiah(min)}`;
                
                // Ubah style jadi putih & kursor teks
                inputHarga.style.backgroundColor = "#fff";
                inputHarga.style.cursor = "text"; 
                inputHarga.classList.add('unlocked-input');

                // Tampilkan info range
                infoRange.style.display = 'block';
                infoRange.textContent = `*Harga Fleksibel: Rp ${formatRupiah(min)} - Rp ${formatRupiah(max)}`;
            } else {
                // KASUS 2: HARGA FIX (TERKUNCI)
                inputHarga.readOnly = true;
                inputHarga.value = harga;
                
                // Ubah style jadi abu-abu & kursor not-allowed
                inputHarga.style.backgroundColor = "#e9ecef";
                inputHarga.style.cursor = "not-allowed";
                inputHarga.classList.add('locked-input');
            }
            
            hitungTotal(); // Hitung ulang total biaya
        }

        // Helper format rupiah sederhana
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // Event Listener saat halaman dimuat (Khusus Edit)
        document.addEventListener('DOMContentLoaded', function() {
            // Jalankan sekali saat load agar status awal benar
            const select = document.getElementById('layanan_id');
            if(select && !select.disabled && select.value !== "") {
                setHargaOtomatis();
                
                // Khusus Edit: Kembalikan nilai harga lama jika ada
                @if(isset($hargaLama))
                    const inputHarga = document.getElementById('harga_satuan');
                    if(inputHarga) inputHarga.value = "{{ $hargaLama }}";
                @endif
            }
        });
    </script>
</body>
</html>