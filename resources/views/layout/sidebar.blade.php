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
        
        <li class="{{ Request::is('pegawai/transaksi/status*') ? 'active' : '' }}">
            <a href="{{ route('pegawai.transaksi.status') }}">
                <i class='bx bx-check-shield'></i> 
                <span class="text">Status Order</span>
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
