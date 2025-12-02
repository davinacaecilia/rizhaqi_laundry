<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Data Pengeluaran - Rizhaqi Laundry</title>
    
    <style>
        /* --- GAYA TABEL KONSISTEN --- */
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container thead tr { background-color: var(--surface-white); border-bottom: 1px solid var(--border-light); }
        
        .table-container th, .table-container td { 
            padding: 15px; 
            border: 1px solid var(--border-light); 
            text-align: left; font-size: 14px; color: var(--text-primary); 
        }
        
        .table-container th { font-weight: 600; color: var(--text-secondary); background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }

        /* BUTTONS */
        .btn-detail { padding: 6px 12px; font-size: 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; transition: 0.2s; border: none; cursor: pointer; font-weight: 500; }
        .btn-edit { background-color: var(--accent-blue); color: white; }
        .btn-delete { background-color: var(--accent-red); color: white; }
        .btn-detail:hover { opacity: 0.9; }

        .head-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        
        /* HEADER FILTER AREA */
        .table-data .order .head { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
            margin-bottom: 20px; 
            flex-wrap: wrap; /* Agar responsif di HP */
        }

        /* --- STYLING KHUSUS TOTAL DI KANAN --- */
        .total-badge-wrapper {
            margin-left: auto; /* INI KUNCINYA: Mendorong elemen ke paling kanan */
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #FFEBEE; /* Latar merah muda lembut */
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #FFCDD2;
        }

        .total-label {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
        }

        .total-amount {
            font-family: monospace;
            font-size: 18px;
            font-weight: 700;
            color: #C62828; /* Merah tebal */
        }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Manajemen Pengeluaran</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Pengeluaran</a></li>
                    </ul>
                </div>
                
                <a href="{{ route('admin.pengeluaran.create') }}" class="btn-download">
                    <i class='bx bx-plus'></i> Catat Pengeluaran
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Riwayat</h3>
                        
                        <input type="date" style="padding: 8px; border-radius: 8px; border: 1px solid #ddd;" value="{{ date('Y-m-d') }}">
                        <button class="btn-detail" style="background: #eee; color: #333;">Filter</button>

                        <div class="total-badge-wrapper">
                            <span class="total-label">Total Hari Ini:</span>
                            <span class="total-amount">Rp 395.000</span>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan Pengeluaran</th>
                                    <th>Dicatat Oleh</th>
                                    <th>Jumlah (Rp)</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DUMMY 1 --}}
                                <tr>
                                    <td><strong>OUT-001</strong></td>
                                    <td>29 Nov 2025</td>
                                    <td>Beli Deterjen Cair (5 Liter) & Pewangi</td>
                                    <td>Budi Santoso</td>
                                    <td style="color: #C62828; font-weight: bold;">Rp 150.000</td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <a href="#" class="btn-detail btn-edit"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail btn-delete"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- DUMMY 2 --}}
                                <tr>
                                    <td><strong>OUT-002</strong></td>
                                    <td>29 Nov 2025</td>
                                    <td>Isi Ulang Token Listrik</td>
                                    <td>Kak Elvira</td>
                                    <td style="color: #C62828; font-weight: bold;">Rp 200.000</td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <a href="#" class="btn-detail btn-edit"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail btn-delete"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- DUMMY 3 --}}
                                <tr>
                                    <td><strong>OUT-003</strong></td>
                                    <td>29 Nov 2025</td>
                                    <td>Beli Makan Siang Pegawai (3 Org)</td>
                                    <td>Ani Wijaya</td>
                                    <td style="color: #C62828; font-weight: bold;">Rp 45.000</td>
                                    <td>
                                        <div style="display: flex; gap: 5px;">
                                            <a href="#" class="btn-detail btn-edit"><i class='bx bx-edit'></i></a>
                                            <a href="#" class="btn-detail btn-delete"><i class='bx bx-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                                
                                {{-- Baris Total di bawah tabel DIHAPUS karena sudah pindah ke atas kanan --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>
</html>