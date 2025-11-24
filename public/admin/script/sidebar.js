document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggles = document.querySelectorAll('.side-menu .has-dropdown .dropdown-toggle');

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah link pindah halaman
            const parentLi = this.closest('.has-dropdown');
            parentLi.classList.toggle('open'); // Toggle class 'open'

            // Opsional: Tutup dropdown lain saat satu dibuka
            // dropdownToggles.forEach(otherToggle => {
            //     const otherParentLi = otherToggle.closest('.has-dropdown');
            //     if (otherParentLi !== parentLi && otherParentLi.classList.contains('open')) {
            //         otherParentLi.classList.remove('open');
            //     }
            // });
        });
    });

    // Opsional: Logika agar dropdown tetap terbuka jika ada item aktif di dalamnya
    const activeDropdowns = document.querySelectorAll('.side-menu .has-dropdown.active');
    activeDropdowns.forEach(dropdown => {
        dropdown.classList.add('open');
    });
});