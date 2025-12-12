<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/form-transaksi.css') }}" />

    <title>Edit Order ({{ $transaksi->kode_invoice }}) - Rizhaqi Laundry</title>
    
    <style>
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
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
                    <h1>Edit Order ({{ $transaksi->kode_invoice }})</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Edit Order</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                {{-- PERBAIKAN: Form Action mengarah ke route update yang benar --}}
                <form id="formTransaksi" action="{{ route('admin.transaksi.update', $transaksi->id_transaksi) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="pelanggan">Nama Pelanggan</label>
                        <input type="text" list="customer_list" id="pelanggan" name="nama_pelanggan" 
                               value="{{ $transaksi->pelanggan->nama }}" 
                               placeholder="Ketik nama pelanggan..." autocomplete="off" required>
                        <datalist id="customer_list">
                            @foreach($pelanggan as $cust)
                                <option value="{{ $cust->nama }}">{{ $cust->telepon }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">Nomor WhatsApp</label>
                        <input type="number" id="no_hp" name="no_hp" 
                               value="{{ $transaksi->pelanggan->telepon }}" 
                               placeholder="Contoh: 0812..." required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat Pelanggan (Opsional)</label>
                        <textarea id="alamat" name="alamat" placeholder="Contoh: Jl. Merpati No. 1..." rows="2">{{ $transaksi->pelanggan->alamat }}</textarea>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    @php
                        // Ambil data lama
                        $mainDetail = $transaksi->detailTransaksi->first(); 
                        $kategoriLama = $mainDetail && $mainDetail->layanan ? $mainDetail->layanan->kategori : '';
                        $layananLamaId = $mainDetail ? $mainDetail->id_layanan : '';
                        $hargaLama = $mainDetail ? $mainDetail->harga_saat_transaksi : 0;
                    @endphp

                    <div class="form-group">
                        <label for="kategori_id">Kategori Layanan</label>
                        {{-- PERBAIKAN: ID DIUBAH JADI kategori_id AGAR SESUAI DENGAN JS --}}
                        <select id="kategori_id" class="form-control" onchange="updateLayananDropdown()">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->kategori }}" {{ $kategoriLama == $kat->kategori ? 'selected' : '' }}>
                                    {{ $kat->kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="layanan_id">Jenis Layanan</label>
                        <select id="layanan_id" name="layanan_id" required disabled onchange="setHargaOtomatis()">
                            <option value="" data-harga="0">-- Pilih Kategori Terlebih Dahulu --</option>
                            
                            {{-- Render SEMUA layanan (disembunyikan JS nanti) --}}
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
                               value="{{ $hargaLama }}"
                               placeholder="0" required 
                               oninput="hitungTotal()" 
                               class="locked-input" readonly>
                        <small id="info_range" style="color: blue; display: none; margin-top: 5px;"></small>
                    </div>

                    <div class="form-group">
                        <label>Layanan Tambahan (Add On)</label>
                        <div class="addon-container">
                            @php
                                // Helper function buat cek addon lama
                                function getAddonData($keyword, $trx) {
                                    foreach($trx->detailTransaksi as $dt) {
                                        if($dt->layanan && stripos($dt->layanan->nama_layanan, $keyword) !== false) {
                                            return ['checked' => true, 'qty' => $dt->jumlah];
                                        }
                                    }
                                    return ['checked' => false, 'qty' => ''];
                                }
                                $eks = getAddonData('Ekspress', $transaksi);
                                $han = getAddonData('Hanger', $transaksi);
                                $pla = getAddonData('Plastik', $transaksi);
                                $hp  = getAddonData('Hanger + Plastik', $transaksi);
                            @endphp

                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_ekspress" data-harga="5000" onchange="toggleAddonQty(this, 'qty_ekspress')" {{ $eks['checked'] ? 'checked' : '' }}>
                                    <span>Layanan Ekspress (+Rp 5.000/kg)</span>
                                </label>
                                <input type="number" id="qty_ekspress" class="addon-qty" placeholder="Kg" readonly step="1" value="{{ $eks['qty'] }}" style="{{ $eks['checked'] ? 'display:block' : 'display:none' }}">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger" data-harga="3000" onchange="toggleAddonQty(this, 'qty_hanger')" {{ $han['checked'] ? 'checked' : '' }}>
                                    <span>Hanger (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger" class="addon-qty" placeholder="Pcs" min="1" value="{{ $han['qty'] }}" oninput="hitungTotal()" style="{{ $han['checked'] ? 'display:block' : 'display:none' }}">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_plastik" data-harga="3000" onchange="toggleAddonQty(this, 'qty_plastik')" {{ $pla['checked'] ? 'checked' : '' }}>
                                    <span>Plastik (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_plastik" class="addon-qty" placeholder="Pcs" min="1" value="{{ $pla['qty'] }}" oninput="hitungTotal()" style="{{ $pla['checked'] ? 'display:block' : 'display:none' }}">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger_plastik" data-harga="5000" onchange="toggleAddonQty(this, 'qty_hanger_plastik')" {{ $hp['checked'] ? 'checked' : '' }}>
                                    <span>Hanger + Plastik (+Rp 5.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger_plastik" class="addon-qty" placeholder="Pcs" min="1" value="{{ $hp['qty'] }}" oninput="hitungTotal()" style="{{ $hp['checked'] ? 'display:block' : 'display:none' }}">
                            </div>
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    @php
                        $inv = $transaksi->inventaris->pluck('jumlah', 'nama_barang')->toArray();
                        $hasDetail = count($inv) > 0;
                    @endphp
                    <div class="form-group">
                        <label class="toggle-detail-wrapper">
                            <input type="checkbox" id="toggleDetail" onchange="toggleDetailSection()" {{ $hasDetail ? 'checked' : '' }}>
                            <span><i class='bx bx-list-plus'></i> Isi Rincian Pakaian? (Inventaris)</span>
                        </label>
                        
                        <div id="detail-pakaian-section" style="{{ $hasDetail ? 'display:block' : 'display:none' }}">
                            <div class="clothing-grid">
                                <div>
                                    <div class="clothing-item"><label>Kemeja/Baju</label> <input type="number" min="0" class="qty-input" name="qty_baju" value="{{ $inv['Baju'] ?? 0 }}"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Kaos/T-Shirt</label> <input type="number" min="0" class="qty-input" name="qty_kaos" value="{{ $inv['Kaos'] ?? 0 }}"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Celana Panjang</label> <input type="number" min="0" class="qty-input" name="qty_celana_panjang" value="{{ $inv['Celana Panjang'] ?? 0 }}"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Celana Pendek</label> <input type="number" min="0" class="qty-input" name="qty_celana_pendek" value="{{ $inv['Celana Pendek'] ?? 0 }}"></div>
                                </div>
                                <div>
                                    <div class="clothing-item"><label>Jilbab/Kerudung</label> <input type="number" min="0" class="qty-input" name="qty_jilbab" value="{{ $inv['Jilbab'] ?? 0 }}"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Jaket/Sweater</label> <input type="number" min="0" class="qty-input" name="qty_jaket" value="{{ $inv['Jaket'] ?? 0 }}"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Kaos Kaki (Psg)</label> <input type="number" min="0" class="qty-input" name="qty_kaos_kaki" value="{{ $inv['Kaos Kaki'] ?? 0 }}"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Sarung/Mukena</label> <input type="number" min="0" class="qty-input" name="qty_sarung" value="{{ $inv['Sarung'] ?? 0 }}"></div>
                                    <div class="clothing-item" style="margin-top:8px"><label>Daleman/Lainnya</label> <input type="number" min="0" class="qty-input" name="qty_lainnya" value="{{ $inv['Lainnya'] ?? 0 }}"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="berat">Berat (Kg) / Jumlah (Pcs)</label>
                            <input type="number" id="berat" name="berat" value="{{ $transaksi->berat }}" step="1" min="1" required style="font-weight: bold;" oninput="hitungTotal()">
                        </div>

                        <div class="form-group">
                            <label for="total_biaya">Estimasi Total Biaya (Rp)</label>
                            <input type="text" id="total_biaya_tampil" value="Rp " readonly>
                            <input type="hidden" id="total_biaya" name="total_biaya" value="">
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

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

                    <div class="form-group fade-in" id="container_input_dp" style="{{ $transaksi->status_bayar == 'dp' ? 'display:block' : 'display:none' }}">
                        <label for="jumlah_dp" style="color: #f57c00;">Nominal DP (Rp)</label>
                        <input type="number" id="jumlah_dp" name="jumlah_dp" value="{{ $transaksi->jumlah_bayar }}" min="0">
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Khusus (Opsional)</label>
                        <textarea id="catatan" name="catatan" rows="2">{{ $transaksi->catatan }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="window.location.href='{{ route('admin.transaksi.index') }}'">
                            <i class='bx bx-x'></i> Batal
                        </button>
                        <button type="button" class="btn-submit" id="btnSimpan" onclick="prosesSimpan()">
                            <i class='bx bx-save'></i> Update Transaksi
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