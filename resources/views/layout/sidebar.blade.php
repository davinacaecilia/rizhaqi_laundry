<section id="sidebar">
    <a href="{{ route('pegawai.dashboard') }}" class="brand">
        <i class='bx bxs-store-alt'></i> <span class="text">
            <span class="octa">Rizhaqi </span><span class="prime">Laundry</span>
        </span>
    </a>
    <ul class="side-menu top">
        <li class="{{ Request::is('pegawai/dashboard') ? 'active' : '' }}">
            <a href="{{ route('pegawai.dashboard') }}">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>

        <li class="{{ Request::is('pegawai/transaksi*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-credit-card'></i>
                <span class="text">Manajemen Transaksi</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>
            <ul class="submenu">
                {{-- Data Transaksi (Index) --}}
                <li class="{{ Request::is('pegawai/transaksi') && !Request::is('pegawai/transaksi/*') ? 'active' : '' }}">
                    <a href="{{ route('pegawai.transaksi.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Data Transaksi</span>
                    </a>
                </li>

                {{-- Status Order --}}
                <li class="{{ Request::is('pegawai/transaksi/status*') ? 'active' : '' }}">
                    <a href="{{ route('pegawai.transaksi.status') }}">
                        <i class='bx bx-check-shield'></i> <span class="text">Status Order</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ Request::is('pegawai/pelanggan*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-group'></i>
                <span class="text">Manajemen Pelanggan</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>
            <ul class="submenu">
                <li class="{{ Request::is('pegawai/pelanggan') && !Request::is('pegawai/pelanggan/create') ? 'active' : '' }}">
                    <a href="{{ route('pegawai.pelanggan.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Data Pelanggan</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ Request::is('pegawai/cucian*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-washer'></i>
                <span class="text">Manajemen Cucian</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>

            <ul class="submenu">
                <li class="{{ Request::is('pegawai/cucian') ? 'active' : '' }}">
                    <a href="{{ route('pegawai.cucian.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Data Cucian</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <ul class="side-menu">
        <li>
            <a href="{{ route('logout') }}" class="logout"
            onclick="event.preventDefault(); if(confirm('Yakin ingin keluar?')) { document.getElementById('logout-form').submit(); }">
                <i class='bx bxs-log-out-circle'></i>
                <span class="text">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</section>
