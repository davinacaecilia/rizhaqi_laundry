document.addEventListener("DOMContentLoaded", function() {
    
    // =================================================
    // 1. LOGIKA TAB HARIAN (FILTER & TOTAL BERAT)
    // =================================================
    const dateInput = document.getElementById('laporanDate');
    const searchInput = document.getElementById('laporanSearch');
    
    // Jalankan filter saat halaman pertama kali dimuat
    if(dateInput && searchInput) {
        filterLaporanHarian(); 

        dateInput.addEventListener('change', filterLaporanHarian);
        searchInput.addEventListener('keyup', filterLaporanHarian);
    }

    // Toggle Search Bar (Khusus Halaman Laporan)
    const toggleIcon = document.getElementById('tableSearchIcon');
    const searchField = document.getElementById('laporanSearch');
    if(toggleIcon && searchField) {
        toggleIcon.addEventListener('click', function() {
            searchField.classList.toggle('show');
            if(searchField.classList.contains('show')) searchField.focus();
        });
    }

    function filterLaporanHarian() {
        const dateInput = document.getElementById('laporanDate');
        const searchInput = document.getElementById('laporanSearch');
        if(!dateInput || !searchInput) return;

        const filterDate = dateInput.value;
        const filterText = searchInput.value.toLowerCase();
        const tables = document.querySelectorAll('.data-table');

        tables.forEach(table => {
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr.data-row'); // Hanya pilih baris data asli
            let sumTotal = 0; let sumMasuk = 0; let sumKeluar = 0; let sumBerat = 0;
            let visibleRows = 0;

            // Hapus pesan kosong lama jika ada
            const oldMsg = tbody.querySelector('.no-data-msg-row');
            if(oldMsg) oldMsg.remove();

            rows.forEach(row => {
                const rowDate = row.querySelector('.tgl-row').textContent.trim();
                const rowText = row.textContent.toLowerCase();
                const matchDate = filterDate === '' || rowDate === filterDate;
                const matchSearch = rowText.includes(filterText);

                if (matchDate && matchSearch) {
                    row.style.display = '';
                    visibleRows++;
                    
                    // Hitung Uang (Total Tagihan)
                    const valTotal = row.querySelector('.val-total');
                    if(valTotal) sumTotal += parseInt(valTotal.textContent);

                    // Hitung Arus Kas (Masuk/Keluar)
                    const valMasuk = row.querySelector('.val-masuk');
                    const valKeluar = row.querySelector('.val-keluar');
                    if(valMasuk) sumMasuk += parseInt(valMasuk.textContent);
                    if(valKeluar) sumKeluar += parseInt(valKeluar.textContent);

                    // Hitung Berat (Ambil angka saja)
                    const valBerat = row.querySelector('.val-berat');
                    if(valBerat) {
                        const beratStr = valBerat.textContent.replace(' Kg', '').replace(',', '.');
                        sumBerat += parseFloat(beratStr);
                    }

                } else {
                    row.style.display = 'none';
                }
            });

            // TAMPILKAN PESAN "DATA TIDAK ADA" JIKA KOSONG
            if(visibleRows === 0) {
                const colSpan = table.querySelector('thead tr').children.length;
                const msgRow = document.createElement('tr');
                msgRow.className = 'no-data-msg-row';
                msgRow.innerHTML = `<td colspan="${colSpan}" class="no-data-msg" style="text-align: center; padding: 20px; color: #999; font-style: italic;"><i class='bx bx-search'></i> Data tidak ditemukan</td>`;
                tbody.appendChild(msgRow);
            }

            // Update Footer Total Kiri
            const footerKiri = document.getElementById('totalKiri');
            if(footerKiri) footerKiri.textContent = formatRupiah(sumTotal);
            
            // Update Footer Total Kanan
            const footerKanan = document.getElementById('totalKanan');
            if(footerKanan) footerKanan.textContent = formatRupiah(sumMasuk - sumKeluar);

            // Update Total Berat
            const footerBerat = document.getElementById('totalBerat');
            if(footerBerat) footerBerat.textContent = sumBerat.toFixed(1) + ' Kg';
        });
    }


    // =================================================
    // 2. LOGIKA TAB BULANAN (TAMPILKAN DATA)
    // =================================================
    const btnTampilkan = document.getElementById('btnTampilkanBulan');
    const filterBulan = document.getElementById('filterBulan');
    
    if(btnTampilkan && filterBulan) {
        // Jalankan awal
        filterLaporanBulanan();

        btnTampilkan.addEventListener('click', function() {
            const oriText = btnTampilkan.innerHTML;
            btnTampilkan.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Loading...";
            setTimeout(() => {
                filterLaporanBulanan();
                btnTampilkan.innerHTML = oriText;
            }, 300);
        });
    }

    function filterLaporanBulanan() {
        const filterBulan = document.getElementById('filterBulan');
        if(!filterBulan) return;

        const selectedBulan = filterBulan.value;
        const tbody = document.getElementById('tbodyBulanan');
        const rows = tbody.querySelectorAll('tr.row-bulan');
        let totalMasuk = 0; let totalKeluar = 0; let visibleRows = 0;

        const oldMsg = tbody.querySelector('.no-data-msg-row');
        if(oldMsg) oldMsg.remove();

        rows.forEach(row => {
            if (row.getAttribute('data-bulan') === selectedBulan) {
                row.style.display = '';
                visibleRows++;
                
                const m = parseInt(row.querySelector('.val-masuk').textContent.replace(/\D/g,''));
                const k = parseInt(row.querySelector('.val-keluar').textContent.replace(/\D/g,''));
                totalMasuk += m;
                totalKeluar += k;
            } else {
                row.style.display = 'none';
            }
        });

        if(visibleRows === 0) {
            const msgRow = document.createElement('tr');
            msgRow.className = 'no-data-msg-row';
            msgRow.innerHTML = `<td colspan="4" class="no-data-msg" style="text-align: center; padding: 20px; color: #999; font-style: italic;">Belum ada rekap untuk bulan ini</td>`;
            tbody.appendChild(msgRow);
        }

        document.getElementById('grandMasuk').textContent = formatRupiah(totalMasuk);
        document.getElementById('grandKeluar').textContent = formatRupiah(totalKeluar);
        document.getElementById('grandBersih').textContent = formatRupiah(totalMasuk - totalKeluar);
        
        // Format ulang tampilan angka di tabel biar cantik (tambah Rp)
        rows.forEach(row => {
            if(row.style.display !== 'none') {
                const cellM = row.querySelector('.val-masuk');
                const cellK = row.querySelector('.val-keluar');
                const cellB = row.querySelector('.text-blue'); // Kolom Bersih
                
                if(!cellM.textContent.includes('Rp')) {
                    const m = parseInt(cellM.textContent);
                    const k = parseInt(cellK.textContent);
                    cellM.textContent = formatRupiah(m);
                    cellK.textContent = formatRupiah(k);
                    if(cellB) cellB.textContent = formatRupiah(m - k);
                }
            }
        });
    }

    // Helper Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }
});

// =================================================
// 3. LOGIKA TAB SWITCHING (Global Function)
// =================================================
// Kita tempel ke window agar bisa dipanggil via onclick di HTML
window.openTab = function(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
};