<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Menggunakan URL absolut untuk aset agar aman -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="/admin/css/style.css" />
    
    <title>Catat Pengeluaran - Rizhaqi Laundry</title>
    
    <style>
        /* STYLE FORM KONSISTEN */
        .form-card {
            background: #fff; 
            padding: 30px; 
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            border: 1px solid #e0e0e0;
            max-width: 600px; 
            margin: 24px auto;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; 
            font-size: 14px; 
            font-weight: 600; 
            color: #555; 
            margin-bottom: 8px; 
        }
        
        .form-group input, .form-group textarea {
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd;
            border-radius: 8px; 
            font-family: sans-serif; 
            font-size: 14px; 
            outline: none;
            background: #fff; 
            color: #333;
        }
        .form-group input:focus { 
            border-color: #2a9708; 
            box-shadow: 0 0 0 3px rgba(42, 151, 8, 0.1); 
        }
        
        /* BUTTONS */
        .form-actions { 
            display: flex; 
            justify-content: flex-end; 
            gap: 10px; 
            margin-top: 30px; 
            border-top: 1px solid #eee; 
            padding-top: 20px; 
        }
        .btn-submit { 
            background: #2a9708; 
            color: white; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
        }
        .btn-submit:hover { background: #217a05; }
        
        .btn-cancel { 
            background: #e0e0e0; 
            color: #424242; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
        }
        .btn-cancel:hover { background: #d6d6d6; }
    </style>
</head>
<body>

    @include('partial.sidebar')

    <section id="content">
        @include('partial.navbar')

        <main>
            <div class="head-title" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <div class="left">
                    <h1 style="font-size: 28px; font-weight: 600; color: #333;">Catat Pengeluaran Baru</h1>
                    <ul class="breadcrumb" style="display: flex; align-items: center; gap: 10px; list-style: none; padding: 0;">
                        <li><a href="#" style="color: #888; text-decoration: none; font-size: 14px;">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right' style="color: #888;"></i></li>
                        <li><a href="#" style="color: #888; text-decoration: none; font-size: 14px;">Pengeluaran</a></li>
                        <li><i class='bx bx-chevron-right' style="color: #888;"></i></li>
                        <li><a class="active" href="#" style="color: #2a9708; text-decoration: none; font-weight: 600; font-size: 14px;">Input</a></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <!-- Form action dummy ke pagar # atau rute statis -->
                <form id="formPengeluaran" onsubmit="return false;">
                    
                    <!-- TANGGAL PENGELUARAN (Nilai Statis) -->
                    <div class="form-group">
                        <label>Tanggal Pengeluaran</label>
                        <input type="date" name="tanggal" required value="2025-11-30">
                    </div>

                    <!-- KETERANGAN BARANG -->
                    <div class="form-group">
                        <label>Keterangan / Nama Barang</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Beli Deterjen 5L, Listrik, dll..." required autocomplete="off">
                    </div>

                    <!-- JUMLAH UANG -->
                    <div class="form-group">
                        <label>Total Biaya (Rp)</label>
                        <input type="number" name="jumlah" placeholder="Contoh: 50000" min="0" required>
                    </div>


                    <div class="form-actions">
                        <!-- Link cancel statis -->
                        <a href="/admin/pengeluaran" class="btn-cancel">
                            <i class='bx bx-x'></i> Batal
                        </a>
                        <!-- Tombol dengan JS Handler -->
                        <button type="button" class="btn-submit" onclick="simpanPengeluaran()">
                            <i class='bx bx-save'></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </section>

    <!-- Script JS Statis -->
    <script src="/admin/script/script.js"></script>
    <script src="/admin/script/sidebar.js"></script>
    
    <script>
        function simpanPengeluaran() {
            const form = document.getElementById('formPengeluaran');
            
            // Validasi Input HTML5
            if(!form.checkValidity()) { 
                form.reportValidity(); 
                return; 
            }
            
            // Efek Loading
            const btn = document.querySelector('.btn-submit');
            btn.disabled = true;
            btn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Menyimpan...";

            // Simulasi Simpan & Redirect ke Index
            setTimeout(() => {
                alert("Sukses! Pengeluaran berhasil dicatat (Simulasi).");
                // Redirect manual ke halaman index pengeluaran
                window.location.href = "/admin/pengeluaran"; 
            }, 1000);
        }
    </script>

</body>
</html>