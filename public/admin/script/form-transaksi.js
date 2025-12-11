let allLayananOptions = [];

document.addEventListener('DOMContentLoaded', function() {
    // 1. Simpan opsi layanan ke memory
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

    // 2. Set Default Tanggal
    const dateInput = document.getElementById('tgl_selesai');
    if(dateInput && !dateInput.value) {
        const today = new Date();
        today.setDate(today.getDate() + 2);
        dateInput.value = today.toISOString().split('T')[0];
    }

    // 3. Listener Berat (Trigger hitungTotal)
    const beratInput = document.getElementById('berat');
    if(beratInput) {
        beratInput.addEventListener('input', hitungTotal); // Langsung panggil hitungTotal
    }
});

// --- FUNGSI 1: FILTER LAYANAN ---
function updateLayananDropdown() {
    const kategoriSelect = document.getElementById('kategori_id');
    const layananSelect = document.getElementById('layanan_id');
    const selectedKategori = kategoriSelect.value;

    // Reset
    layananSelect.innerHTML = '<option value="" data-harga="0">-- Pilih Layanan --</option>';
    const inputHarga = document.getElementById('harga_satuan');
    inputHarga.value = '';
    
    // Pastikan hitung total jalan biar reset ke 0
    hitungTotal(); 

    if (selectedKategori === "") {
        layananSelect.disabled = true;
        return;
    }

    layananSelect.disabled = false;
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
        layananSelect.add(newOption);
    });
}

// --- FUNGSI 2: SET HARGA SAAT PILIH LAYANAN ---
function setHargaOtomatis() {
    const select = document.getElementById('layanan_id');
    const inputHarga = document.getElementById('harga_satuan');
    const infoRange = document.getElementById('info_range');
    const selectedOption = select.options[select.selectedIndex];

    inputHarga.value = 0; // Default 0
    infoRange.style.display = 'none';
    inputHarga.readOnly = true; 
    inputHarga.style.backgroundColor = "#e9ecef"; 

    if (selectedOption && selectedOption.value !== "") {
        const tipe = selectedOption.getAttribute('data-tipe');
        
        if (tipe === 'fixed') {
            const harga = selectedOption.getAttribute('data-harga');
            inputHarga.value = harga; // Isi harga fix
        } else if (tipe === 'range') {
            const min = selectedOption.getAttribute('data-min');
            const max = selectedOption.getAttribute('data-max');
            inputHarga.value = min; // Isi harga min
            
            // Buka kunci
            inputHarga.readOnly = false;
            inputHarga.style.backgroundColor = "#ffffff";
            
            infoRange.style.display = 'block';
            let fmtMin = parseInt(min).toLocaleString('id-ID');
            let fmtMax = parseInt(max).toLocaleString('id-ID');
            infoRange.innerText = `*Harga Fleksibel: Rp ${fmtMin} - Rp ${fmtMax}`;
        }
    }
    hitungTotal(); // PENTING: Hitung ulang setelah harga di-set
}

// --- FUNGSI 3: KALKULATOR UTAMA ---
function hitungTotal() {
    // 1. Ambil Harga Satuan (Pastikan convert ke float, default 0)
    const elHarga = document.getElementById('harga_satuan');
    const hargaSatuan = elHarga && elHarga.value ? parseFloat(elHarga.value) : 0;
    
    // 2. Ambil Berat
    const elBerat = document.getElementById('berat');
    const berat = elBerat && elBerat.value ? parseFloat(elBerat.value) : 0;
    
    // 3. Subtotal Layanan
    let subTotal = hargaSatuan * berat;

    // 4. Hitung Addons
    let totalAddon = 0;
    
    function getAddonVal(idCheck, idQty) {
        const check = document.getElementById(idCheck);
        const qty = document.getElementById(idQty);
        
        // Cek apakah elemen ada & dicentang
        if(check && check.checked) {
            let h = parseFloat(check.getAttribute('data-harga')) || 0;
            // Kalau qty input ada isinya pake itu, kalau gak ada (hidden) pake berat utama
            let q = 0;
            if(qty) {
               q = parseFloat(qty.value) || 0; 
            }
            return h * q;
        }
        return 0;
    }

    totalAddon += getAddonVal('addon_ekspress', 'qty_ekspress');
    totalAddon += getAddonVal('addon_hanger', 'qty_hanger');
    totalAddon += getAddonVal('addon_plastik', 'qty_plastik');
    totalAddon += getAddonVal('addon_hanger_plastik', 'qty_hanger_plastik');

    // 5. Grand Total
    const grandTotal = subTotal + totalAddon;

    // 6. Tampilkan
    const tampilElement = document.getElementById('total_biaya_tampil');
    const hiddenElement = document.getElementById('total_biaya');
    
    if(tampilElement) tampilElement.value = "Rp " + grandTotal.toLocaleString('id-ID');
    if(hiddenElement) hiddenElement.value = grandTotal;
}

// --- FUNGSI UTILS ---
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
        inputDP.value = '';
    }
}

function toggleAddonQty(checkbox, inputId) {
    const inputQty = document.getElementById(inputId);
    const beratUtama = document.getElementById('berat').value;

    if(checkbox.checked) {
        inputQty.style.display = 'block';
        // Auto isi qty addon sama dengan berat utama jika kosong
        if(inputQty.value == "" || inputQty.value == 0) {
             inputQty.value = beratUtama ? beratUtama : 1;
        }
    } else {
        inputQty.style.display = 'none';
        inputQty.value = ''; 
    }
    hitungTotal();
}

function prosesSimpan() {
    document.getElementById('formTransaksi').submit(); 
}