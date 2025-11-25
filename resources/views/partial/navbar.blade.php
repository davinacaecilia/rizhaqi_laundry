<!-- NAVBAR -->
<nav>
    <i class='bx bx-menu'></i>
    <!-- Link kategori dan form pencarian -->

    <input type="checkbox" id="switch-mode" hidden>
    <label for="switch-mode" class="switch-mode"></label>

    <a href="#" class="notification">
        <i class='bx bxs-bell'></i>
        <span class="num">8</span>
    </a>

    {{-- Bagian Profil Pengguna (Front-End Design Only) --}}
    <div class="profile-info">
        <span class="user-name">John</span> {{-- Nama pengguna statis --}}
        <span class="user-role">(admin)</span> {{-- Level pengguna statis (contoh) --}}
        <a href="#" class="profile-image">
            <img src="https://placehold.co/32x32/cccccc/333333?text=PJ" alt="User Profile"> {{-- Gambar profil statis --}}
        </a>
    </div>

</nav>
<!-- NAVBAR -->

<style>
    .profile-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: 20px; 
        font-family: var(--roboto);
        color: var(--text-secondary);
        font-size: 14px;
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis;
    }

    .profile-info .user-name {
        font-weight: 500;
        color: var(--text-primary);
    }

    .profile-info .user-role {
        font-size: 12px;
        color: var(--text-tertiary);
    }

    .profile-info .profile-image img {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid var(--accent-blue); 
        transition: all 0.2s ease;
    }

    .profile-info .profile-image img:hover {
        box-shadow: var(--shadow-medium);
        transform: scale(1.05);
    }

    #content nav {
        justify-content: flex-end;
    }
    #content nav .bx.bx-menu {
        margin-right: auto; 
    }
</style>
