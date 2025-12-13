<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/form-transaksi.css') }}" />

    <title>Edit Order ({{ $transaksi->kode_invoice }}) - Rizhaqi Laundry Admin</title>
    
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
                {{-- FORM ACTION MENGARAH KE UPDATE --}}
                <form id="formTransaksi" action="{{ route('admin.transaksi.update', $transaksi->id_transaksi) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="pelanggan">Nama Pelanggan</label>
                        {{-- Isi Value dengan data lama --}}
                        <input type="text" list="customer_list" id="pelanggan" name="nama_pelanggan" 
                               value="{{ $transaksi->pelanggan->nama }}" 
                               placeholder="Ketik nama pelanggan..." autocomplete="off" required oninput="autofillPelanggan()">
                        
                        <datalist id="customer_list">
                            {{-- DATA PELANGGAN DARI DB --}}
                            @foreach($pelanggan as $cust)
                                <option value="{{ $cust->nama }}" 
                                        data-hp="{{ $cust->telepon }}" 
                                        data-alamat="{{ $cust->alamat ?? '' }}">
                                    {{ $cust->telepon }}
                                </option>
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

                    {{-- PERSIAPAN DATA LAMA UNTUK LOGIC LAYANAN --}}
                    @php
                        $mainDetail = $transaksi->detailTransaksi->first(); 
                        $kategoriLama = $mainDetail && $mainDetail->layanan ? $mainDetail->layanan->kategori : '';
                        $layananLamaId = $mainDetail ? $mainDetail->id_layanan : '';
                        $hargaLama = $mainDetail ? $mainDetail->harga_saat_transaksi : 0;
                    @endphp

                    <div class="form-group">
                        <label for="kategori_id">Kategori Layanan</label>
                        <select id="kategori_id" name="kategori_id" required onchange="updateLayananDropdown()">
                            <option value="">-- Pilih Kategori --</option>
                            {{-- LOOPING KATEGORI DARI DB --}}
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->kategori }}" {{ $kategoriLama == $kat->kategori ? 'selected' : '' }}>
                                    {{ $kat->kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="layanan_id">Jenis Layanan</label>
                        {{-- Hapus disabled karena di edit mode data sudah ada --}}
                        <select id="layanan_id" name="layanan_id" required onchange="setHargaOtomatis()">
                            <option value="" data-harga="0">-- Pilih Kategori Terlebih Dahulu --</option>
                            
                            {{-- LOAD SEMUA LAYANAN TAPI DISEMBUNYIKAN JS DULU --}}
                            @foreach($layanan as $item)
                                <option value="{{ $item->id_layanan }}" 
                                        data-kategori="{{ $item->kategori }}"
                                        data-tipe="{{ $item->is_flexible ? 'range' : 'fixed' }}"
                                        data-harga="{{ $item->harga_satuan }}"
                                        data-min="{{ $item->harga_min }}"
                                        data-max="{{ $item->harga_max }}"
                                        {{ $layananLamaId == $item->id_layanan ? 'selected' : '' }}>
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
                            {{-- LOGIC PHP: Cek apakah addon ada di transaksi lama --}}
                            @php
                                function cekAddon($key, $trx) {
                                    foreach($trx->detailTransaksi as $dt) {
                                        // Cari addon berdasarkan nama (case insensitive)
                                        if($dt->layanan && stripos($dt->layanan->nama_layanan, $key) !== false) {
                                            return ['checked' => true, 'qty' => $dt->jumlah];
                                        }
                                    }
                                    return ['checked' => false, 'qty' => ''];
                                }
                                $eks = cekAddon('Ekspress', $transaksi);
                                $han = cekAddon('Hanger', $transaksi);
                                $pla = cekAddon('Plastik', $transaksi);
                                $hp  = cekAddon('Hanger + Plastik', $transaksi);
                            @endphp

                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_ekspress" name="addon_ekspress" data-harga="5000" onchange="toggleAddonQty(this, 'qty_ekspress')" {{ $eks['checked'] ? 'checked' : '' }}>
                                    <span>Layanan Ekspress (+Rp 5.000/kg)</span>
                                </label>
                                <input type="number" id="qty_ekspress" name="qty_ekspress" class="addon-qty" placeholder="Kg" readonly step="1" value="{{ $eks['qty'] }}" style="{{ $eks['checked'] ? 'display:block' : 'display:none' }}">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger" name="addon_hanger" data-harga="3000" onchange="toggleAddonQty(this, 'qty_hanger')" {{ $han['checked'] ? 'checked' : '' }}>
                                    <span>Hanger (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger" name="qty_hanger" class="addon-qty" placeholder="Pcs" min="1" value="{{ $han['qty'] }}" oninput="hitungTotal()" style="{{ $han['checked'] ? 'display:block' : 'display:none' }}">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_plastik" name="addon_plastik" data-harga="3000" onchange="toggleAddonQty(this, 'qty_plastik')" {{ $pla['checked'] ? 'checked' : '' }}>
                                    <span>Plastik (+Rp 3.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_plastik" name="qty_plastik" class="addon-qty" placeholder="Pcs" min="1" value="{{ $pla['qty'] }}" oninput="hitungTotal()" style="{{ $pla['checked'] ? 'display:block' : 'display:none' }}">
                            </div>
                            <div class="addon-row">
                                <label class="addon-label">
                                    <input type="checkbox" id="addon_hanger_plastik" name="addon_hanger_plastik" data-harga="5000" onchange="toggleAddonQty(this, 'qty_hanger_plastik')" {{ $hp['checked'] ? 'checked' : '' }}>
                                    <span>Hanger + Plastik (+Rp 5.000/pcs)</span>
                                </label>
                                <input type="number" id="qty_hanger_plastik" name="qty_hanger_plastik" class="addon-qty" placeholder="Pcs" min="1" value="{{ $hp['qty'] }}" oninput="hitungTotal()" style="{{ $hp['checked'] ? 'display:block' : 'display:none' }}">
                            </div>
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--border-light); margin: 20px 0;">

                    {{-- Data Inventaris Lama --}}
                    @php
                        $inv = $transaksi->inventaris->pluck('jumlah', 'nama_barang')->toArray();
                        $hasInv = count($inv) > 0;
                    @endphp

                    <div class="form-group">
                        <label class="toggle-detail-wrapper">
                            <input type="checkbox" id="toggleDetail" name="toggleDetail" onchange="toggleDetailSection()" {{ $hasInv ? 'checked' : '' }}>
                            <span><i class='bx bx-list-plus'></i> Isi Rincian Pakaian? (Inventaris)</span>
                        </label>
                        <small style="display:block; margin-top:5px; color:var(--text-tertiary);">Data ini hanya untuk catatan nota (tidak mempengaruhi harga kiloan).</small>

                        <div id="detail-pakaian-section" style="{{ $hasInv ? 'display:block' : 'display:none' }}">
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
                            <small style="color: var(--text-secondary);">Masukkan angka bulat (tanpa koma).</small>
                        </div>

                        <div class="form-group">
                            <label for="total_biaya">Estimasi Total Biaya (Rp)</label>
                            <input type="text" id="total_biaya_tampil" value="Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}" readonly>
                            <input type="hidden" id="total_biaya" name="total_biaya" value="{{ $transaksi->total_biaya }}">
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
                            <option value="belum" {{ $transaksi->status_bayar == 'belum' ? 'selected' : '' }}>Belum Bayar (Bayar Nanti)</option>
                            <option value="lunas" {{ $transaksi->status_bayar == 'lunas' ? 'selected' : '' }}>Lunas (Bayar Sekarang)</option>
                            <option value="dp"    {{ $transaksi->status_bayar == 'dp' ? 'selected' : '' }}>DP (Uang Muka)</option>
                        </select>
                    </div>

                    <div class="form-group fade-in" id="container_input_dp" style="{{ $transaksi->status_bayar == 'dp' ? 'display:block' : 'display:none' }}">
                        <label for="jumlah_dp" style="color: #f57c00;">Nominal DP (Rp)</label>
                        <input type="number" id="jumlah_dp" name="jumlah_dp" value="{{ $transaksi->jumlah_bayar }}" min="0">
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan Khusus (Opsional)</label>
                        <textarea id="catatan" name="catatan" placeholder="Contoh: Jangan pakai pewangi..." rows="2">{{ $transaksi->catatan }}</textarea>
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

    <script>
        // === VARIABEL GLOBAL UNTUK MENYIMPAN OPSI LAYANAN LENGKAP ===
        let allLayananOptions = [];

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Simpan semua opsi layanan ke memory sebelum di-filter
            const layananSelect = document.getElementById('layanan_id');
            if(layananSelect) {
                const options = Array.from(layananSelect.options);
                allLayananOptions = options.slice(1).map(opt => ({
                    value: opt.value,
                    text: opt.text,
                    kategori: opt.getAttribute('data-kategori'),
                    harga: opt.getAttribute('data-harga'),
                    tipe: opt.getAttribute('data-tipe'),
                    min: opt.getAttribute('data-min'),
                    max: opt.getAttribute('data-max'),
                    selected: opt.selected // Simpan status terpilih dari PHP
                }));
            }

            // 2. Jalankan Filter Awal (Agar dropdown layanan sesuai kategori yang terpilih di PHP)
            updateLayananDropdown();

            // 3. Set Status Harga (Agar input harga unlock/lock sesuai data lama)
            setHargaOtomatis();
        });

        // === FUNGSI TAMBAHAN: AUTOFILL PELANGGAN (SAMA SEPERTI CREATE) ===
        function autofillPelanggan() {
            const inputVal = document.getElementById('pelanggan').value;
            const list = document.getElementById('customer_list');
            const options = list.options;
            
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === inputVal) {
                    const hp = options[i].getAttribute('data-hp');
                    const almt = options[i].getAttribute('data-alamat');
                    
                    document.getElementById('no_hp').value = hp;
                    document.getElementById('alamat').value = almt;
                    break;
                }
            }
        }

        // === FUNGSI UTAMA (SAMA SEPERTI CREATE, DENGAN SEDIKIT PENYESUAIAN EDIT) ===
        function updateLayananDropdown() {
            const kategoriSelect = document.getElementById('kategori_id');
            const layananSelect = document.getElementById('layanan_id');
            const selectedKategori = kategoriSelect.value;
            
            // Simpan ID yang sedang dipilih (dari PHP atau user)
            const currentSelectedID = "{{ $layananLamaId }}"; 

            // Reset dropdown
            layananSelect.innerHTML = '<option value="" data-harga="0">-- Pilih Layanan --</option>';

            if (selectedKategori === "") {
                layananSelect.disabled = true;
                return;
            }

            layananSelect.disabled = false;
            
            // Filter opsi dari memory
            const filteredOptions = allLayananOptions.filter(opt => opt.kategori === selectedKategori);

            filteredOptions.forEach(opt => {
                const newOption = document.createElement('option');
                newOption.value = opt.value;
                newOption.text = opt.text;
                newOption.setAttribute('data-kategori', opt.kategori);
                newOption.setAttribute('data-harga', opt.harga);
                newOption.setAttribute('data-tipe', opt.tipe);
                newOption.setAttribute('data-min', opt.min);
                newOption.setAttribute('data-max', opt.max);
                
                // Cek apakah opsi ini adalah layanan lama? Jika ya, select kembali
                if (opt.value == currentSelectedID) {
                    newOption.selected = true;
                }
                
                layananSelect.add(newOption);
            });
        }

        function setHargaOtomatis() {
            const select = document.getElementById('layanan_id');
            const inputHarga = document.getElementById('harga_satuan');
            const infoRange = document.getElementById('info_range');
            
            if (!select || !inputHarga) return;

            const selectedOption = select.options[select.selectedIndex];
            if (!selectedOption || selectedOption.value === "") return;

            const tipe = selectedOption.getAttribute('data-tipe');
            const harga = selectedOption.getAttribute('data-harga');
            const min = selectedOption.getAttribute('data-min');
            const max = selectedOption.getAttribute('data-max');

            inputHarga.classList.remove('locked-input', 'unlocked-input');
            infoRange.style.display = 'none';

            if (tipe === 'range') {
                inputHarga.readOnly = false;
                inputHarga.style.backgroundColor = "#fff";
                inputHarga.style.cursor = "text"; 
                inputHarga.classList.add('unlocked-input');
                
                infoRange.style.display = 'block';
                infoRange.textContent = `*Harga Fleksibel: Rp ${formatRupiah(min)} - Rp ${formatRupiah(max)}`;

                // PENTING: Jangan reset harga jadi 0 jika sudah ada nilai dari DB!
                if(inputHarga.value == 0 || inputHarga.value == "") {
                     inputHarga.placeholder = `Min: ${formatRupiah(min)}`;
                }

            } else {
                inputHarga.readOnly = true;
                inputHarga.value = harga; 
                
                inputHarga.style.backgroundColor = "#e9ecef";
                inputHarga.style.cursor = "not-allowed";
                inputHarga.classList.add('locked-input');
            }
            
            hitungTotal(); 
        }

        function hitungTotal() {
            const elBerat = document.getElementById('berat');
            const elHarga = document.getElementById('harga_satuan');
            
            const berat = elBerat && elBerat.value ? parseFloat(elBerat.value) : 0;
            const hargaSatuan = elHarga && elHarga.value ? parseFloat(elHarga.value) : 0;
            
            let subTotal = hargaSatuan * berat;

            let totalAddon = 0;
            
            const addons = [
                {chk: 'addon_ekspress', qty: 'qty_ekspress'},
                {chk: 'addon_hanger', qty: 'qty_hanger'},
                {chk: 'addon_plastik', qty: 'qty_plastik'},
                {chk: 'addon_hanger_plastik', qty: 'qty_hanger_plastik'}
            ];

            addons.forEach(item => {
                const checkbox = document.getElementById(item.chk);
                const inputQty = document.getElementById(item.qty);
                
                if(checkbox && checkbox.checked) {
                    const hargaAddon = parseFloat(checkbox.getAttribute('data-harga')) || 0;
                    const jumlah = parseFloat(inputQty.value) || 0;
                    totalAddon += (hargaAddon * jumlah);
                }
            });

            const grandTotal = subTotal + totalAddon;

            document.getElementById('total_biaya_tampil').value = "Rp " + new Intl.NumberFormat('id-ID').format(grandTotal);
            document.getElementById('total_biaya').value = grandTotal;
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function toggleAddonQty(checkbox, inputId) {
            const inputQty = document.getElementById(inputId);
            const beratUtama = document.getElementById('berat').value;

            if(checkbox.checked) {
                inputQty.style.display = 'block';
                if(inputQty.value == "" || inputQty.value == 0) {
                     inputQty.value = beratUtama ? beratUtama : 1;
                }
            } else {
                inputQty.style.display = 'none';
                inputQty.value = 0; 
            }
            hitungTotal();
        }

        function toggleDetailSection() {
            const checkBox = document.getElementById('toggleDetail');
            const section = document.getElementById('detail-pakaian-section');
            if(checkBox && section) section.style.display = checkBox.checked ? "block" : "none";
        }

        function toggleInputDP() {
            const status = document.getElementById('status_bayar').value;
            const containerDP = document.getElementById('container_input_dp');
            const inputDP = document.getElementById('jumlah_dp');
            if(status === 'dp') {
                containerDP.style.display = 'block';
                inputDP.required = true;
            } else {
                containerDP.style.display = 'none';
                inputDP.required = false;
            }
        }

        function prosesSimpan() {
            document.getElementById('formTransaksi').submit(); 
        }
    </script>
</body>
</html>