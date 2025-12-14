let allLayananOptions = [];

document.addEventListener('DOMContentLoaded', function() {
    // 1. Simpan opsi layanan ke memory (Untuk fitur filter kategori)
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
            max: opt.getAttribute('data-max')
        }));

        layananSelect.innerHTML = '<option value="" data-harga="0">-- Pilih Kategori Terlebih Dahulu --</option>';
        layananSelect.disabled = true;
    }

    // 2. Set Default Tanggal (Hari ini + 2 hari)
    const dateInput = document.getElementById('tgl_selesai');
    if(dateInput && !dateInput.value) {
        const today = new Date();
        today.setDate(today.getDate() + 2);
        dateInput.value = today.toISOString().split('T')[0];
    }

    // 3. Listener Berat (Trigger hitungTotal saat berat diketik)
    const beratInput = document.getElementById('berat');
    if(beratInput) {
        beratInput.addEventListener('input', hitungTotal); 
    }
});

// --- FUNGSI 1: FILTER LAYANAN BERDASARKAN KATEGORI ---
function updateLayananDropdown() {
    const kategoriSelect = document.getElementById('kategori_id');
    const layananSelect = document.getElementById('layanan_id');
    const selectedKategori = kategoriSelect.value;

    // Reset dropdown & harga
    layananSelect.innerHTML = '<option value="" data-harga="0">-- Pilih Layanan --</option>';
    const inputHarga = document.getElementById('harga_satuan');
    if(inputHarga) inputHarga.value = '';
    
    // Pastikan hitung total jalan biar reset ke 0
    hitungTotal(); 

    if (selectedKategori === "") {
        layananSelect.disabled = true;
        return;
    }

    layananSelect.disabled = false;
    // Filter opsi sesuai kategori yang dipilih
    const filteredOptions = allLayananOptions.filter(opt => opt.kategori === selectedKategori);

    // Masukkan opsi yang sudah difilter ke dropdown
    filteredOptions.forEach(opt => {
        const newOption = document.createElement('option');
        newOption.value = opt.value;
        newOption.text = opt.text;
        newOption.setAttribute('data-kategori', opt.kategori);
        newOption.setAttribute('data-harga', opt.harga);
        newOption.setAttribute('data-tipe', opt.tipe);
        newOption.setAttribute('data-min', opt.min);
        newOption.setAttribute('data-max', opt.max);
        layananSelect.add(newOption);
    });
}

// --- FUNGSI 2: SET HARGA SAAT PILIH JENIS LAYANAN ---
function setHargaOtomatis() {
    const select = document.getElementById('layanan_id');
    const inputHarga = document.getElementById('harga_satuan');
    const infoRange = document.getElementById('info_range');
    const selectedOption = select.options[select.selectedIndex];

    if(!inputHarga) return;

    inputHarga.value = 0; // Default 0
    if(infoRange) infoRange.style.display = 'none';
    
    inputHarga.readOnly = true; 
    inputHarga.style.backgroundColor = "#e9ecef"; 

    if (selectedOption && selectedOption.value !== "") {
        const tipe = selectedOption.getAttribute('data-tipe');
        
        if (tipe === 'fixed') {
            // Jika harga tetap
            const harga = selectedOption.getAttribute('data-harga');
            inputHarga.value = harga; 
        } else if (tipe === 'range') {
            // Jika harga range (fleksibel)
            const min = selectedOption.getAttribute('data-min');
            const max = selectedOption.getAttribute('data-max');
            inputHarga.value = min; // Set nilai awal ke minimum
            
            // Buka kunci input biar bisa diedit manual
            inputHarga.readOnly = false;
            inputHarga.style.backgroundColor = "#ffffff";
            
            if(infoRange) {
                infoRange.style.display = 'block';
                let fmtMin = parseInt(min).toLocaleString('id-ID');
                let fmtMax = parseInt(max).toLocaleString('id-ID');
                infoRange.innerText = `*Harga Fleksibel: Rp ${fmtMin} - Rp ${fmtMax}`;
            }
        }
    }
    hitungTotal(); // PENTING: Hitung ulang setelah harga di-set
}

// --- FUNGSI 3: KALKULATOR UTAMA (HITUNG TOTAL) ---
function hitungTotal() {
    // 1. Ambil Harga Satuan (Pastikan convert ke float, default 0)
    const elHarga = document.getElementById('harga_satuan');
    const hargaSatuan = elHarga && elHarga.value ? parseFloat(elHarga.value) : 0;
    
    // 2. Ambil Berat
    const elBerat = document.getElementById('berat');
    const berat = elBerat && elBerat.value ? parseFloat(elBerat.value) : 0;
    
    // === [LOGIKA SYNC EKSPRESS] ===
    // Update Qty semua Add On jenis "Ekspress" agar selalu sama dengan Berat Laundry
    const allChecks = document.querySelectorAll('.addon-checkbox');
    allChecks.forEach(chk => {
        // Cek apakah jenisnya ekspress & sedang dicentang
        if (chk.getAttribute('data-jenis') === 'Ekspress' && chk.checked) {
            // Cari input qty pasangannya
            const targetId = chk.id.replace('addon_', 'qty_');
            const qtyInput = document.getElementById(targetId);
            if (qtyInput) {
                qtyInput.value = berat > 0 ? berat : 1;
                qtyInput.readOnly = true; // Pastikan terkunci
            }
        }
    });
    // ==============================

    // 3. Subtotal Layanan (Harga x Berat)
    let subTotal = hargaSatuan * berat;

    // 4. Hitung Addons (Looping semua checkbox yang ada di halaman)
    let totalAddon = 0;
    
    allChecks.forEach(chk => {
        // Hanya hitung jika dicentang
        if(chk.checked) {
            let h = parseFloat(chk.getAttribute('data-harga')) || 0;
            
            // Ambil Qty dari input pasangannya
            const targetId = chk.id.replace('addon_', 'qty_');
            const qtyInput = document.getElementById(targetId);
            
            let q = 0;
            if(qtyInput) {
               q = parseFloat(qtyInput.value) || 0; 
            }
            
            // Tambah ke total
            totalAddon += (h * q);
        }
    });

    // 5. Grand Total
    const grandTotal = subTotal + totalAddon;

    // 6. Tampilkan ke Input
    const tampilElement = document.getElementById('total_biaya_tampil');
    const hiddenElement = document.getElementById('total_biaya');
    
    if(tampilElement) tampilElement.value = "Rp " + grandTotal.toLocaleString('id-ID');
    if(hiddenElement) hiddenElement.value = grandTotal;
}

// --- FUNGSI 4: TOGGLE INPUT ADD ON (Dipanggil saat checkbox diklik) ---
function toggleAddonQty(checkbox, inputId) {
    const inputQty = document.getElementById(inputId);
    const beratUtama = document.getElementById('berat').value;
    const jenis = checkbox.getAttribute('data-jenis'); // Ambil jenis dari HTML (ekspress/biasa)

    if(checkbox.checked) {
        // Tampilkan input qty
        inputQty.style.display = 'block';
        
        // Logika beda untuk Ekspress vs Biasa
        if (jenis === 'Ekspress') {
             // Kalau Ekspress: Ikut Berat & Readonly
             inputQty.value = beratUtama ? beratUtama : 1;
             inputQty.readOnly = true; 
             inputQty.style.backgroundColor = "#e9ecef"; 
        } else {
             // Kalau Biasa: Default 1 & Bisa diedit
             if(inputQty.value == "" || inputQty.value == 0) {
                 inputQty.value = 1; 
             }
             inputQty.readOnly = false;
             inputQty.style.backgroundColor = "#ffffff";
        }

    } else {
        // Jika uncheck: Sembunyikan & Nol-kan nilai
        inputQty.style.display = 'none';
        inputQty.value = 0; 
    }
    
    // PENTING: Langsung hitung total agar harga berubah real-time
    hitungTotal();
}

// --- FUNGSI UTILS LAINNYA ---
function toggleDetailSection() {
    const checkBox = document.getElementById('toggleDetail');
    const section = document.getElementById('detail-pakaian-section');
    if(checkBox && section) section.style.display = checkBox.checked ? "block" : "none";
}

function toggleInputDP() {
    const status = document.getElementById('status_bayar').value;
    const containerDP = document.getElementById('container_input_dp');
    const inputDP = document.getElementById('jumlah_dp');
    
    if(containerDP && inputDP) {
        if(status === 'dp') {
            containerDP.style.display = 'block';
            inputDP.required = true;
        } else {
            containerDP.style.display = 'none';
            inputDP.required = false;
            inputDP.value = '';
        }
    }
}

function prosesSimpan() {
    const form = document.getElementById('formTransaksi');
    if(form) form.submit(); 
}