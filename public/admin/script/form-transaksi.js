// public/admin/script/form-transaksi.js

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

// 3. FUNGSI TOGGLE DETAIL PAKAIAN
function toggleDetailSection() {
    const checkBox = document.getElementById('toggleDetail');
    const section = document.getElementById('detail-pakaian-section');
    if(checkBox && section) {
        section.style.display = checkBox.checked ? "block" : "none";
    }
}

// 4. FUNGSI TOGGLE INPUT DP
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

// 5. FUNGSI ADD ON (Show/Hide Qty Input)
function toggleAddonQty(checkbox, inputId) {
    const inputQty = document.getElementById(inputId);
    if(checkbox.checked) {
        inputQty.style.display = 'block';
        // Khusus Ekspress: qty otomatis sama dengan berat utama
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

// 6. KALKULATOR TOTAL
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

// 7. FUNGSI SIMPAN MANUAL (ANTI LAYAR PUTIH)
function prosesSimpan() {
    const form = document.getElementById('formTransaksi');
    
    // Validasi HTML5 bawaan
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Efek Loading
    const btnSubmit = document.getElementById('btnSimpan');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Menyimpan...";

    // Simulasi Sukses
    setTimeout(() => {
        alert("Sukses! Transaksi berhasil disimpan.");
        
        // Redirect menggunakan URL dari atribut data-redirect-url di form
        const redirectUrl = form.getAttribute('data-redirect-url');
        if(redirectUrl) {
            window.location.href = redirectUrl;
        }
    }, 1500);
}