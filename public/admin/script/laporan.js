document.addEventListener("DOMContentLoaded", function() {
    
    // =================================================
    // 1. UI SEARCH BAR (Hanya efek buka tutup)
    // =================================================
    const toggleIcon = document.getElementById('tableSearchIcon');
    const searchField = document.getElementById('laporanSearch');
    
    if(toggleIcon && searchField) {
        toggleIcon.addEventListener('click', function() {
            searchField.classList.toggle('show');
            if(searchField.classList.contains('show')) searchField.focus();
        });
    }

    // =================================================
    // 2. CLIENT SIDE SEARCH (Opsional: Cari teks di tabel aktif)
    // =================================================
    if(searchField) {
        searchField.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            
            // Hanya cari di tab yang sedang aktif (biar gak bentrok)
            let activeTab = document.querySelector('.tab-content[style*="block"]');
            if(!activeTab) activeTab = document.querySelector('.tab-content.active');
            
            if(activeTab) {
                let rows = activeTab.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    let text = row.innerText.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            }
        });
    }

    // =================================================
    // 3. LOGIKA TAB SWITCHING (Wajib Ada)
    // =================================================
    window.openTab = function(evt, tabName) {
        // A. Sembunyikan semua konten tab
        var tabcontent = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";     // Sembunyikan visual
            tabcontent[i].classList.remove("active"); // Hapus class active
        }

        // B. Matikan style aktif di tombol
        var tablinks = document.getElementsByClassName("tab-btn");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
            tablinks[i].style.color = "#666"; 
            tablinks[i].style.borderBottomColor = "transparent";
        }

        // C. Tampilkan tab yang dipilih
        var selectedTab = document.getElementById(tabName);
        selectedTab.style.display = "block";   // Tampilkan visual
        selectedTab.classList.add("active");   // Tambah class active (PENTING BUAT CSS CETAK)

        // D. Set style tombol aktif
        evt.currentTarget.classList.add("active");
        evt.currentTarget.style.color = "var(--accent-blue)";
        evt.currentTarget.style.borderBottomColor = "var(--accent-blue)";
    };
});