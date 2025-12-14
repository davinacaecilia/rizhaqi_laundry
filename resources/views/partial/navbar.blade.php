<nav>
    <i class='bx bx-menu'></i>
    <input type="checkbox" id="switch-mode" hidden>
    <label for="switch-mode" class="switch-mode"></label>

    {{-- LOGIKA NOTIFIKASI --}}
    @if(auth()->user()->role !== 'admin') 
        <div class="notif-wrapper">
            <a href="#" class="notification" id="notifDropdownBtn">
                <i class='bx bxs-bell'></i>
                @if($notifCount > 0)
                    <span class="num">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                @endif
            </a>

            {{-- DROPDOWN MENU --}}
            <div class="notif-dropdown">
                <div class="notif-header">
                    @if(auth()->user()->role === 'owner')
                        <span>Aktivitas Terbaru</span>
                    @else
                        <span>Siap Disetrika</span>
                    @endif
                </div>

                <div class="notif-list">
                    @forelse($notifData as $item)
                        
                        {{-- TAMPILAN UNTUK OWNER (LOG) --}}
                        @if($notifType === 'log')
                            <div class="notif-item">
                                <div class="icon-circle log">
                                    <i class='bx bx-history'></i>
                                </div>
                                <div class="text">
                                    <p class="main-text">{{ Str::limit($item->keterangan, 40) }}</p>
                                    <p class="sub-text">
                                        {{ $item->user->nama ?? 'Sistem' }} • {{ \Carbon\Carbon::parse($item->waktu)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                        {{-- TAMPILAN UNTUK PEGAWAI (TRANSAKSI) --}}
                        @elseif($notifType === 'task')
                            <a href="{{ route('admin.transaksi.index') }}?search={{ $item->kode_invoice }}" class="notif-item link">
                                <div class="icon-circle task">
                                    <i class='bx bxs-t-shirt'></i>
                                </div>
                                <div class="text">
                                    <p class="main-text">Invoice <b>{{ $item->kode_invoice }}</b></p>
                                    <p class="sub-text">
                                        Siap Disetrika • {{ $item->berat }} Kg
                                    </p>
                                </div>
                            </a>
                        @endif

                    @empty
                        <div class="notif-item empty">
                            <p>Tidak ada pemberitahuan baru.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <div class="profile-info">
        <span class="user-name">{{ auth()->user()->nama ?? 'Guest' }}</span> 
        <span class="user-role">({{ ucfirst(auth()->user()->role ?? 'user') }})</span> 
        <a href="#" class="profile-image">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama ?? 'Guest') }}&background=random&color=fff&size=128" alt="User Profile"> 
        </a>
    </div>
</nav>

{{-- STYLE TAMBAHAN KHUSUS NAVBAR --}}
<style>
    /* CSS Profile Lama */
    .profile-info { display: flex; align-items: center; gap: 10px; margin-left: 20px; font-family: var(--roboto); color: var(--text-secondary); font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .profile-info .user-name { font-weight: 500; color: var(--text-primary); }
    .profile-info .user-role { font-size: 12px; color: var(--text-tertiary); }
    .profile-info .profile-image img { width: 32px; height: 32px; object-fit: cover; border-radius: 50%; border: 2px solid var(--accent-blue); transition: all 0.2s ease; }
    .profile-info .profile-image img:hover { box-shadow: var(--shadow-medium); transform: scale(1.05); }
    #content nav { justify-content: flex-end; position: relative; }
    #content nav .bx.bx-menu { margin-right: auto; }

    /* CSS NOTIFIKASI DROPDOWN */
    .notif-wrapper { position: relative; }
    
    .notif-dropdown {
        position: absolute;
        top: 45px;
        right: -80px; /* Geser sedikit biar pas */
        width: 300px;
        background: var(--primary-white);
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1000;
        border: 1px solid var(--border-light);
    }

    /* Tampilkan dropdown saat hover bell atau wrapper */
    .notif-wrapper:hover .notif-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .notif-header {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        font-weight: 600;
        font-size: 14px;
        color: var(--text-primary);
        background: #f8f9fa;
        border-radius: 10px 10px 0 0;
    }

    .notif-list { max-height: 300px; overflow-y: auto; }

    .notif-item {
        display: flex;
        align-items: flex-start;
        padding: 12px 15px;
        border-bottom: 1px solid #f1f1f1;
        gap: 12px;
        transition: background 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .notif-item.link:hover { background: #f0f7ff; cursor: pointer; }
    .notif-item:last-child { border-bottom: none; }

    .icon-circle {
        min-width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .icon-circle.log { background: #fff3e0; color: #f57c00; }
    .icon-circle.task { background: #e3f2fd; color: #2196f3; }

    .notif-item .text { display: flex; flex-direction: column; }
    .notif-item .main-text { font-size: 13px; font-weight: 500; color: #333; margin-bottom: 4px; line-height: 1.3; }
    .notif-item .sub-text { font-size: 11px; color: #888; }
    
    .notif-item.empty { text-align: center; color: #999; font-size: 13px; justify-content: center; padding: 20px; }
</style>