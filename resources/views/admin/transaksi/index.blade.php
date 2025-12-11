<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Manajemen Transaksi - Rizhaqi Laundry Admin</title>
    
    <style>
        /* CSS BAWAAN (TIDAK DIUBAH) */
        .table-container table { width: 100%; border-collapse: collapse; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-light); }
        .table-container thead tr { background-color: var(--surface-white); border-bottom: 1px solid var(--border-light); }
        
        .table-container th, .table-container td { padding: 15px; border: 1px solid var(--border-light); text-align: left; font-size: 14px; color: var(--text-primary); }
        .table-container th { font-weight: 600; color: var(--text-secondary); font-family: var(--google-sans); background-color: var(--surface-white); }
        .table-container tbody tr:hover { background-color: rgba(26, 115, 232, 0.04); }

        .table-container .btn-action-group { display: flex; gap: 5px; flex-wrap: wrap; }
        .table-container .btn-action-group .btn-detail { padding: 6px 12px; font-size: 12px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; transition: all 0.2s ease; font-weight: 500; border: none; cursor: pointer; }
        
        .btn-detail.show { background-color: var(--accent-green); color: white; }
        .btn-detail.edit { background-color: var(--accent-blue); color: white; }
        .btn-detail.delete { background-color: var(--accent-red); color: white; }
        .btn-detail.disabled { background-color: #e0e0e0 !important; color: #999 !important; cursor: not-allowed; pointer-events: none; }
        .btn-detail:hover { opacity: 0.9; }

        .badge-bayar { font-weight: 700; font-size: 12px; }
        .bayar-lunas { color: #2E7D32; }
        .bayar-dp { color: #F9A825; }
        .bayar-belum { color: #C62828; }
        
        .table-data .order .head { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .table-data .order .head h3 { margin-right: auto; }

        .filter-input { padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 20px; font-size: 13px; outline: none; background: #fff; color: var(--text-primary); }
        
        .table-search-wrapper { position: relative; display: flex; align-items: center; }
        .table-search-input { width: 0; padding: 0; border: none; transition: width 0.3s ease; background: var(--surface-white); border-radius: 20px; outline: none; height: 40px; }
        .table-search-input.show { width: 200px; padding: 8px 12px; border: 1px solid var(--border-light); }
        .bx-search { cursor: pointer; font-size: 20px; padding: 5px; }

    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Manajemen Transaksi</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a href="{{ route('admin.transaksi.index') }}">Manajemen Transaksi</a></li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li><a class="active" href="{{ route('admin.transaksi.index') }}">Data Transaksi</a></li>
                    </ul>
                </div>
                
                <a href="{{ route('admin.transaksi.create') }}" class="btn-download">
                    <i class='bx bx-plus'></i> Tambah Order
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Data Transaksi</h3>
                        
                        <div class="table-search-wrapper">
                            <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari Nama/Invoice...">
                            <i class='bx bx-search' id="tableSearchIcon"></i>
                        </div>

                        <input type="date" id="dateFilter" class="filter-input" title="Filter Tanggal Masuk">
                        
                        <select id="statusFilter" class="filter-input">
                            <option value="">Semua Status</option>
                            <option value="LUNAS">Lunas</option>
                            <option value="BELUM">Belum Bayar</option>
                            <option value="DP">DP (Uang Muka)</option>
                        </select>
                    </div>
                    
                    <div class="table-container">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th>Kode Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Estimasi Selesai</th>
                                    <th>Status Pembayaran</th>
                                    <th>Total Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaksi as $item)
                                <tr>
                                    <td><strong>{{ $item->kode_invoice }}</strong></td>
                                    
                                    <td>{{ $item->pelanggan->nama ?? 'Pelanggan Dihapus' }}</td>
                                    
                                    <td>{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d M Y') }}</td>
                                    
                                    <td>
                                        @if($item->tgl_selesai)
                                            {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($item->status_bayar == 'lunas')
                                            <span class="badge-bayar bayar-lunas">LUNAS</span>
                                        @elseif($item->status_bayar == 'dp')
                                            <span class="badge-bayar bayar-dp">
                                                DP Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="badge-bayar bayar-belum">BELUM BAYAR</span>
                                        @endif
                                    </td>
                                    
                                    <td>{{ $item->total_biaya_format }}</td>
                                    
                                    <td>
                                        <div class="btn-action-group">
                                            @if($item->status_bayar == 'lunas')
                                                <a href="#" class="btn-detail edit disabled"><i class='bx bx-edit'></i></a>
                                                <a href="#" class="btn-detail delete disabled"><i class='bx bx-trash'></i></a>
                                            @else
                                                <a href="{{ route('admin.transaksi.edit', $item->id_transaksi) }}" class="btn-detail edit"><i class='bx bx-edit'></i></a>
                                                
                                                <form action="{{ route('admin.transaksi.destroy', $item->id_transaksi) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-detail delete" onclick="return confirm('Yakin hapus transaksi ini?')">
                                                        <i class='bx bx-trash'></i>
                                                    </button>
                                                </form>
                                            @endif
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

    <div id="pagination" class="pagination-container">
        {{ $transaksi->links() }}
    </div>

    <script src="{{ asset('admin/script/script.js') }}"></script>
    <script src="{{ asset('admin/script/sidebar.js') }}"></script>
    

    
</body>
</html>