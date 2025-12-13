<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />

    <title>Laporan Harian Pegawai</title>

    <style>
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        th, td {
            padding: 14px;
            border: 1px solid var(--border-light);
            font-size: 14px;
        }

        th {
            background: #f9f9f9;
            font-weight: 600;
        }

        tbody tr:hover {
            background: rgba(26,115,232,0.05);
        }

        /* HEADER */
        .head {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .head h3 {
            margin-right: auto;
        }

        /* SEARCH */
        .search-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .search-input {
            width: 0;
            padding: 0;
            border: none;
            border-radius: 20px;
            transition: 0.3s;
            height: 38px;
        }

        .search-input.show {
            width: 200px;
            padding: 8px 12px;
            border: 1px solid #ccc;
        }

        .bx-search {
            cursor: pointer;
            font-size: 22px;
        }

        /* DATE */
        .filter-date {
            padding: 8px 12px;
            border-radius: 20px;
            border: 1px solid #ccc;
            font-size: 13px;
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
                            <h1>Laporan Harian Pegawai</h1>
                            <ul class="breadcrumb">
                                <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                                <li><i class='bx bx-chevron-right' ></i></li>
                                <li><a class="active" href="{{ route('pegawai.laporan') }}">Laporan Harian Pegawai</a></li>
                            </ul>
                        </div>

        <div class="table-data">
        <div class="order">

            <div class="head">
                <h3>Laporan Harian Pegawai</h3>

                <div class="search-wrapper">
                    <input type="text" id="searchInput" class="search-input" placeholder="Cari nama pegawai...">
                    <i class='bx bx-search' id="searchBtn"></i>
                </div>

                <input type="date" id="dateFilter" class="filter-date">
            </div>

            <div class="table-container">
            {{-- TABLE INI AKAN SELALU ADA (HANYA UNTUK JUDUL KOLOM) --}}
            <table>
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Tanggal Dikerjakan</th>
                        <th>Total Berat (Kg)</th>
                    </tr>
                </thead>

                @if ($laporan->isNotEmpty())
                    {{-- HANYA TAMPILKAN TBODY JIKA ADA DATA --}}
                    <tbody>
                        @foreach ($laporan as $item)
                            <tr data-date="{{ $item->tgl_dikerjakan }}">
                                <td>{{ $item->nama_pegawai }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tgl_dikerjakan)->isoFormat('DD MMM YYYY') }}</td>
                                <td>{{ number_format($item->total_berat, 2) }} Kg</td>
                            </tr>
                        @endforeach
                    </tbody>
                @endif
            </table>

            {{-- PESAN KOSONG DI LUAR TABEL (JIKA KOSONG) --}}
            @if ($laporan->isEmpty())
                <div id="emptyDatabase" style="
                    text-align: center;
                    padding: 80px 0; /* Padding agar di tengah vertikal */
                    width: 100%;
                    /* Penting: Pastikan tidak ada border atau shadow yang diwarisi dari class lain */
                ">
                    <i class='bx bx-folder-open' style="font-size: 80px; color: #bdbdbd;"></i>
                    <h3 style="margin-top: 10px; font-size: 20px; color: #333; font-weight: 600;">
                        Belum ada data laporan harian.
                    </h3>
                </div>
            @endif
        </div>

        </div>
        </div>

        </main>
        </section>

        <script>
        document.addEventListener('DOMContentLoaded', () => {

            const searchBtn   = document.getElementById('searchBtn');
            const searchInput = document.getElementById('searchInput');
            const dateFilter  = document.getElementById('dateFilter');
            const rows        = document.querySelectorAll('tbody tr');

            // toggle search
            searchBtn.onclick = () => {
                searchInput.classList.toggle('show');
                searchInput.focus();
            };

            // search nama
            searchInput.addEventListener('input', () => {
                const keyword = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(keyword)
                        ? ''
                        : 'none';
                });
            });

            // filter tanggal
            dateFilter.addEventListener('input', () => {
                const date = dateFilter.value;

                rows.forEach(row => {
                    if (!date) {
                        row.style.display = '';
                        return;
                    }
                    row.style.display = row.dataset.date === date ? '' : 'none';
                });
            });

        });
        </script>

        <script src="{{ asset('admin/script/script.js') }}"></script>
        <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>
