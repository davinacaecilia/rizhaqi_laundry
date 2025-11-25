// Sidebar menu active class toggle
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item => {
    const li = item.parentElement;

    item.addEventListener('click', function () {
        // Hapus class 'active' dari semua item menu utama
        allSideMenu.forEach(i => {
            i.parentElement.classList.remove('active');
        });
        // Tambahkan class 'active' ke item yang diklik
        li.classList.add('active');
    });
});

// TOGGLE SIDEBAR (untuk menyembunyikan/menampilkan sidebar utama)
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

if (menuBar && sidebar) {
    menuBar.addEventListener('click', function () {
        sidebar.classList.toggle('hide');
    });
}

// SEARCH BUTTON TOGGLE (responsive)
const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

if (searchButton && searchButtonIcon && searchForm) {
    searchButton.addEventListener('click', function (e) {
        if (window.innerWidth < 576) {
            e.preventDefault(); // Mencegah form submit
            searchForm.classList.toggle('show');
            if (searchForm.classList.contains('show')) {
                searchButtonIcon.classList.replace('bx-search', 'bx-x');
            } else {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
            }
        }
    });
}

// Responsive sidebar & search form pada saat load/resize
if (window.innerWidth < 768) {
    if (sidebar) {
        sidebar.classList.add('hide'); // Sembunyikan sidebar di layar kecil
    }
} else if (window.innerWidth > 576) {
    if (searchButtonIcon && searchForm) {
        searchButtonIcon.classList.replace('bx-x', 'bx-search');
        searchForm.classList.remove('show'); // Sembunyikan search form di layar besar
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

// DARK MODE TOGGLE
const switchMode = document.getElementById('switch-mode');

if (switchMode) {
    // Load state from localStorage
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

// Fungsionalitas Search Icon di Table Head
const tableSearchIcon = document.getElementById('tableSearchIcon');
const tableSearchInput = document.getElementById('tableSearchInput');

if (tableSearchIcon && tableSearchInput) {
    tableSearchIcon.addEventListener('click', function() {
        tableSearchInput.classList.toggle('show');
        if (tableSearchInput.classList.contains('show')) {
            tableSearchInput.focus();
        } else {
            tableSearchInput.value = '';
        }
    });

            }