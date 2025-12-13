// ========================================
// PAGINATION LOGIC - Auto Work
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Deteksi pagination links
    const paginationLinks = document.querySelectorAll('.pagination-wrapper a:not(.disabled)');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Jika link adalah arrow/number biasa, biarkan default behavior (pindah halaman)
            // Tidak perlu e.preventDefault() karena kita pakai href Laravel
            
            // Optional: Tambahkan loading indicator
            const wrapper = this.closest('.pagination-wrapper');
            if (wrapper) {
                wrapper.style.opacity = '0.6';
                wrapper.style.pointerEvents = 'none';
            }
        });
    });
    
    // Update info text saat halaman load (untuk AJAX pagination jika diperlukan)
    updatePaginationInfo();
});

// Function untuk update info "Showing X-Y of Z" (jika pakai AJAX)
function updatePaginationInfo() {
    const infoElement = document.querySelector('.pagination-info');
    if (!infoElement) return;
    
    // Ambil data dari URL atau element
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = parseInt(urlParams.get('page')) || 1;
    
    // Contoh kalkulasi (sesuaikan dengan data Anda)
    const totalRows = document.querySelectorAll('.table-container tbody tr:not(.no-data-row)').length;
    const perPage = 10; // Sesuaikan dengan Laravel Paginator
    
    const from = ((currentPage - 1) * perPage) + 1;
    const to = Math.min(currentPage * perPage, totalRows);
    
    // Update text (opsional, karena Laravel sudah inject)
    // infoElement.textContent = `Menampilkan ${from}-${to} dari ${totalRows} data`;
}

// Export function untuk digunakan di halaman lain (optional)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { updatePaginationInfo };
}