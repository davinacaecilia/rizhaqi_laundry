<section id="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="brand">
        <i class='bx bxs-store-alt'></i> <span class="text">
            <span class="octa">Rizhaqi </span><span class="prime">Laundry</span>
        </span>
    </a>
    <ul class="side-menu top">
        <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>

        <li class="{{ Request::is('admin/transaksi*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-credit-card'></i>
                <span class="text">Manajemen Transaksi</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>
            <ul class="submenu">
                {{-- Data Transaksi (Index) --}}
                <li class="{{ Request::is('admin/transaksi') && !Request::is('admin/transaksi/*') ? 'active' : '' }}">
                    <a href="{{ route('admin.transaksi.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Data Transaksi</span>
                    </a>
                </li>

                {{-- Tambah Order (Create) --}}
                <li class="{{ Request::is('admin/transaksi/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.transaksi.create') }}">
                        <i class='bx bx-plus-circle'></i> <span class="text">Tambah Order</span>
                    </a>
                </li>

                {{-- Status Order --}}
                <li class="{{ Request::is('admin/transaksi/status*') ? 'active' : '' }}">
                    <a href="{{ route('admin.transaksi.status') }}">
                        <i class='bx bx-check-shield'></i> <span class="text">Status Order</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ Request::is('admin/pegawai*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-user-badge'></i>
                <span class="text">Manajemen Pegawai</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>
            <ul class="submenu">
                <li class="{{ Request::is('admin/pegawai') && !Request::is('admin/pegawai/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.pegawai.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Data Pegawai</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/pegawai/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.pegawai.create') }}">
                        <i class='bx bx-user-plus'></i> <span class="text">Tambah Pegawai</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ Request::is('admin/pelanggan*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-group'></i>
                <span class="text">Manajemen Pelanggan</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>
            <ul class="submenu">
                <li class="{{ Request::is('admin/pelanggan') && !Request::is('admin/pelanggan/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.pelanggan.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Data Pelanggan</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/pelanggan/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.pelanggan.create') }}">
                        <i class='bx bx-user-plus'></i> <span class="text">Tambah Pelanggan</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ Request::is('admin/layanan*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-cog'></i>
                <span class="text">Manajemen Layanan</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>
            <ul class="submenu">
                <li class="{{ Request::is('admin/layanan') && !Request::is('admin/layanan/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.layanan.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Daftar Harga</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/layanan/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.layanan.create') }}">
                        <i class='bx bx-plus-circle'></i> <span class="text">Tambah Layanan</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ Request::is('admin/alat*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-wrench'></i>
                <span class="text">Manajemen Alat</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>
            <ul class="submenu">
                <li class="{{ Request::is('admin/alat') && !Request::is('admin/alat/create') && !Request::is('admin/alat/stok') ? 'active' : '' }}">
                    <a href="{{ route('admin.alat.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Data Alat</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/alat/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.alat.create') }}">
                        <i class='bx bx-plus-circle'></i> <span class="text">Tambah Alat</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ Request::is('admin/pengeluaran*') ? 'active' : '' }} has-dropdown">
            <a href="#" class="dropdown-toggle">
                <i class='bx bxs-wallet-alt'></i> <span class="text">Manajemen Pengeluaran</span>
                <i class='bx bx-chevron-down toggle-icon'></i>
            </a>
            <ul class="submenu">
                <li class="{{ Request::is('admin/pengeluaran') && !Request::is('admin/pengeluaran/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.pengeluaran.index') }}">
                        <i class='bx bx-list-ul'></i> <span class="text">Riwayat Pengeluaran</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/pengeluaran/create') ? 'active' : '' }}">
                    <a href="{{ route('admin.pengeluaran.create') }}">
                        <i class='bx bx-plus-circle'></i> <span class="text">Catat Pengeluaran</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="{{ Request::is('admin/laporan*') ? 'active' : '' }}">
            <a href="{{ route('admin.laporan.index') }}"> <i class='bx bxs-report'></i>
                <span class="text">Laporan Keuangan</span>
            </a>
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