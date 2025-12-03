<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Status Order - Rizhaqi Laundry pegawai</title>

    <style>
        /* CSS TABEL FULL GRID (KOTAK-KOTAK) - SAMA DENGAN INDEX */
        .table-container table {
            width: 100%;
            border-collapse: collapse; /* KUNCI: Agar garis nyambung */
            border: 1px solid var(--border-light);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .table-container thead tr {
            background-color: var(--surface-white);
            border-bottom: 1px solid var(--border-light);
        }

        /* SEL KOTAK-KOTAK (Border di semua sisi) */
        .table-container th,
        .table-container td {
            padding: 15px;
            border: 1px solid var(--border-light); /* Garis Keliling */
            text-align: left;
            font-size: 14px;
            color: var(--text-primary);
        }

        .table-container th {
            font-weight: 600;
            color: var(--text-secondary);
            font-family: var(--google-sans);
            background-color: var(--surface-white);
        }

        .table-container tbody tr:hover {
            background-color: rgba(26, 115, 232, 0.04);
        }

        /* TOMBOL AKSI KHUSUS STATUS */
        .table-container .btn-action-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-status {
            padding: 8px 16px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-status:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Tombol Disabled (Mati) */
        .btn-status.disabled {
            background-color: #e0e0e0 !important;
            color: #999 !important;
            cursor: not-allowed;
            pointer-events: none;
            transform: none;
            box-shadow: none;
        }

        /* Warna Tombol Status */
        .btn-blue { background-color: var(--accent-blue); }
        .btn-orange { background-color: #FF9800; }
        .btn-purple { background-color: #9C27B0; }
        .btn-teal { background-color: #009688; } /* Warna Packing */
        .btn-green { background-color: var(--accent-green); }
        .btn-dark { background-color: #455A64; }
        .btn-red { background-color: var(--accent-red); }

        /* BADGES STATUS */
        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; white-space: nowrap; }
        .st-diterima { background: #E0E0E0; color: #424242; }
        .st-dicuci { background: #E3F2FD; color: #1565C0; }
        .st-dikeringkan { background: #FFF3E0; color: #EF6C00; }
        .st-disetrika { background: #F3E5F5; color: #7B1FA2; }
        .st-packing { background: #E0F2F1; color: #00695C; }
        .st-siap { background: #E8F5E9; color: #2E7D32; }
        .st-selesai { background: #1B5E20; color: #fff; }      /* Hijau Tua (Selesai) */
        .st-batal { background: #FFEBEE; color: #C62828; }     /* Merah (Batal) */


        /* SEARCH BAR */
        .table-data .order .head { position: relative; }
        .table-search-input {
            width: 0; padding: 0; border: none; transition: width 0.3s ease;
            background: var(--surface-white); border-radius: 20px; margin-left: auto; outline: none; height: 40px;
        }
        .table-search-input.show { width: 200px; padding: 8px 12px; border: 1px solid var(--border-light); }
        .table-data .order .head .bx-search { margin-left: 10px; cursor: pointer; }
    </style>
</head>
<body>

    @include('layout.sidebar')

    <section id="content">
        @include('partial.navbar')
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Status Order</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('pegawai.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('pegawai.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Status Order</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Update Status Cucian (Real-time)</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Kode Invoice...">
                        <i class='bx bx-search' id="tableSearchIcon"></i>
                        <i class='bx bx-filter'></i>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th>Kode Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Status Sekarang</th>
                                    <th>Aksi Selanjutnya</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- SKENARIO 1: DITERIMA -> MULAI CUCI --}}
                                <tr>
                                    <td><strong>D0226</strong></td>
                                    <td>Ibu Ratna</td>
                                    <td><span class="status-badge st-diterima">Diterima</span></td>
                                    <td>
                                        <button type="button" class="btn-status btn-blue" onclick="return confirm('Mulai cuci pesanan ini?')">
                                            <i class='bx bx-water'></i> Mulai Cuci
                                        </button>
                                    </td>
                                </tr>

                                {{-- SKENARIO 2: DICUCI -> KERINGKAN --}}
                                <tr>
                                    <td><strong>D0225</strong></td>
                                    <td>Kak Dinda</td>
                                    <td><span class="status-badge st-dicuci">Dicuci</span></td>
                                    <td>
                                        <button type="button" class="btn-status btn-orange" onclick="return confirm('Selesai cuci? Lanjut keringkan?')">
                                            <i class='bx bx-wind'></i> Ke Pengeringan
                                        </button>
                                    </td>
                                </tr>

                                {{-- SKENARIO 3: DIKERINGKAN -> SETRIKA --}}
                                <tr>
                                    <td><strong>D0224</strong></td>
                                    <td>Bu Siti</td>
                                    <td><span class="status-badge st-dikeringkan">Dikeringkan</span></td>
                                    <td>
                                        <button type="button" class="btn-status btn-purple" onclick="return confirm('Sudah kering? Lanjut setrika?')">
                                            <!-- Icon Baju (Pengganti Icon Setrika) -->
                                            <i class='bx bxs-t-shirt'></i> Mulai Setrika
                                        </button>
                                    </td>
                                </tr>

                                {{-- SKENARIO 4: DISETRIKA -> PACKING (NEW STATUS) --}}
                                <tr>
                                    <td><strong>D0223</strong></td>
                                    <td>Pak Rahmat</td>
                                    <td><span class="status-badge st-disetrika">Disetrika</span></td>
                                    <td>
                                        <button type="button" class="btn-status btn-teal" onclick="return confirm('Selesai setrika? Lanjut packing?')">
                                            <i class='bx bx-box'></i> Mulai Packing
                                        </button>
                                    </td>
                                </tr>

                                {{-- SKENARIO 5: PACKING -> SIAP (NEW STATUS) --}}
                                <tr>
                                    <td><strong>D0222</strong></td>
                                    <td>Mbak Rini</td>
                                    <td><span class="status-badge st-packing">Packing</span></td>
                                    <td>
                                        <button type="button" class="btn-status btn-green" onclick="return confirm('Selesai packing? Tandai siap diambil?')">
                                            <i class='bx bx-check-circle'></i> Tandai Siap
                                        </button>
                                    </td>
                                </tr>

                                {{-- SKENARIO 6: SIAP -> DIAMBIL --}}
                                <tr>
                                    <td><strong>D0221</strong></td>
                                    <td>Mas Tono</td>
                                    <td><span class="status-badge st-siap">Siap Diambil</span></td>
                                    <td>
                                        <button type="button" class="btn-status btn-dark" onclick="return confirm('Barang sudah diambil pelanggan?')">
                                            <i class='bx bx-package'></i> Konfirmasi Ambil
                                        </button>
                                    </td>
                                </tr>

                                {{-- SKENARIO 7: SUDAH DIAMBIL (FINAL) -> TOMBOL MATI --}}
                                <tr>
                                    <td><strong>D0220</strong></td>
                                    <td>Pak Yusuf</td>
                                    <td><span class="status-badge st-selesai">Sudah Diambil</span></td>
                                    <td>
                                        {{-- Tombol Mati (Disabled) --}}
                                        <button type="button" class="btn-status disabled" title="Transaksi Selesai">
                                            <i class='bx bx-check-double'></i> Selesai
                                        </button>
                                    </td>
                                </tr>

                                {{-- SKENARIO 8: DIBATALKAN (FINAL) -> TOMBOL MATI --}}
                                <tr>
                                    <td><strong>D0219</strong></td>
                                    <td>Ibu Susi</td>
                                    <td><span class="status-badge st-batal">Dibatalkan</span></td>
                                    <td>
                                        {{-- Tombol Mati (Disabled) --}}
                                        <button type="button" class="btn-status disabled" title="Transaksi Dibatalkan">
                                            <i class='bx bx-x-circle'></i> Batal
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </section>

    <div id="pagination" class="pagination-container"></div>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/pagination.js') }}"></script>
    <script src="{{ asset('admin/script/chart.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>
