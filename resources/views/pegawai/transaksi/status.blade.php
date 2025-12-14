<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />

    <title>Status Order - Rizhaqi Laundry Pegawai</title>

    <style>
        /* CSS TABEL GRID */
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .table-container thead tr {
            background-color: var(--surface-white);
            border-bottom: 1px solid var(--border-light);
        }

        .table-container th,
        .table-container td {
            padding: 15px;
            border: 1px solid var(--border-light);
            text-align: left;
            font-size: 14px;
            color: var(--text-primary);
        }

        .table-container th {
            font-weight: 600;
            color: var(--text-secondary);
            background-color: #f9f9f9;
        }

        .table-container tbody tr:hover {
            background-color: rgba(26, 115, 232, 0.04);
        }

        /* --- 1. DESIGN TOMBOL (STATE: BELUM SELESAI) --- */
        .btn-status {
            padding: 8px 16px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: 0.2s;
            background-color: #9C27B0;
            /* Ungu Setrika */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-status:hover {
            background-color: #7B1FA2;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        /* --- 2. DESIGN SELESAI (STATE: SUDAH SELESAI) --- */
        /* Ini dibuat FLAT, tanpa border tombol, tanpa background tombol */
        .status-done {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #27ae60;
            /* Hijau Sukses */
            font-weight: 700;
            /* Tebal biar jelas */
            font-size: 14px;
            background: transparent;
            /* Hilangkan background tombol */
            cursor: default;
            /* Cursor panah biasa (ga bisa diklik) */
            user-select: none;
        }

        .status-done i {
            font-size: 20px;
            /* Ikon centang agak besar */
        }

        /* BADGES STATUS DI KOLOM STATUS SEKARANG */
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .st-disetrika {
            background: #F3E5F5;
            color: #7B1FA2;
            border: 1px solid #E1BEE7;
        }

        .st-packing {
            background: #E0F2F1;
            color: #00695C;
            border: 1px solid #B2DFDB;
        }

        /* UI FILTER */
        .table-data .order .head {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .filter-row {
            display: flex;
            gap: 10px;
            width: 100%;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-right: auto;
        }

        .status-pills-container {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 6px 14px;
            border: 1px solid var(--border-light);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            background: #fff;
            color: var(--text-secondary);
        }

        .filter-pill.active {
            background: var(--accent-blue);
            color: white;
            border-color: var(--accent-blue);
        }

        /* SEARCH BAR */
        .table-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .table-search-input {
            width: 200px;
            padding: 6px 12px;
            border: 1px solid var(--border-light);
            border-radius: 20px;
            outline: none;
        }

        /* STATUS SUMMARY CARDS */
        .status-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
            list-style: none;
            padding: 0;
        }

        .status-summary li {
            background: #fff;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-summary li .bx {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-summary li .info h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            line-height: 1;
        }

        .status-summary li .info p {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
            font-weight: 500;
        }

        .bg-setrika {
            background: #F3E5F5;
            color: #7B1FA2;
        }

        .bg-packing {
            background: #E0F2F1;
            color: #00796B;
        }
    </style>
</head>

<body>

    @include('layout.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Tugas Setrika</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Antrian Setrika</a></li>
                    </ul>
                </div>
            </div>

            @php
                // Hitung total dari data yang sudah difilter
                // Gunakan $transaksi->where()->count() karena $counts dari Controller kosong
                $countDisetrika = $transaksi->where('status_pesanan', 'disetrika')->count();
                $countPacking = $transaksi->where('status_pesanan', 'packing')->count();
            @endphp

            <ul class="status-summary">
                <li>
                    <i class='bx bxs-t-shirt bg-setrika'></i>
                    <div class="info">
                        <h3>{{ $countDisetrika }}</h3> {{-- Menggunakan hitungan langsung dari $transaksi --}}
                        <p>Antrian Setrika</p>
                    </div>
                </li>
                <li>
                    <i class='bx bx-package bg-packing'></i>
                    <div class="info">
                        <h3>{{ $countPacking }}</h3> {{-- Menggunakan hitungan langsung dari $transaksi --}}
                        <p>Siap Packing</p>
                    </div>
                </li>
            </ul>

            <div class="table-data">
                <div class="order">

                    <div class="head">
                        <div class="filter-row">
                            <h3 class="filter-title">Antrian Setrika & Packing</h3>
                        </div>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th>Kode Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Berat (Kg)</th>
                                    <th>Status Sekarang</th>
                                    <th style="width: 200px;">Aksi / Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksi as $item)
                                    <tr>
                                        <td><strong>{{ $item->kode_invoice }}</strong></td>
                                        <td>{{ optional($item->pelanggan)->nama ?? 'Umum' }}</td>
                                        <td>{{ date('d M Y', strtotime($item->tgl_masuk)) }}</td>
                                        <td>{{ number_format($item->berat, 2) }} Kg</td>
                                        <td>
                                            @if($item->status_pesanan == 'disetrika')
                                                <span class="status-badge st-disetrika">Disetrika</span>
                                            @elseif($item->status_pesanan == 'packing')
                                                <span class="status-badge st-packing">Packing</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- LOGIKA TOMBOL AKSI PEGAWAI --}}
                                            @if($item->status_pesanan == 'disetrika')
                                                {{-- ACTION BENAR HARUSNYA: route('pegawai.transaksi.update') --}}
                                                <form action="{{ route('pegawai.transaksi.update', $item->id_transaksi) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn-status"
                                                        onclick="return confirm('Selesaikan tugas ini? Status akan diubah ke PACKING.')">
                                                        <i class='bx bxs-iron'></i> Selesai Setrika
                                                    </button>
                                                </form>
                                            @elseif($item->status_pesanan == 'packing')
                                                <div class="status-done">
                                                    <i class='bx bx-check-double'></i>
                                                    <span>Siap Diserahkan</span>
                                                </div>
                                            @else
                                                {{-- Jika status lain (misal batal, selesai, dicuci) --}}
                                                <span style="color: #888;">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada order yang
                                            sedang disetrika atau packing saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </section>


    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ELEMENT FILTER
            const pills = document.querySelectorAll('.filter-pill');
            const rows = document.querySelectorAll('.row-item');
            const dateInput = document.getElementById('dateFilter');
            const searchInput = document.getElementById('tableSearchInput');
            const searchIcon = document.getElementById('tableSearchIcon');

            // 1. FILTER FUNCTION
            function filterTable() {
                const activePill = document.querySelector('.filter-pill.active');
                const statusFilter = activePill ? activePill.getAttribute('data-status') : 'all';
                const dateFilter = dateInput.value;
                const searchText = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    const date = row.getAttribute('data-date');
                    const rowText = row.innerText.toLowerCase();

                    // Logic Status
                    let statusMatch = (statusFilter === 'all');
                    if (!statusMatch && status === statusFilter) {
                        statusMatch = true;
                    }

                    // Logic Date
                    let dateMatch = true;
                    if (dateFilter && date !== dateFilter) {
                        dateMatch = false;
                    }

                    // Logic Search
                    let searchMatch = true;
                    if (searchText && !rowText.includes(searchText)) {
                        searchMatch = false;
                    }

                    // Final Decision
                    if (statusMatch && dateMatch && searchMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // 2. EVENT LISTENER PILLS
            pills.forEach(pill => {
                pill.addEventListener('click', function () {
                    // Reset active class
                    pills.forEach(p => p.classList.remove('active'));
                    // Set active current
                    this.classList.add('active');
                    filterTable();
                });
            });

            // 3. EVENT LISTENER DATE & SEARCH
            dateInput.addEventListener('change', filterTable);
            searchInput.addEventListener('keyup', filterTable);

            // 4. ANIMASI SEARCH BAR
            searchIcon.addEventListener('click', function () {
                searchInput.classList.toggle('show');
                if (searchInput.classList.contains('show')) {
                    searchInput.focus();
                }
            });
        });
    </script>

</body>

</html>