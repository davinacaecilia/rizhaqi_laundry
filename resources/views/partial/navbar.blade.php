<nav>
    <i class='bx bx-menu'></i>
    <input type="checkbox" id="switch-mode" hidden>
    <label for="switch-mode" class="switch-mode"></label>

    <a href="#" class="notification">
        <i class='bx bxs-bell'></i>
        <span class="num">8</span>
    </a>

    {{-- Bagian Profil Pengguna (DINAMIS - BACKEND CONNECTED) --}}
    <div class="profile-info">
        
        {{-- 1. Menampilkan Nama User yang Login --}}
        {{-- auth()->user()->nama : Mengambil kolom 'nama' dari tabel users --}}
        <span class="user-name">
            {{ auth()->user()->nama ?? 'Guest' }}
        </span> 

        {{-- 2. Menampilkan Role (Admin/Owner/Pegawai) --}}
        {{-- ucfirst() bikin huruf depan jadi kapital --}}
        <span class="user-role">
            ({{ ucfirst(auth()->user()->role ?? 'user') }})
        </span> 

        <a href="#" class="profile-image">
            {{-- 3. Gambar Profil Otomatis Berdasarkan Inisial Nama --}}
            {{-- Kita pakai layanan UI Avatars biar gak repot upload foto --}}
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama ?? 'Guest') }}&background=random&color=fff&size=128" 
                alt="User Profile"> 
        </a>
    </div>

</nav>
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