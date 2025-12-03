document.addEventListener("DOMContentLoaded", function () {
    // =========================================================
    // 1. LOGIKA TOGGLE SEARCH BAR (ANIMASI MELEBAR)
    // =========================================================
    const searchIcon = document.getElementById('tableSearchIcon');
    const searchInput = document.getElementById('tableSearchInput');

    if (searchIcon && searchInput) {
        searchIcon.addEventListener('click', function () {
            // Toggle class 'show' untuk memicu transisi CSS width
            searchInput.classList.toggle('show');
            
            // Jika terbuka, langsung fokus ke input agar user bisa ketik
            if (searchInput.classList.contains('show')) {
                searchInput.focus();
            }
        });
    }

    // =========================================================
    // 2. LOGIKA PENCARIAN LIVE (CLIENT SIDE)
    // =========================================================
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const filter = searchInput.value.toLowerCase();
            
            // Ambil semua baris di dalam tbody tabel manapun yang ada di class .table-container
            const rows = document.querySelectorAll('.table-container tbody tr');

            rows.forEach(row => {
                // Ambil seluruh teks di dalam baris tersebut
                const text = row.textContent.toLowerCase();

                // Jika teks cocok, tampilkan. Jika tidak, sembunyikan (display: none)
                if (text.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    }
});