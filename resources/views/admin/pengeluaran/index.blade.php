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
        /* --- CSS SAMA SEPERTI SEBELUMNYA --- */
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
            background-color: var(--surface-white);
        }

        .table-container tbody tr:hover {
            background-color: rgba(26, 115, 232, 0.04);
        }

        .btn-detail {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: 0.2s;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-edit {
            background-color: var(--accent-blue);
            color: white;
        }

        .btn-delete {
            background-color: var(--accent-red);
            color: white;
        }

        .btn-detail:hover {
            opacity: 0.9;
        }

        .head-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        /* HEADER FILTER AREA */
        /* Flexbox Space-Between: Judul di Kiri, Grup Filter di Kanan */
        .table-data .order .head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* KUNCI: Dorong konten ke ujung-ujung */
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Container untuk grup elemen kanan (Search, Date, Total) */
        .head-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Total Badge */
        .total-badge-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #FFEBEE;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid #FFCDD2;
            white-space: nowrap;
            /* Biar gak turun baris */
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
            color: #C62828;
        }

        /* SEARCH BAR ANIMASI */
        .table-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .table-search-input {
            width: 0;
            padding: 0;
            border: none;
            margin-left: 0;
            background: transparent;
            transition: width 0.3s ease, padding 0.3s ease;
            opacity: 0;
            pointer-events: none;
            height: 40px;
            border-radius: 20px;
        }

        .table-search-input.show {
            width: 200px;
            padding: 6px 12px;
            border: 1px solid var(--border-light);
            margin-right: 10px;
            opacity: 1;
            pointer-events: auto;
            background: var(--surface-white);
        }

        .table-search-input:focus {
            border-color: var(--accent-blue);
            outline: none;
        }

        .bx-search {
            cursor: pointer;
            font-size: 20px;
            color: #888;
            padding: 5px;
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
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Pengeluaran</a></li>
                    </ul>
                </div>

                <a href="{{ route('admin.pengeluaran.create') }}" class="btn-download">
                    <i class='bx bx-plus'></i> Catat Pengeluaran
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <!-- HEADER / FILTER AREA -->
                    <div class="head">
                        <!-- KIRI: Judul -->
                        <h3>Riwayat</h3>

                        <!-- KANAN: Grup Kontrol (Search, Date, Total) -->
                        <div class="head-controls">
                            <!-- 1. SEARCH BAR (Paling Kiri di grup kanan) -->
                            <div class="table-search-wrapper">
                                <input type="text" id="tableSearchInput" class="table-search-input"
                                    placeholder="Cari keterangan...">
                                <i class='bx bx-search' id="tableSearchIcon"></i>
                            </div>

                            <!-- 2. DATE FILTER -->
                            <input type="date" id="dateFilter"
                                style="padding: 6px 12px; border-radius: 20px; border: 1px solid #ddd; font-family: inherit; color: #555; outline: none; cursor: pointer;">

                            <!-- 3. TOTAL BADGE (Paling Kanan Mentok) -->
                            <div class="total-badge-wrapper">
                                <span class="total-label">Total:</span>
                                <span class="total-amount">Rp
                                    {{ number_format($total_pengeluaran, 0, ',', '.') }}</span>
                            </div>
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
                                @foreach ($pengeluaran as $out)
                                    <tr>
                                        <td><strong>OUT-{{ str_pad($out->id_pengeluaran, 3, '0', STR_PAD_LEFT) }}</strong>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($out->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $out->keterangan }}</td>
                                        {{-- Mengakses nama user melalui relasi Model --}}
                                        <td>{{ $out->user->nama ?? 'N/A' }}</td>
                                        <td style="color: #C62828; font-weight: bold;">Rp
                                            {{ number_format($out->jumlah, 0, ',', '.') }}</td>
                                        <td>
                                            <div style="display: flex; gap: 5px;">
                                                {{-- TOMBOL EDIT --}}
                                                <a href="{{ route('admin.pengeluaran.edit', $out->id_pengeluaran) }}"
                                                    class="btn-detail btn-edit" title="Edit Data"><i
                                                        class='bx bx-edit'></i></a>

                                                {{-- TOMBOL DELETE --}}
                                                <form
                                                    action="{{ route('admin.pengeluaran.destroy', $out->id_pengeluaran) }}"
                                                    method="POST" style="display:inline;"
                                                    onsubmit="return confirm('Yakin ingin menghapus pengeluaran ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-detail btn-delete" title="Hapus"><i
                                                            class='bx bx-trash'></i></button>
                                                </form>
                                            </div>
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

    <div id="pagination" class="pagination-container"></div>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/pagination.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
</body>

</html>