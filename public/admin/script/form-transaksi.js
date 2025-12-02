// public/admin/script/form-transaksi.js

// --- DATABASE LAYANAN (JSON) ---
// Ini simulasi data dari backend. Nanti backend bisa ganti ini pakai API/Ajax.
const databaseLayanan = {
    'kiloan': [
        { id: 1, nama: 'Cuci Kering Setrika Pakaian', harga: 10000 },
        { id: 2, nama: 'CKS - Pakaian Dalam', harga: 14500 },
        { id: 3, nama: 'CKS - Sprei/Selimut/Bed Cover', harga: 14000 },
        { id: 4, nama: 'CKS - Fitrasi/Gordyn', harga: 14000 },
        { id: 5, nama: 'Setrika Saja', harga: 6000 }
    ],
    'promo_jumat': [
        { id: 6, nama: 'JUMAT: CKS - Pakaian', harga: 9000 },
        { id: 7, nama: 'JUMAT: CKS - Sprei/BC', harga: 12000 },
        { id: 8, nama: 'JUMAT: Setrika', harga: 5000 }
    ],
    'promo_selasa': [
        { id: 9, nama: 'SELASA: CKS - Pakaian', harga: 9500 },
        { id: 10, nama: 'SELASA: CKS - Sprei/BC', harga: 13500 }
    ],
    'paket': [
        { id: 11, nama: 'Cuci Kering (6kg)', harga: 20000 },
        { id: 12, nama: 'Cuci Kering Lipat (6kg)', harga: 25000 },
        { id: 13, nama: 'Setrika Borongan (50kg)', harga: 250000 }
    ],
    'satuan': [
        { id: 14, nama: 'Pakaian Satuan', harga: 15000 },
        { id: 15, nama: 'Jas', harga: 20000 },
        { id: 16, nama: 'Kebaya/Gaun', harga: 30000 }
    ],
    'karpet': [
        { id: 17, nama: 'Karpet Tipis', harga: 18500 },
        { id: 18, nama: 'Karpet Tebal/Berbulu', harga: 20000 }
    ]
};

document.addEventListener('DOMContentLoaded', function() {
    // 1. Set Default Tanggal Selesai (+2 Hari)
    const dateInput = document.getElementById('tgl_selesai');
    if(dateInput && !dateInput.value) {
        const today = new Date();
        today.setDate(today.getDate() + 2);
        dateInput.value = today.toISOString().split('T')[0];
    }

    // 2. Listener untuk Berat Utama (Update Ekspress otomatis)
    const beratInput = document.getElementById('berat');
    if(beratInput) {
        beratInput.addEventListener('input', function() {
            const ekspressCheck = document.getElementById('addon_ekspress');
            const ekspressQty = document.getElementById('qty_ekspress');
            if(ekspressCheck && ekspressCheck.checked) {
                ekspressQty.value = this.value;
            }
            hitungTotal();
        });
    }
});

// 3. FUNGSI UPDATE DROPDOWN (CASCADING)
function updateLayananDropdown() {
    const kategoriSelect = document.getElementById('kategori_id');
    const layananSelect = document.getElementById('layanan_id');
    const kategori = kategoriSelect.value;

    // Kosongkan dropdown layanan dulu
    layananSelect.innerHTML = '<option value="" data-harga="0">-- Pilih Jenis Layanan --</option>';
    
    if (kategori && databaseLayanan[kategori]) {
        // Aktifkan dropdown
        layananSelect.disabled = false;
        
        // Loop data dan masukkan ke option
        databaseLayanan[kategori].forEach(item => {
            let option = document.createElement('option');
            option.value = item.id;
            // Format harga biar cantik
            let hargaFmt = item.harga.toLocaleString('id-ID');
            option.text = `${item.nama} (Rp ${hargaFmt})`;
            option.setAttribute('data-harga', item.harga); // Penting buat kalkulator!
            layananSelect.add(option);
        });
    } else {
        // Kalau tidak pilih kategori, disable lagi
        layananSelect.disabled = true;
    }
    // Reset total harga karena layanan berubah
    hitungTotal();
}

// 4. FUNGSI TOGGLE DETAIL PAKAIAN
function toggleDetailSection() {
    const checkBox = document.getElementById('toggleDetail');
    const section = document.getElementById('detail-pakaian-section');
    if(checkBox && section) {
        section.style.display = checkBox.checked ? "block" : "none";
    }
}

// 5. FUNGSI TOGGLE INPUT DP
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

// 6. FUNGSI ADD ON (Show/Hide Qty Input)
function toggleAddonQty(checkbox, inputId) {
    const inputQty = document.getElementById(inputId);
    if(checkbox.checked) {
        inputQty.style.display = 'block';
        if(inputId === 'qty_ekspress') {
            const beratUtama = document.getElementById('berat').value;
            inputQty.value = beratUtama ? beratUtama : 0;
        }
    } else {
        inputQty.style.display = 'none';
        inputQty.value = ''; 
    }
    hitungTotal();
}

// 7. KALKULATOR TOTAL
function hitungTotal() {
    // A. Hitung Layanan Utama
    var selectLayanan = document.getElementById('layanan_id');
    var hargaDasar = 0;
    if(selectLayanan && selectLayanan.selectedIndex !== -1) {
        var selectedOption = selectLayanan.options[selectLayanan.selectedIndex];
        hargaDasar = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
    }
    
    var beratInput = document.getElementById('berat');
    var berat = parseFloat(beratInput.value) || 0;
    var totalLayanan = hargaDasar * berat;

    // B. Hitung Add On
    var totalAddOn = 0;
    
    function hitungAddon(idCheck, idQty) {
        const check = document.getElementById(idCheck);
        const qty = document.getElementById(idQty);
        if(check && qty && check.checked) {
            let h = parseFloat(check.getAttribute('data-harga'));
            let q = parseFloat(qty.value) || 0;
            return h * q;
        }
        return 0;
    }

    totalAddOn += hitungAddon('addon_ekspress', 'qty_ekspress');
    totalAddOn += hitungAddon('addon_hanger', 'qty_hanger');
    totalAddOn += hitungAddon('addon_plastik', 'qty_plastik');
    totalAddOn += hitungAddon('addon_hanger_plastik', 'qty_hanger_plastik');

    // C. Grand Total
    var grandTotal = totalLayanan + totalAddOn;

    // D. Tampilkan
    const tampilElement = document.getElementById('total_biaya_tampil');
    const hiddenElement = document.getElementById('total_biaya');
    
    if(tampilElement) tampilElement.value = "Rp " + grandTotal.toLocaleString('id-ID');
    if(hiddenElement) hiddenElement.value = grandTotal;
}

// 8. FUNGSI SIMPAN MANUAL (ANTI LAYAR PUTIH)
function prosesSimpan() {
    const form = document.getElementById('formTransaksi');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const btnSubmit = document.getElementById('btnSimpan');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Menyimpan...";

    setTimeout(() => {
        alert("Sukses! Transaksi berhasil disimpan.");
        const redirectUrl = form.getAttribute('data-redirect-url');
        if(redirectUrl) {
            window.location.href = redirectUrl;
        }
    }, 1500);
}