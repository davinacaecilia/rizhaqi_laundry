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

        .summary-box {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-light);
            margin-bottom: 20px;
        }

        .summary-box p {
            font-size: 16px;
            margin: 0;
            font-weight: 500;
        }

        .summary-box strong {
            font-size: 20px;
            color: var(--text-primary);
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
        </div>

        <div class="table-data">
        <div class="order">

            <div class="head">
                <h3>Detail Pekerjaan</h3>

                {{-- FORM FILTER TANGGAL --}}
                <form action="{{ route('pegawai.laporan') }}" method="GET" style="margin: 0;">
                    <input type="date" id="dateFilter" name="date"
                        class="filter-date"
                        {{-- Gunakan null coalescing operator (??) untuk memastikan value kosong jika tidak ada filter --}}
                        value="{{ $dateFilter ?? '' }}"
                        onchange="this.form.submit()">
                </form>
            </div>

            <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode Invoice</th>
                        <th>Nama Pelanggan</th>
                        <th>Tanggal Dikerjakan</th>
                        <th>Berat Dikerjakan (Kg)</th>
                    </tr>
                </thead>

                @if ($laporan->isNotEmpty())
                    <tbody>
                    @foreach ($laporan as $item)
                        <tr data-search="{{ strtolower($item->kode_invoice) }} {{ strtolower($item->nama_pelanggan) ?? 'umum' }}">

                            <td>
                                <strong>{{ $item->kode_invoice }}</strong>
                            </td>

                            <td>
                                {{ $item->nama_pelanggan ?? 'Umum' }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($item->tgl_dikerjakan)->isoFormat('DD MMM YYYY') }}
                            </td>

                            <td>
                                {{ number_format($item->berat, 2) }} Kg
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            {{-- 1. SEL KOSONG (Kolom Kode Invoice) --}}
                            <td style="
                                background-color: #ffffffff;
                                font-weight: 700;
                                color: var(--accent-blue);
                            ">

                            {{-- 2. SEL KOSONG (Kolom Nama Pelanggan) --}}
                            <td style="
                                background-color: #ffffffff;
                                font-weight: 700;
                                color: var(--accent-blue);
                            ">
                            </td>

                            {{-- 3. TEKS TOTAL (Di bawah Kolom Tanggal Dikerjakan) --}}
                            <td style="
                                text-align: left;
                                font-weight: 700;
                                background-color: #f2f7ff;
                                color: var(--accent-blue);
                            ">
                                TOTAL
                            </td>

                            {{-- 4. NILAI TOTAL (Di bawah Kolom Berat Dikerjakan) --}}
                            <td style="
                                text-align: left;
                                font-weight: 700;
                                background-color: #f2f7ff;
                                color: var(--accent-blue);
                            ">
                                {{ number_format($totalBeratDikerjakan, 2) }} Kg
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>

            {{-- PESAN KOSONG DI LUAR TABEL (JIKA KOSONG) --}}
            @if ($laporan->isEmpty())
                <div id="emptyDatabase" style="
                    text-align: center;
                    padding: 80px 0; /* Padding agar di tengah vertikal */
                    width: 100%;
                ">
                    <i class='bx bx-folder-open' style="font-size: 80px; color: #bdbdbd;"></i>
                    <h3 style="margin-top: 10px; font-size: 20px; color: #333; font-weight: 600;">
                        Belum ada pekerjaan yang Anda selesaikan di tanggal {{ \Carbon\Carbon::parse($dateFilter)->isoFormat('D MMMM YYYY') }}.
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

            const searchBtn   = document.getElementById('searchBtn');
            const searchInput = document.getElementById('searchInput');
            // Hapus const dateFilter dan logic filter tanggal JS karena sekarang menggunakan filter PHP/Laravel
            const rows        = document.querySelectorAll('tbody tr');

            // toggle search
            searchBtn.onclick = () => {
                searchInput.classList.toggle('show');
                if (searchInput.classList.contains('show')) {
                    searchInput.focus();
                }
            };

            // search nama
            searchInput.addEventListener('input', () => {
                const keyword = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    // Cek data-search yang berisi nama dan kode invoice
                    const rowSearchText = row.getAttribute('data-search');

                    row.style.display = rowSearchText.includes(keyword)
                         ? ''
                         : 'none';
                });
            });

            // LOGIC FILTER TANGGAL JS DIHAPUS karena sudah ditangani oleh Laravel (onchange="this.form.submit()")

        });
        </script>

        <script src="{{ asset('admin/script/script.js') }}"></script>
        <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>
