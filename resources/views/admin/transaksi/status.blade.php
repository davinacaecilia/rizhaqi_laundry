<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Update Status Order - Rizhaqi Laundry Admin</title>
    
    <style>
        /* CSS KHUSUS STATUS */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .status-card {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .status-icon {
            width: 40px; height: 40px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        
        /* WARNA ICON */
        .ic-diterima { background: #E0E0E0; color: #616161; }
        .ic-dicuci { background: #E3F2FD; color: #1976D2; }
        .ic-dikeringkan { background: #FFF3E0; color: #F57C00; }
        .ic-disetrika { background: #F3E5F5; color: #7B1FA2; }
        .ic-packing { background: #E0F2F1; color: #00796B; }
        .ic-siap { background: #E8F5E9; color: #388E3C; }
        .ic-selesai { background: #212121; color: #fff; }

        .status-info h3 { font-size: 20px; font-weight: 700; margin: 0; }
        .status-info p { font-size: 12px; color: #888; margin: 0; }

        /* TABLE STYLING */
        .table-status td { vertical-align: middle; }
        .badge-status { padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block; }
        
        /* STATUS COLORS */
        .st-diterima { background: #E0E0E0; color: #424242; }
        .st-dicuci { background: #E3F2FD; color: #1565C0; }
        .st-dikeringkan { background: #FFF3E0; color: #EF6C00; }
        .st-disetrika { background: #F3E5F5; color: #7B1FA2; }
        .st-packing { background: #E0F2F1; color: #00695C; }
        .st-siap { background: #E8F5E9; color: #2E7D32; }
        .st-selesai { background: #212121; color: #fff; }
        .st-dibatalkan { background: #FFEBEE; color: #C62828; text-decoration: line-through; }

        .btn-update {
            padding: 6px 12px;
            background: var(--accent-blue);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-update:hover { opacity: 0.9; }
        .btn-selesai { background: var(--accent-green); }
        .btn-disabled { background: #ccc; cursor: not-allowed; }

        /* FILTER BUTTONS */
        .filter-status-group { display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 15px; }
        .btn-filter-status { 
            padding: 6px 12px; border: 1px solid var(--border-light); 
            background: #fff; border-radius: 20px; font-size: 12px; cursor: pointer; 
            transition: all 0.2s;
        }
        .btn-filter-status.active { background: var(--accent-blue); color: white; border-color: var(--accent-blue); }

        /* --- PERBAIKAN SEARCH BAR (YANG HILANG TADI) --- */
        .table-search-wrapper { position: relative; display: flex; align-items: center; }
        .table-search-input { width: 0; padding: 0; border: none; background: transparent; transition: all 0.3s ease; opacity: 0; height: 40px; border-radius: 20px; pointer-events: none; }
        /* Class .show ini akan di-toggle oleh script.js */
        .table-search-input.show { width: 200px; padding: 6px 12px; border: 1px solid var(--border-light); margin-right: 10px; opacity: 1; pointer-events: auto; background: #fff; }
        .bx-search-toggle { cursor: pointer; font-size: 20px; color: #888; padding: 5px; }
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
                        <li><a class="active" href="#">Update Status</a></li>
                    </ul>
                </div>
            </div>

            <div class="status-grid">
                <div class="status-card">
                    <div class="status-icon ic-diterima"><i class='bx bx-receipt'></i></div>
                    <div class="status-info"><h3>{{ $counts['diterima'] ?? 0 }}</h3><p>Diterima</p></div>
                </div>
                <div class="status-card">
                    <div class="status-icon ic-dicuci"><i class='bx bx-water'></i></div>
                    <div class="status-info"><h3>{{ $counts['dicuci'] ?? 0 }}</h3><p>Dicuci</p></div>
                </div>
                <div class="status-card">
                    <div class="status-icon ic-dikeringkan"><i class='bx bx-wind'></i></div>
                    <div class="status-info"><h3>{{ $counts['dikeringkan'] ?? 0 }}</h3><p>Dikeringkan</p></div>
                </div>
                <div class="status-card">
                    <div class="status-icon ic-disetrika"><i class='bx bx-closet'></i></div>
                    <div class="status-info"><h3>{{ $counts['disetrika'] ?? 0 }}</h3><p>Disetrika</p></div>
                </div>
                <div class="status-card">
                    <div class="status-icon ic-packing"><i class='bx bx-package'></i></div>
                    <div class="status-info"><h3>{{ $counts['packing'] ?? 0 }}</h3><p>Packing</p></div>
                </div>
                <div class="status-card">
                    <div class="status-icon ic-siap"><i class='bx bx-check-circle'></i></div>
                    <div class="status-info"><h3>{{ $counts['siap'] ?? 0 }}</h3><p>Siap Ambil</p></div>
                </div>
                <div class="status-card">
                    <div class="status-icon ic-selesai"><i class='bx bx-check-double'></i></div>
                    <div class="status-info"><h3>{{ $counts['selesai'] ?? 0 }}</h3><p>Selesai</p></div>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Update Status (Real-time)</h3>
                        
                        <div class="table-search-wrapper">
                            <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Invoice/Nama...">
                            <i class='bx bx-search bx-search-toggle' id="tableSearchIcon"></i>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
                        <input type="date" id="filterDate" class="filter-input" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 6px;">
                        <div class="filter-status-group" id="statusFilterContainer">
                            <button class="btn-filter-status active" data-filter="all">Semua</button>
                            <button class="btn-filter-status" data-filter="diterima">Diterima</button>
                            <button class="btn-filter-status" data-filter="dicuci">Dicuci</button>
                            <button class="btn-filter-status" data-filter="dikeringkan">Dikeringkan</button>
                            <button class="btn-filter-status" data-filter="disetrika">Disetrika</button>
                            <button class="btn-filter-status" data-filter="packing">Packing</button>
                            <button class="btn-filter-status" data-filter="siap">Siap Ambil</button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="table-status">
                            <thead>
                                <tr>
                                    <th>Kode Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Status Sekarang</th>
                                    <th>Aksi Selanjutnya</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @foreach($transaksi as $item)
                                    <tr class="row-item" data-status="{{ strtolower($item->status_pesanan) }}" data-date="{{ date('Y-m-d', strtotime($item->tgl_masuk)) }}">
                                        <td><strong>{{ $item->kode_invoice }}</strong></td>
                                        <td>{{ $item->pelanggan->nama }}</td>
                                        <td>{{ date('d M Y', strtotime($item->tgl_masuk)) }}</td>
                                        
                                        <td>
                                            @php
                                                $st = strtolower($item->status_pesanan);
                                                $classBadge = 'st-diterima';
                                                if($st == 'dicuci') $classBadge = 'st-dicuci';
                                                elseif($st == 'dikeringkan') $classBadge = 'st-dikeringkan';
                                                elseif($st == 'disetrika') $classBadge = 'st-disetrika';
                                                elseif($st == 'packing') $classBadge = 'st-packing';
                                                elseif($st == 'siap diambil') $classBadge = 'st-siap';
                                                elseif($st == 'selesai') $classBadge = 'st-selesai';
                                                elseif($st == 'dibatalkan') $classBadge = 'st-dibatalkan';
                                            @endphp
                                            <span class="badge-status {{ $classBadge }}">
                                                {{ strtoupper($item->status_pesanan) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{-- LOGIKA TOMBOL AKSI (TETAP AMAN) --}}
                                            @if($item->status_pesanan == 'dibatalkan')
                                                
                                                <span style="color: #C62828; font-size: 12px; font-weight: 600;">
                                                    <i class='bx bx-x-circle'></i> Order Dibatalkan
                                                </span>

                                            @elseif($item->status_pesanan == 'selesai')
                                                
                                                <span style="color: #2E7D32; font-size: 12px; font-weight: 600;">
                                                    <i class='bx bx-check-double'></i> Transaksi Selesai
                                                </span>

                                            @else
                                                
                                                <form action="{{ route('admin.transaksi.updateStatus', $item->id_transaksi) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    @php
                                                        $nextStatus = '';
                                                        $btnText = '';
                                                        $btnClass = 'btn-update';

                                                        if($st == 'diterima') { $nextStatus = 'dicuci'; $btnText = 'Mulai Cuci'; }
                                                        elseif($st == 'dicuci') { $nextStatus = 'dikeringkan'; $btnText = 'Mulai Keringkan'; }
                                                        elseif($st == 'dikeringkan') { $nextStatus = 'disetrika'; $btnText = 'Mulai Setrika'; }
                                                        elseif($st == 'disetrika') { $nextStatus = 'packing'; $btnText = 'Mulai Packing'; }
                                                        elseif($st == 'packing') { $nextStatus = 'siap diambil'; $btnText = 'Siap Diambil'; }
                                                        elseif($st == 'siap diambil') { $nextStatus = 'selesai'; $btnText = 'Ambil / Selesai'; $btnClass .= ' btn-selesai'; }
                                                    @endphp

                                                    @if($nextStatus)
                                                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                        <button type="submit" class="{{ $btnClass }}">
                                                            {{ $btnText }} <i class='bx bx-right-arrow-alt'></i>
                                                        </button>
                                                    @endif
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btns = document.querySelectorAll('.btn-filter-status');
            const rows = document.querySelectorAll('.row-item');
            const dateInput = document.getElementById('filterDate');
            
            // PERBAIKAN: Selector untuk search bar
            const searchInput = document.getElementById('tableSearchInput'); 

            function filterTable() {
                const activeBtn = document.querySelector('.btn-filter-status.active');
                const statusFilter = activeBtn.getAttribute('data-filter');
                const dateFilter = dateInput.value;
                const searchText = searchInput.value.toLowerCase(); // Ambil teks pencarian

                rows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    const date = row.getAttribute('data-date');
                    const rowText = row.innerText.toLowerCase(); // Ambil semua teks di baris itu

                    // Cek Status
                    let statusMatch = (statusFilter === 'all');
                    if (!statusMatch) {
                        if (statusFilter === 'siap' && status === 'siap diambil') statusMatch = true;
                        else if (status === statusFilter) statusMatch = true;
                    }

                    // Cek Tanggal
                    let dateMatch = true;
                    if (dateFilter && date !== dateFilter) dateMatch = false;

                    // Cek Pencarian Teks
                    let searchMatch = true;
                    if (searchText && !rowText.includes(searchText)) {
                        searchMatch = false;
                    }

                    // Gabungkan semua filter
                    if (statusMatch && dateMatch && searchMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Event Listeners
            btns.forEach(btn => {
                btn.addEventListener('click', function() {
                    btns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterTable();
                });
            });

            dateInput.addEventListener('change', filterTable);
            
            // Tambahkan event listener buat search bar biar real-time
            if(searchInput) {
                searchInput.addEventListener('keyup', filterTable);
            }
        });
    </script>
</body>
</html>