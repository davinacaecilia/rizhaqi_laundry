<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/pagination.css') }}" />

    <title>Log Aktivitas - Rizhaqi Laundry Admin</title>

    <style>
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
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

        .table-container tbody tr:nth-child(even) {
            background-color: var(--surface-white);
        }

        .table-container tbody tr:hover {
            background-color: rgba(26,115,232,0.04);
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
                <h1>Log Aktivitas</h1>
                <ul class="breadcrumb">
                    <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li><a class="active" href="#">Log Aktivitas</a></li>
                </ul>
            </div>
        </div>

        <div class="table-data">
            <div class="order">
                <div class="head">
                    <h3>Log Aktivitas</h3>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID User</th>
                                <th>Aksi</th>
                                <th>Keterangan</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($logs as $log)
                            <tr>
                                <td>
                                    @if ($log->id_user)
                                        {{ $log->user->nama }}
                                    @else
                                        <span style="color:#999; font-style:italic;">Tidak diketahui</span>
                                    @endif
                                </td>
                                <td>{{ $log->aksi }}</td>
                                <td>{{ $log->keterangan }}</td>
                                <td>{{ $log->waktu }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
</section>

<script src="{{ asset('admin/script/script.js') }}"></script>
<script src="{{ asset('admin/script/pagination.js') }}"></script>
<script src="{{ asset('admin/script/chart.js') }}"></script>
<script src="{{ asset('admin/script/sidebar.js') }}"></script>

</body>
</html>
