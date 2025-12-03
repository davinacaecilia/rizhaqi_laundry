// =========================================================
// SCRIPT JS FINAL - FIX PENCARIAN KETERANGAN
// =========================================================

// --- BAGIAN 1: LOGIKA BAWAAN (SIDEBAR, NAVBAR, DLL) ---
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');
allSideMenu.forEach(item => {
    const li = item.parentElement;
    item.addEventListener('click', function () {
        allSideMenu.forEach(i => i.parentElement.classList.remove('active'));
        li.classList.add('active');
    });
});

const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');
if (menuBar && sidebar) {
    menuBar.addEventListener('click', function () {
        sidebar.classList.toggle('hide');
    });
}

const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');
if (searchButton && searchButtonIcon && searchForm) {
    searchButton.addEventListener('click', function (e) {
        if (window.innerWidth < 576) {
            e.preventDefault();
            searchForm.classList.toggle('show');
            if (searchForm.classList.contains('show')) {
                searchButtonIcon.classList.replace('bx-search', 'bx-x');
            } else {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
            }
        }
    });
}

if (window.innerWidth < 768) {
    if (sidebar) sidebar.classList.add('hide');
} else if (window.innerWidth > 576) {
    if (searchButtonIcon && searchForm) {
        searchButtonIcon.classList.replace('bx-x', 'bx-search');
        searchForm.classList.remove('show');
    }
}

window.addEventListener('resize', function () {
    if (window.innerWidth > 576) {
        if (searchButtonIcon && searchForm) {
            searchButtonIcon.classList.replace('bx-x', 'bx-search');
            searchForm.classList.remove('show');
        }
    }
});

const switchMode = document.getElementById('switch-mode');
if (switchMode) {
    if (localStorage.getItem('dark-mode') === 'enabled') {
        document.body.classList.add('dark');
        switchMode.checked = true;
    }
    switchMode.addEventListener('change', function () {
        if (this.checked) {
            document.body.classList.add('dark');
            localStorage.setItem('dark-mode', 'enabled');
        } else {
            document.body.classList.remove('dark');
            localStorage.setItem('dark-mode', 'disabled');
        }
    });
}

// --- BAGIAN 2: LOGIKA FILTER TABEL (YANG DIPERBAIKI) ---

document.addEventListener("DOMContentLoaded", function () {
    const tableSearchIcon = document.getElementById('tableSearchIcon');
    const tableSearchInput = document.getElementById('tableSearchInput');
    const dateFilter = document.getElementById('dateFilter');
    const statusDropdown = document.getElementById('statusFilter'); 
    const statusPills = document.querySelectorAll('.filter-pill'); 

    // A. Toggle Search Bar
    if (tableSearchIcon && tableSearchInput) {
        tableSearchIcon.addEventListener('click', function () {
            tableSearchInput.classList.toggle('show');
            if (tableSearchInput.classList.contains('show')) {
                tableSearchInput.focus();
            } else {
                tableSearchInput.value = '';
                tableSearchInput.blur();
                filterTable(); 
            }
        });
    }

    // B. Logic Filter Pills (Status)
    if (statusPills.length > 0) {
        statusPills.forEach(pill => {
            pill.addEventListener('click', function() {
                this.classList.toggle('active');
                filterTable(); 
            });
        });
    }

    // C. Fungsi Filter Utama
    function filterTable() {
        const nameValue = tableSearchInput ? tableSearchInput.value.toLowerCase() : '';
        const dateValue = dateFilter ? dateFilter.value : ''; 

        let dropdownValue = '';
        let activePillValues = [];

        if (statusDropdown) dropdownValue = statusDropdown.value.toLowerCase();
        
        const activePills = document.querySelectorAll('.filter-pill.active');
        activePills.forEach(pill => {
            activePillValues.push(pill.dataset.status.toLowerCase());
        });

        const rows = document.querySelectorAll('.table-container tbody tr:not(.no-data-row)');
        let visibleCount = 0;

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            
            // Pastikan baris memiliki data
            if (cells.length > 1) {
                let textPencarian = '';
                let textTanggal = '';
                let textStatus = '';

                // --- [PERBAIKAN FOKUS DISINI] ---
                
                // KASUS 1: TABEL PENGELUARAN (6 KOLOM)
                // Urutan: [0]ID, [1]Tanggal, [2]Keterangan, [3]Pencatat, [4]Jumlah, [5]Aksi
                if (cells.length === 6) {
                     textTanggal = cells[1].textContent.trim();
                     // HANYA AMBIL KOLOM KETERANGAN (Index 2)
                     textPencarian = cells[2].textContent.toLowerCase();
                }
                
                // KASUS 2: TABEL TRANSAKSI/ORDER (7 KOLOM)
                // Urutan: [0]Inv, [1]Nama, [2]Tgl Masuk, [3]Tgl Keluar, [4]Status, [5]Biaya, [6]Aksi
                else if (cells.length === 7) { 
                    textPencarian = cells[1].textContent.toLowerCase(); // Nama Pelanggan
                    textTanggal = cells[2].textContent.trim();
                    textStatus = cells[4].textContent.toLowerCase(); 
                }

                // KASUS 3: STATUS ORDER (5 KOLOM - FORMAT LAMA)
                else if (cells.length === 5) { 
                    textPencarian = cells[1].textContent.toLowerCase();
                    textTanggal = cells[2].textContent; 
                    textStatus = cells[3].textContent.toLowerCase();
                }

                // --- LOGIKA PENCOCOKAN ---
                const matchName = textPencarian.includes(nameValue);

                let matchDate = true;
                if (dateValue !== '') {
                    // Logic parsing tanggal (mengatasi format teks seperti "29 Nov 2025")
                    const rowDateText = textTanggal; 
                    const rowDateObj = new Date(rowDateText);
                    const filterDateObj = new Date(dateValue);
                    
                    rowDateObj.setHours(0,0,0,0);
                    filterDateObj.setHours(0,0,0,0);
                    
                    if (!isNaN(rowDateObj)) {
                        if (rowDateObj.getTime() !== filterDateObj.getTime()) {
                            matchDate = false;
                        }
                    }
                }

                let matchStatus = true;
                if (statusDropdown) {
                    matchStatus = dropdownValue === '' || textStatus.includes(dropdownValue);
                } else if (statusPills.length > 0) {
                    if (activePillValues.length > 0) {
                        matchStatus = activePillValues.some(val => textStatus.includes(val));
                    }
                }

                // --- KEPUTUSAN ---
                if (matchName && matchDate && matchStatus) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            }
        });

        updateEmptyState(visibleCount);
    }

    // D. Update Empty State (Pesan Error Tengah)
    function updateEmptyState(count) {
        const tableContainer = document.querySelector('.table-container');
        if (!tableContainer) return;

        const tbody = tableContainer.querySelector('tbody');
        if (!tbody) return;

        let emptyOverlay = document.getElementById('emptyStateOverlay');

        if (count === 0) {
            const allRows = tbody.querySelectorAll('tr:not(.no-data-row)');
            allRows.forEach(row => row.style.display = 'none');

            if (!emptyOverlay) {
                emptyOverlay = document.createElement('div');
                emptyOverlay.id = 'emptyStateOverlay';
                emptyOverlay.style.cssText = `
                    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                    z-index: 10; text-align: center; width: 100%; padding: 40px 20px; pointer-events: none;
                `;
                emptyOverlay.innerHTML = `
                    <div style="display: inline-block; padding: 20px; pointer-events: auto;">
                        <i class='bx bx-search-alt' style="font-size: 64px; color: #cbd5e1; margin-bottom: 16px; display: block;"></i>
                        <h4 style="font-size: 18px; font-weight: 600; color: #475569; margin: 0 0 8px 0;">Ups! Data tidak ditemukan</h4>
                        <p style="font-size: 14px; color: #94a3b8; margin: 0; max-width: 400px;">Coba ubah kata kunci pencarian atau atur ulang filter.</p>
                    </div>
                `;
                tableContainer.style.position = 'relative';
                if (tableContainer.offsetHeight < 300) tableContainer.style.minHeight = '300px'; 
                tableContainer.appendChild(emptyOverlay);
            }
            emptyOverlay.style.display = 'block';
        } else {
            if (emptyOverlay) emptyOverlay.style.display = 'none';
            tableContainer.style.minHeight = '';
        }
    }

    if (tableSearchInput) tableSearchInput.addEventListener('keyup', filterTable);
    if (dateFilter) dateFilter.addEventListener('change', filterTable);
    if (statusDropdown) statusDropdown.addEventListener('change', filterTable);
});