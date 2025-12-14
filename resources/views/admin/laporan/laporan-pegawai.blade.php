<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Laporan Harian Pegawai - Rizhaqi Laundry Admin</title>
    
    <style>
        /* Menggunakan style yang sudah ada */
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container thead tr { background-color: var(--surface-white); border-bottom: 1px solid var(--border-light); }
        .table-container th, .table-container td { padding: 15px; border: 1px solid var(--border-light); text-align: left; font-size: 14px; color: var(--text-primary); }
        .table-container th { font-weight: 600; color: var(--text-secondary); font-family: var(--google-sans); background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }

        /* Style Filter */
        .filter-section {
            display: flex; align-items: center; gap: 15px; margin-bottom: 20px;
            background: #fff; padding: 15px; border-radius: 10px; border: 1px solid var(--border-light);
        }
        .filter-label { font-weight: 600; color: var(--text-secondary); font-size: 14px; }
        .filter-input { padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 6px; font-size: 14px; outline: none; color: var(--text-primary); }

        .total-weight-badge {
            background-color: #e3f2fd; color: #1565c0; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 13px;
        }

        /* Style Empty State di Tengah */
        .empty-state-cell {
            text-align: center !important; 
            vertical-align: middle !important;
            padding: 60px 20px !important;
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
                    <h1>Laporan Harian Pegawai</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.laporan.index') }}">Laporan</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="#">Laporan Pegawai</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    
                    {{-- FILTER TANGGAL --}}
                    <form action="{{ route('admin.laporan.pegawai') }}" method="GET">
                        <div class="head" style="flex-direction: column; align-items: flex-start; gap: 10px;">
                            <h3>Filter Laporan</h3>
                            <div class="filter-section" style="width: 100%; margin-bottom: 0;">
                                <label class="filter-label">Pilih Tanggal:</label>
                                <input type="date" name="date" class="filter-input" 
                                       value="{{ $tanggal }}" 
                                       onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>

                    <div class="head" style="margin-top: 20px;">
                        <h3>Kinerja Pegawai Tanggal: {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM Y') }}</h3>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th width="5%">No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Tanggal Dikerjakan</th>
                                    <th>Total Berat (Kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div style="font-weight: 600;">{{ $item->nama_pegawai }}</div>
                                            <div style="font-size: 12px; color: #888;">{{ $item->email_pegawai }}</div>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}
                                        </td>
                                        <td>
                                            <span class="total-weight-badge">
                                                {{ number_format($item->total_berat, 2) }} Kg
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- {{-- FIX: HAPUS INCLUDE, PAKAI HTML MANUAL BIAR GAK ERROR --}}
                                    <tr class="no-data-row">
                                        <td colspan="4" class="empty-state-cell">
                                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                                <i class='bx bx-search-alt' style="font-size: 64px; color: #cbd5e1; margin-bottom: 16px;"></i>
                                                <h4 style="font-size: 18px; font-weight: 600; color: #475569; margin: 0;">
                                                    Belum ada kinerja pegawai pada tanggal ini.
                                                </h4>
                                            </div>
                                        </td>
                                    </tr> -->
                                @endforelse
                            </tbody>
                            
                            {{-- FOOTER TOTAL --}}
                            @if($laporan->isNotEmpty())
                            <tfoot>
                                <tr style="background-color: white; border: none;">
                                    <td colspan="4" style="padding: 10px; border: none;"></td>
                                </tr>
                                
                                <tr style="background-color: #f8f9fa;">
                                    {{-- Kolom 1 & 2 Kosong --}}
                                    <td colspan="2" style="border: none;"></td>
                                    
                                    {{-- Kolom 3: Label TOTAL (Rata Kanan) --}}
                                    <td style="text-align: right; padding-right: 20px; font-weight: bold; border: 1px solid var(--border-light); vertical-align: middle;">
                                        TOTAL KESELURUHAN:
                                    </td>
                                    
                                    {{-- Kolom 4: Nilai Total (DI BAWAH KOLOM BERAT) --}}
                                    <td style="color: #2e7d32; font-size: 16px; font-weight: bold; border: 1px solid var(--border-light); vertical-align: middle;">
                                        {{ number_format($laporan->sum('total_berat'), 2) }} Kg
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
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