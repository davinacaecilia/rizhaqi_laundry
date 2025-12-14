<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />
    <title>Status Order - Rizhaqi Laundry Admin</title>

    <style>
        /* CSS LAMA ANDA TETAP (TIDAK BERUBAH) */
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container thead tr { background-color: var(--surface-white); border-bottom: 1px solid var(--border-light); }
        .table-container th, .table-container td { padding: 15px; border: 1px solid var(--border-light); text-align: left; font-size: 14px; color: var(--text-primary); }
        .table-container th { font-weight: 600; color: var(--text-secondary); font-family: var(--google-sans); background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }
        .btn-status { padding: 8px 16px; border-radius: 6px; color: white; font-weight: 500; font-size: 13px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: 0.2s; }
        .btn-status:hover { opacity: 0.9; transform: translateY(-1px); }

        /* WARNA TOMBOL & BADGE */
        .btn-blue { background-color: var(--accent-blue); }
        .btn-orange { background-color: #FF9800; }
        .btn-purple { background-color: #9C27B0; }
        .btn-teal { background-color: #009688; }
        .btn-green { background-color: var(--accent-green); }
        .btn-dark { background-color: #455A64; }
        .btn-red { background-color: var(--accent-red); }
        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; white-space: nowrap; }
        .st-diterima { background: #E0E0E0; color: #424242; }
        .st-dicuci { background: #E3F2FD; color: #1565C0; }
        .st-dikeringkan { background: #FFF3E0; color: #EF6C00; }
        .st-disetrika { background: #F3E5F5; color: #7B1FA2; }
        .st-packing { background: #E0F2F1; color: #00695C; }
        .st-siap { background: #E8F5E9; color: #2E7D32; }
        .st-selesai { background: #212121; color: #fff; }
        .st-dibatalkan { background: #FFEBEE; color: #C62828; text-decoration: line-through; }

        /* FILTER UI */
        .table-data .order .head { display: flex; flex-direction: column; gap: 15px; align-items: flex-start; }
        .filter-row { display: flex; gap: 10px; width: 100%; align-items: center; flex-wrap: wrap; }
        .filter-title { font-size: 20px; font-weight: 600; color: var(--text-primary); margin-right: auto; }
        .filter-date { padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 20px; outline: none; font-size: 13px; color: var(--text-secondary); }
        .status-pills-container { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-pill { padding: 6px 14px; border: 1px solid var(--border-light); border-radius: 20px; font-size: 12px; font-weight: 500; cursor: pointer; background: #fff; color: var(--text-secondary); transition: all 0.2s; user-select: none; }
        .filter-pill:hover { background: #f5f5f5; border-color: #ccc; }
        .filter-pill.active { background: var(--accent-blue); color: white; border-color: var(--accent-blue); }

        /* SEARCH BAR */
        .table-search-wrapper { position: relative; display: flex; align-items: center; margin-left: auto; }
        .table-search-input { width: 0; padding: 0; border: none; margin-left: 0; background: transparent; transition: width 0.3s ease, padding 0.3s ease; opacity: 0; pointer-events: none; height: 40px; border-radius: 20px; }
        .table-search-input.show { width: 200px; padding: 6px 12px; border: 1px solid var(--border-light); margin-right: 10px; opacity: 1; pointer-events: auto; background: var(--surface-white); }
        .table-search-input:focus { border-color: var(--accent-blue); outline: none; }
        .bx-search-toggle { cursor: pointer; font-size: 20px; color: #888; padding: 5px; }

        /* SUMMARY CARDS */
        .status-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; margin-bottom: 24px; list-style: none; padding: 0; }
        .status-summary li { background: #fff; padding: 12px; border-radius: 10px; border: 1px solid var(--border-light); display: flex; align-items: center; gap: 12px; transition: all 0.2s ease; cursor: default; }
        .status-summary li .bx { width: 36px; height: 36px; border-radius: 8px; font-size: 20px; display: flex; align-items: center; justify-content: center; }
        .status-summary li .info h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0; line-height: 1; }
        .status-summary li .info p { font-size: 11px; color: var(--text-secondary); margin-top: 4px; font-weight: 500; }

        /* WARNA SUMMARY ICON */
        .bg-diterima { background: #EEEEEE; color: #616161; }
        .bg-dicuci { background: #E3F2FD; color: #1976D2; }
        .bg-kering { background: #FFF3E0; color: #F57C00; }
        .bg-setrika { background: #F3E5F5; color: #7B1FA2; }
        .bg-packing { background: #E0F2F1; color: #00796B; }
        .bg-siap { background: #E8F5E9; color: #388E3C; }
        .bg-selesai { background: #263238; color: #fff; }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Status Order</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Status Order</a></li>
                    </ul>
                </div>
            </div>

            <ul class="status-summary">
                <li><i class='bx bx-receipt bg-diterima'></i><div class="info"><h3>{{ $counts['diterima'] ?? 0 }}</h3><p>Diterima</p></div></li>
                <li><i class='bx bx-water bg-dicuci'></i><div class="info"><h3>{{ $counts['dicuci'] ?? 0 }}</h3><p>Dicuci</p></div></li>
                <li><i class='bx bx-wind bg-kering'></i><div class="info"><h3>{{ $counts['dikeringkan'] ?? 0 }}</h3><p>Dikeringkan</p></div></li>
                <li><i class='bx bxs-t-shirt bg-setrika'></i><div class="info"><h3>{{ $counts['disetrika'] ?? 0 }}</h3><p>Disetrika</p></div></li>
                <li><i class='bx bx-package bg-packing'></i><div class="info"><h3>{{ $counts['packing'] ?? 0 }}</h3><p>Packing</p></div></li>
                <li><i class='bx bx-check-circle bg-siap'></i><div class="info"><h3>{{ $counts['siap'] ?? 0 }}</h3><p>Siap Ambil</p></div></li>
                <li><i class='bx bx-check-double bg-selesai'></i><div class="info"><h3>{{ $counts['selesai'] ?? 0 }}</h3><p>Selesai</p></div></li>
            </ul>

            <div class="table-data">
                <div class="order">

                    <div class="head">
                        <div class="filter-row">
                            <h3 class="filter-title">Update Status (Real-time)</h3>
                            <div class="table-search-wrapper">
                                <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Nama...">
                                <i class='bx bx-search bx-search-toggle' id="tableSearchIcon"></i>
                            </div>
                        </div>

                        <div class="filter-row">
                            {{-- DATE FILTER (auto handled by script.js) --}}
                            <input type="date" id="dateFilter" class="filter-date" title="Filter Tanggal Masuk">

                            {{-- STATUS PILLS (handled by custom JS below) --}}
                            <div class="status-pills-container">
                                @php $currentStatus = request('status', 'all'); @endphp
                                <!-- <span class="filter-pill {{ $currentStatus == 'all' ? 'active' : '' }}" data-status="all">Semua</span> -->
                                <span class="filter-pill {{ $currentStatus == 'diterima' ? 'active' : '' }}" data-status="diterima">Diterima</span>
                                <span class="filter-pill {{ $currentStatus == 'dicuci' ? 'active' : '' }}" data-status="dicuci">Dicuci</span>
                                <span class="filter-pill {{ $currentStatus == 'dikeringkan' ? 'active' : '' }}" data-status="dikeringkan">Dikeringkan</span>
                                <span class="filter-pill {{ $currentStatus == 'disetrika' ? 'active' : '' }}" data-status="disetrika">Disetrika</span>
                                <span class="filter-pill {{ $currentStatus == 'packing' ? 'active' : '' }}" data-status="packing">Packing</span>
                                <span class="filter-pill {{ $currentStatus == 'siap diambil' ? 'active' : '' }}" data-status="siap diambil">Siap Ambil</span>
                                <span class="filter-pill {{ $currentStatus == 'selesai' ? 'active' : '' }}" data-status="selesai">Selesai</span>
                                <span class="filter-pill {{ $currentStatus == 'batal' ? 'active' : '' }}" data-status="batal">Dibatalkan</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th>Kode Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Status Sekarang</th>
                                    <th>Aksi Selanjutnya</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($transaksi as $item)
                                @php
                                    // 1. LOGIK WARNA BADGE (Sama seperti sebelumnya)
                                    $rawStatus = strtolower($item->status_pesanan ?? 'diterima');
                                    $badgeClass = 'st-diterima';

                                    $mapStatus = [
                                        'diterima' => 'st-diterima', 'dicuci' => 'st-dicuci',
                                        'dikeringkan' => 'st-dikeringkan', 'disetrika' => 'st-disetrika',
                                        'packing' => 'st-packing', 'siap diambil' => 'st-siap',
                                        'selesai' => 'st-selesai', 'batal' => 'st-dibatalkan'
                                    ];
                                    if(isset($mapStatus[$rawStatus])) $badgeClass = $mapStatus[$rawStatus];

                                    // 2. LOGIK STATUS SELANJUTNYA
                                    $btnClass = 'btn-blue'; $btnText = 'Mulai Cuci'; $btnIcon = 'bx-water'; $nextStatus = 'dicuci';
                                    $isCancelled = ($rawStatus == 'batal');
                                    $isFinished = ($rawStatus == 'selesai');

                                    switch($rawStatus) {
                                        case 'diterima': $nextStatus = 'dicuci'; $btnText = 'Mulai Cuci'; $btnClass = 'btn-blue'; $btnIcon = 'bx-water'; break;
                                        case 'dicuci': $nextStatus = 'dikeringkan'; $btnText = 'Ke Pengeringan'; $btnClass = 'btn-orange'; $btnIcon = 'bx-wind'; break;
                                        case 'dikeringkan': $nextStatus = 'disetrika'; $btnText = 'Mulai Setrika'; $btnClass = 'btn-purple'; $btnIcon = 'bxs-t-shirt'; break;
                                        case 'disetrika': $nextStatus = 'packing'; $btnText = 'Mulai Packing'; $btnClass = 'btn-teal'; $btnIcon = 'bx-box'; break;
                                        case 'packing': $nextStatus = 'siap diambil'; $btnText = 'Tandai Siap Ambil'; $btnClass = 'btn-green'; $btnIcon = 'bx-check-circle'; break;
                                        case 'siap diambil': $nextStatus = 'selesai'; $btnText = 'Serahkan (Selesai)'; $btnClass = 'btn-dark'; $btnIcon = 'bx-package'; break;
                                    }

                                    // 3. LOGIK PEMBATASAN (UPDATED)
                                    // Ambil role user yang sedang login
                                    $role = auth()->user()->role; 

                                    // Logika: 
                                    // Jika user BUKAN 'pegawai' (berarti Admin atau Owner)
                                    // DAN status sedang 'disetrika'
                                    // MAKA tombol akan dikunci/dibatasi.
                                    $isRestricted = ($role !== 'pegawai' && $rawStatus == 'disetrika');
                                @endphp

                                <tr>
                                    <td><strong>{{ $item->kode_invoice }}</strong></td>
                                    <td>{{ optional($item->pelanggan)->nama ?? 'Tanpa Nama' }}</td>
                                    <td>{{ date('d M Y', strtotime($item->tgl_masuk)) }}</td>
                                    <td>
                                        <span class="status-badge {{ $badgeClass }}">{{ ucwords($rawStatus) }}</span>
                                    </td>
                                    <td>
                                        @if($isCancelled)
                                            <span style="color: #C62828; font-size: 13px; font-weight: 600;">
                                                <i class='bx bx-x-circle'></i> Order Dibatalkan
                                            </span>

                                        @elseif($isFinished)
                                            <span style="color: #2E7D32; font-size: 13px; font-weight: 600;">
                                                <i class='bx bx-check-double'></i> Transaksi Selesai
                                            </span>

                                        @elseif($isRestricted)
                                            {{-- TAMPILAN UNTUK ADMIN & OWNER (Saat status Disetrika) --}}
                                            <span style="color: #FF9800; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; background: #FFF3E0; padding: 6px 12px; border-radius: 6px;">
                                                <i class='bx bx-time-five'></i> Menunggu Pegawai (Packing)
                                            </span>

                                        @else
                                            {{-- TAMPILAN UNTUK PEGAWAI (Atau status selain Disetrika) --}}
                                            <form action="{{ route('admin.transaksi.updateStatus', $item->id_transaksi) }}" method="POST">
                                                @csrf 
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                <button type="submit" class="btn-status {{ $btnClass }}"
                                                    onclick="return confirm('Update status ke {{ ucfirst($nextStatus) }}?')">
                                                    <i class='bx {{ $btnIcon }}'></i> {{ $btnText }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>

                
                </div>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
    <script src="{{ asset('admin/script/pagination.js') }}"></script>

</body>
</html>
