<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/dashboard.css') }}" /> 

    <title>Laporan Kinerja Harian - Owner</title>

    <style>
        /* --- STYLE KHUSUS --- */
        .filter-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            align-items: flex-end; 
            gap: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            flex-grow: 1; /* Input tanggal memenuhi ruang */
            max-width: 300px;
        }

        .filter-group label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
        }

        .filter-group input {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            width: 100%;
        }

        .btn-filter {
            background: #3C91E6;
            color: white;
            padding: 11px 20px; /* Tinggi disesuaikan dgn input */
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.3s;
        }

        .btn-filter:hover { background: #2980b9; }

        /* --- SUMMARY BOX --- */
        .summary-container { display: flex; gap: 20px; margin-bottom: 24px; }
        .summary-box {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 5px solid #3C91E6;
        }
        .summary-box div h3 { font-size: 24px; margin: 0; color: #333; }
        .summary-box div p { margin: 0; font-size: 13px; color: #888; }
        .summary-box i { font-size: 36px; color: #3C91E6; opacity: 0.2; }

        /* --- TABLE STYLE --- */
        .table-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            overflow: hidden; 
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px 20px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
        th { background: #f8f9fa; font-weight: 600; color: #555; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        tbody tr:hover { background-color: #fcfcfc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 600; }
        .badge-count { background: #E3F2FD; color: #1565C0; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .text-success { color: #2E7D32; font-size: 15px; }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Kinerja Pegawai (Harian)</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Owner</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Laporan Harian</a></li>
                    </ul>
                </div>
            </div>

            <form action="{{ route('admin.laporan.kinerja') }}" method="GET" class="filter-card">
                <div class="filter-group">
                    <label>Pilih Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" required onchange="this.form.submit()">
                </div>
            </form>

            <div class="summary-container">
                <div class="summary-box">
                    <div>
                        <p>Total Berat ({{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }})</p>
                        <h3>{{ number_format($grandTotalBerat, 0, ',', '.') }} Kg</h3>
                    </div>
                    <i class='bx bxs-t-shirt'></i>
                </div>
                <div class="summary-box" style="border-left-color: #F59E0B;">
                    <div>
                        <p>Total Transaksi Selesai</p>
                        <h3>{{ number_format($grandTotalTugas, 0, ',', '.') }}</h3>
                    </div>
                    <i class='bx bx-check-circle' style="color: #F59E0B;"></i>
                </div>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="45%">Nama Pegawai</th>
                            <th width="25%" class="text-center">Tugas Selesai</th>
                            <th width="25%" class="text-right">Total Berat (Kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanKinerja as $index => $pegawai)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="font-bold" style="color: #333;">
                                    {{ $pegawai->nama_pegawai }}
                                </td>
                                <td class="text-center">
                                    <span class="badge-count">
                                        {{ $pegawai->kinerja_count }} Order
                                    </span>
                                </td>
                                <td class="text-right font-bold text-success">
                                    {{ number_format($pegawai->kinerja_berat, 2, ',', '.') }} Kg
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center" style="padding: 40px; color: #888;">
                                    <i class='bx bx-calendar-x' style="font-size: 48px; margin-bottom: 10px; opacity: 0.5;"></i>
                                    <p>Tidak ada pegawai yang bekerja/menyelesaikan tugas pada tanggal ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </section>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>