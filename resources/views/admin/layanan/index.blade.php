<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Data Layanan - Rizhaqi Laundry Admin</title>
    <style>
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
            font-family: var(--google-sans);
            background-color: var(--surface-white);
        }

        .table-container tbody tr:nth-child(even) {
            background-color: var(--surface-white);
        }

        .table-container tbody tr:hover {
            background-color: rgba(26, 115, 232, 0.04);
        }

        .btn-action-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-detail {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .btn-detail.edit {
            background-color: var(--accent-blue);
            color: var(--primary-white);
            border: 1px solid var(--accent-blue);
        }

        .btn-detail.edit:hover {
            background-color: var(--accent-blue-hover);
            border-color: var(--accent-blue-hover);
        }

        .btn-detail.delete {
            background-color: var(--accent-red);
            color: var(--primary-white);
            border: 1px solid var(--accent-red);
        }

        .btn-detail.delete:hover {
            background-color: #c52c20;
            border-color: #c52c20;
        }

        .table-search-input {
            width: 0;
            padding: 0;
            border: none;
            transition: width 0.3s ease, padding 0.3s ease, border 0.3s ease;
            box-sizing: border-box;
            background: var(--surface-white);
            color: var(--text-primary);
            font-size: 14px;
            border-radius: 20px;
            margin-left: auto;
            outline: none;
            height: 40px;
        }

        .table-search-input.show {
            width: 200px;
            padding: 8px 12px;
            border: 1px solid var(--border-light);
        }

        .table-search-input.show:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1);
        }
        
        .btn-add {
            padding: 10px 20px;
            background: var(--accent-blue);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
                    <h1>Data Layanan</h1>
                    <ul class="breadcrumb">
                        <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="{{ route('admin.layanan.index') }}">Data Layanan</a></li>
                    </ul>
                </div>
                <a href="{{ route('admin.layanan.create') }}" class="btn-add">
                    <i class='bx bx-plus'></i> Tambah Layanan
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Daftar Harga & Layanan</h3>
                        <input type="text" id="tableSearchInput" class="table-search-input" placeholder="Cari layanan...">
                        <button id="tableSearchBtn" style="background:none; border:none; cursor:pointer; font-size:22px; display:flex; align-items:center;">
                            <i class='bx bx-search'></i>
                        </button>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th> 
                                    <th>Kategori</th>
                                    <th>Nama Layanan</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($layanan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    
                                    <td>
                                        {{-- LOGIKA WARNA KATEGORI BIAR CANTIK --}}
                                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; 
                                            background-color: {{ $item->kategori == 'Regular' ? '#e3f2fd' : ($item->kategori == 'Satuan' ? '#f3e5f5' : '#fce4ec') }};
                                            color: {{ $item->kategori == 'Regular' ? '#1565c0' : ($item->kategori == 'Satuan' ? '#7b1fa2' : '#c2185b') }};">
                                            {{ $item->kategori }}
                                        </span>
                                    </td>

                                    <td>{{ $item->nama_layanan }}</td>
                                    <td>{{ $item->satuan }}</td>
                                    
                                    <td style="font-weight: 500; color: #2e7d32;">
                                        {{-- LOGIKA HARGA RANGE VS TETAP --}}
                                        @if($item->is_flexible == 1)
                                            <span style="color: #f57c00;">
                                                Rp {{ number_format($item->harga_min, 0, ',', '.') }} - 
                                                Rp {{ number_format($item->harga_max, 0, ',', '.') }}
                                            </span>
                                        @else
                                            Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <div class="btn-action-group">
                                            {{-- TOMBOL EDIT --}}
                                            <a href="{{ route('admin.layanan.edit', $item->id_layanan) }}" class="btn-detail edit">
                                                <i class='bx bx-edit'></i> Edit
                                            </a>
                                            
                                            {{-- TOMBOL DELETE --}}
                                            <form action="{{ route('admin.layanan.destroy', $item->id_layanan) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-detail delete" onclick="return confirm('Yakin ingin menghapus layanan ini?')">
                                                    <i class='bx bx-trash'></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; padding: 20px; color: #999;">
                                        <i class='bx bx-folder-open' style="font-size: 24px; display:block; margin-bottom: 5px;"></i>
                                        Belum ada data layanan.
                                    </td>
                                </tr>
                                @endforelse
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
        const searchBtn = document.getElementById('tableSearchBtn');
        const searchInput = document.getElementById('tableSearchInput');

        // Toggle input search
        searchBtn.addEventListener('click', () => {
            searchInput.classList.toggle('show');
            if (searchInput.classList.contains('show')) searchInput.focus();
        });

        // Live search logic
        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('.table-container tbody tr');

            rows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                // Pastikan bukan baris "Belum ada data"
                if(row.cells.length > 1) {
                    row.style.display = rowText.includes(filter) ? '' : 'none';
                }
            });
        });
    </script>

</body>
</html>