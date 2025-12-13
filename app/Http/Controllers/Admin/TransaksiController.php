<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\TransaksiInventaris;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\DetailTransaksi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        // --- LOGIC FILTER (SEARCH + STATUS + TANGGAL) ---
        $query = Transaksi::with('pelanggan')
            // 1. Ambil semua kolom asli transaksi
            ->select('transaksi.*')

            // 2. PANGGIL FUNCTION DATABASE SEBAGAI KOLOM VIRTUAL
            // Kita namakan 'total_biaya' supaya di view tidak perlu ubah kodingan
            ->selectRaw('fn_hitung_total_transaksi(transaksi.id_transaksi) as total_biaya');

        // 1. Search (Invoice / Nama Pelanggan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_invoice', 'like', "%$search%")
                    ->orWhereHas('pelanggan', function ($p) use ($search) {
                        $p->where('nama', 'like', "%$search%");
                    });
            });
        }

        // 2. Filter Status Bayar
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }

        // 3. Filter Tanggal Masuk
        if ($request->filled('tanggal')) {
            $query->whereDate('tgl_masuk', $request->tanggal);
        }

        // Order & Paginate
        $transaksi = $query->orderBy('tgl_masuk', 'desc')->paginate(10);

        // Append query string (biar filter ga ilang pas ganti halaman)
        $transaksi->appends($request->all());

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function create()
{
    $pelanggan = Pelanggan::orderBy('nama', 'asc')->get();
    $layanan = Layanan::all();

    // AMBIL DATA KHUSUS ADD ON DARI DB
    $addOns = Layanan::where('kategori', 'ADD ON')->get(); // <--- TAMBAHAN INI

    // Sorting Kategori (Kode lama Anda tetap aman)
    $kategori = Layanan::select('kategori')
        ->where('kategori', '!=', 'ADD ON')
        ->distinct()
        ->get()
        ->sortBy(function ($item) {
            $urutan = [
                'REGULAR SERVICES' => 1,
                'PACKAGE SERVICES' => 2,
                'KARPET' => 3,
                'DISCOUNT JUMAT BERKAH' => 4,
                'DISCOUNT SELASA CERIA' => 5,
                'CUCI SATUAN' => 6
            ];
            return $urutan[$item->kategori] ?? 99;
        });

    // Jangan lupa kirim 'addOns' di compact
    return view('admin.transaksi.create', compact('pelanggan', 'layanan', 'kategori', 'addOns'));
}

    public function store(Request $request)
    {
        // 1. VALIDASI
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'no_hp' => 'required',
            'layanan_id' => 'required',
            'berat' => 'required|numeric|min:0.1',
            'harga_satuan' => 'required|numeric|min:0',
            'tgl_selesai' => 'required|date',
            'status_bayar' => 'required|in:belum,lunas,dp',
        ]);

        DB::beginTransaction();

        try {
            // 2. CEK / BUAT PELANGGAN
            $pelanggan = Pelanggan::where('nama', $request->nama_pelanggan)
                ->orWhere('telepon', $request->no_hp)
                ->first();

            if (!$pelanggan) {
                $pelanggan = Pelanggan::create([
                    'nama' => $request->nama_pelanggan,
                    'telepon' => $request->no_hp,
                    'alamat' => $request->alamat
                ]);
            } else {
                if ($request->filled('alamat')) {
                    $pelanggan->update(['alamat' => $request->alamat]);
                }
            }

            // 3. SIMPAN HEADER TRANSAKSI (VERSI TANPA KOLOM TOTAL BIAYA)
            $transaksi = Transaksi::create([
                'kode_invoice' => 'AUTO',
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_user' => Auth::id() ?? 1,
                'tgl_masuk' => Carbon::now(),
                'tgl_selesai' => $request->tgl_selesai,
                'berat' => $request->berat,

                // 'total_biaya' => 0,  <-- INI DIHAPUS, JANGAN ADA LAGI
                // 'jumlah_bayar' => 0, <-- INI JUGA BOLEH DIHAPUS (Default DB biasanya 0)
                // Tapi kalau di migration kamu jumlah_bayar tidak ada default, biarkan 0:
                'jumlah_bayar' => 0,

                'status_bayar' => 'belum',
                'status_pesanan' => 'diterima',
                'catatan' => $request->catatan,
            ]);

            // Refresh untuk dapat ID UUID & Kode Invoice
            $transaksi->refresh();

            // 4. SIMPAN DETAIL UTAMA
            $layananDb = Layanan::find($request->layanan_id);
            $hargaFinal = ($layananDb->is_flexible == 1) ? $request->harga_satuan : $layananDb->harga_satuan;

            DetailTransaksi::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'id_layanan' => $request->layanan_id,
                'jumlah' => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);

            // 5. SIMPAN DETAIL ADDON
            // 5. SIMPAN DETAIL ADDON (VERSI DINAMIS)
            // Ambil lagi daftar Add On dari database untuk dicek satu-satu
            $dbAddons = Layanan::where('kategori', 'ADD ON')->get();

            foreach ($dbAddons as $add) {
                // Cek apakah di form ada input bernama 'addon_[id_layanan]'
                // Contoh: addon_15, addon_16
                if ($request->has('addon_' . $add->id_layanan)) {
                    
                    // Ambil quantity-nya
                    $qty = $request->input('qty_' . $add->id_layanan, 0);

                    if ($qty > 0) {
                        DetailTransaksi::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'id_layanan' => $add->id_layanan, // Langsung pakai ID dari DB
                            'jumlah' => $qty,
                            'harga_saat_transaksi' => $add->harga_satuan,
                        ]);
                    }
                }
            }

            // 6. SIMPAN INVENTARIS
            if ($request->has('toggleDetail')) {
                $bajuOps = ['qty_baju', 'qty_kaos', 'qty_celana_panjang', 'qty_celana_pendek', 'qty_jilbab', 'qty_jaket', 'qty_kaos_kaki', 'qty_sarung', 'qty_lainnya'];
                foreach ($bajuOps as $field) {
                    $qty = $request->input($field);
                    if ($qty > 0) {
                        $namaBarang = ucwords(str_replace(['qty_', '_'], ['', ' '], $field));
                        TransaksiInventaris::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'nama_barang' => $namaBarang,
                            'jumlah' => $qty
                        ]);
                    }
                }
            }

            // 7. PROSES PEMBAYARAN
            if ($request->status_bayar != 'belum') {

                $uangBayar = 0;

                // KARENA KOLOM TOTAL HILANG, KITA TANYA FUNCTION DATABASE
                // "Eh DB, tolong hitungin total tagihan transaksi ini sekarang"
                $totalTagihan = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$transaksi->id_transaksi])[0]->total;

                if ($request->status_bayar == 'lunas') {
                    $uangBayar = $totalTagihan; // Bayar sesuai hitungan DB
                } elseif ($request->status_bayar == 'dp') {
                    $uangBayar = $request->jumlah_dp;
                }

                if ($uangBayar > 0) {
                    // Panggil Procedure Input Pembayaran
                    DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                        $transaksi->id_transaksi,
                        Auth::id() ?? 1,
                        $uangBayar,
                        $request->status_bayar == 'lunas' ? 'Lunas Awal' : 'DP Awal'
                    ]);
                }
            }

            DB::commit();

            // Refresh lagi buat pesan sukses
            $transaksi->refresh();

            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Order berhasil! Invoice: ' . $transaksi->kode_invoice);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        // 1. Ambil Data Transaksi Lama (Ini wajib ada di Edit)
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan', 'inventaris'])
            ->select('transaksi.*')
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')
            ->findOrFail($id);

        // 2. Data Dropdown (SAMA PERSIS DENGAN CREATE)
        $pelanggan = Pelanggan::orderBy('nama', 'asc')->get();
        $layanan = Layanan::all();

        // Sorting Kategori (SAMA PERSIS DENGAN CREATE)
        $kategori = Layanan::select('kategori')
            ->where('kategori', '!=', 'ADD ON')
            ->distinct()
            ->get()
            ->sortBy(function ($item) {
                $urutan = [
                    'REGULAR SERVICES' => 1,
                    'PACKAGE SERVICES' => 2,
                    'KARPET' => 3,
                    'DISCOUNT JUMAT BERKAH' => 4,
                    'DISCOUNT SELASA CERIA' => 5,
                    'CUCI SATUAN' => 6
                ];
                return $urutan[$item->kategori] ?? 99;
            });

        // 3. Kirim ke View (Tambah variabel $transaksi)
        return view('admin.transaksi.edit', compact('transaksi', 'pelanggan', 'layanan', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        // A. VALIDASI (Sama seperti Create)
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'no_hp' => 'required',
            'layanan_id' => 'required',
            'berat' => 'required|numeric|min:0.1',
            'harga_satuan' => 'required|numeric|min:0',
            'tgl_selesai' => 'required|date',
            'status_bayar' => 'required|in:belum,lunas,dp',
            // Jika pilih DP, nominal DP wajib diisi
            'jumlah_dp' => 'required_if:status_bayar,dp|numeric|min:0',
        ]);

        DB::beginTransaction(); // Mulai Transaksi Database

        try {
            $transaksi = Transaksi::findOrFail($id);

            // B. UPDATE DATA PELANGGAN
            $transaksi->pelanggan->update([
                'nama' => $request->nama_pelanggan,
                'telepon' => $request->no_hp,
                'alamat' => $request->alamat
            ]);

            // C. UPDATE HEADER TRANSAKSI
            // Catatan: status_bayar kita update belakangan setelah hitung uang
            $transaksi->update([
                'tgl_selesai' => $request->tgl_selesai,
                'berat' => $request->berat,
                'catatan' => $request->catatan,
            ]);

            // D. UPDATE DETAIL LAYANAN (HAPUS LAMA -> BUAT BARU)
            // 1. Hapus detail layanan lama biar bersih
            $transaksi->detailTransaksi()->delete();

            // 2. Simpan Layanan Utama Baru
            $layananDb = Layanan::find($request->layanan_id);
            // Validasi harga: Jika Fixed, paksa pakai harga DB. Jika Flexible, pakai input user.
            $hargaFinal = ($layananDb->is_flexible == 1) ? $request->harga_satuan : $layananDb->harga_satuan;

            DetailTransaksi::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'id_layanan' => $request->layanan_id,
                'jumlah' => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);

            // 3. Simpan Addons (Sama persis logika Create)
            // 3. Simpan Addons (VERSI DINAMIS - LOOPING DB)
            // Ambil daftar layanan kategori ADD ON dari database
            $dbAddons = Layanan::where('kategori', 'ADD ON')->get();

            foreach ($dbAddons as $add) {
                // Cek apakah di form edit ada input bernama 'addon_[ID]'
                if ($request->has('addon_' . $add->id_layanan)) {
                    
                    // Ambil quantity dari input 'qty_[ID]'
                    $qty = $request->input('qty_' . $add->id_layanan, 0);

                    if ($qty > 0) {
                        DetailTransaksi::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'id_layanan'   => $add->id_layanan, // Pakai ID langsung
                            'jumlah'       => $qty,
                            'harga_saat_transaksi' => $add->harga_satuan,
                        ]);
                    }
                }
            }

            // E. UPDATE INVENTARIS (HAPUS LAMA -> BUAT BARU)
            $transaksi->inventaris()->delete(); // Hapus data lama

            // Cek apakah user mencentang "Isi Rincian"
            if ($request->has('toggleDetail')) {
                $bajuOps = ['qty_baju', 'qty_kaos', 'qty_celana_panjang', 'qty_celana_pendek', 'qty_jilbab', 'qty_jaket', 'qty_kaos_kaki', 'qty_sarung', 'qty_lainnya'];
                foreach ($bajuOps as $field) {
                    $qty = $request->input($field, 0);
                    if ($qty > 0) {
                        // Ubah 'qty_celana_panjang' jadi 'Celana Panjang'
                        $namaBarang = ucwords(str_replace(['qty_', '_'], ['', ' '], $field));
                        TransaksiInventaris::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'nama_barang' => $namaBarang,
                            'jumlah' => $qty
                        ]);
                    }
                }
            }

            // F. LOGIKA KEUANGAN (AUTO-CORRECT STATUS BAYAR)
            // Karena berat/layanan mungkin berubah, Total Tagihan pasti berubah.

            // 1. Hitung Total Tagihan Baru (Panggil Function Database)
            $totalBaru = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$transaksi->id_transaksi])[0]->total;

            // 2. Ambil total yang SUDAH dibayar sebelumnya (dari kolom jumlah_bayar di table transaksi)
            $sudahBayar = $transaksi->jumlah_bayar;

            // 3. Cek Status yang dipilih User
            if ($request->status_bayar == 'lunas') {
                // Jika user pilih LUNAS, tapi uang kurang -> Tambahkan Pembayaran Pelunasan
                $kurang = $totalBaru - $sudahBayar;
                if ($kurang > 0) {
                    DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                        $transaksi->id_transaksi,
                        Auth::id() ?? 1,
                        $kurang,
                        'Pelunasan (Edit Order)'
                    ]);
                }
                $transaksi->update(['status_bayar' => 'lunas']);
            } elseif ($request->status_bayar == 'dp') {
                // Jika user pilih DP, kita update nominal DP nya
                // Hitung selisih input DP baru dengan uang yg sudah masuk
                $inputDP = $request->input('jumlah_dp', 0);
                $selisih = $inputDP - $sudahBayar;

                if ($selisih > 0) {
                    // Tambah pembayaran
                    DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                        $transaksi->id_transaksi,
                        Auth::id() ?? 1,
                        $selisih,
                        'Tambahan DP (Edit Order)'
                    ]);
                }
                // Update status jadi DP
                $transaksi->update(['status_bayar' => 'dp']);
            } else {
                // Jika user pilih BELUM BAYAR (atau tidak memilih)
                // Kita biarkan sistem menentukan status berdasarkan uang masuk vs total baru
                if ($sudahBayar >= $totalBaru && $totalBaru > 0) {
                    $transaksi->update(['status_bayar' => 'lunas']);
                } else if ($sudahBayar > 0) {
                    $transaksi->update(['status_bayar' => 'dp']);
                } else {
                    $transaksi->update(['status_bayar' => 'belum']);
                }
            }

            DB::commit(); // Simpan Semua Perubahan

            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Transaksi berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback(); // Batalkan jika er
            return redirect()->back()->with('error', 'Gagal Update: ' . $e->getMessage())->withInput();
        }
    }

    public function bayarCepat(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'nominal_bayar' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();
        try {
            // Ambil Data Awal
            $transaksi = Transaksi::findOrFail($id);

            // 2. JALANKAN LOGIC DATABASE (SP)
            // Biarkan Database yang bekerja mencatat pembayaran & trigger-trigger nya
            DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                $transaksi->id_transaksi,
                Auth::id() ?? 1,
                $request->nominal_bayar,
                'Pelunasan via Menu Cepat'
            ]);

            // ============================================================
            // KUNCI PERBAIKAN: REFRESH DATA!
            // ============================================================
            // Ambil ulang data terbaru dari database setelah SP selesai bekerja.
            // Supaya PHP tahu kondisi terkini (apakah trigger sudah update status?)
            $transaksi->refresh();

            // 3. LOGIC PELUNASAN (Opsional / Backup)
            // Cek dulu, apakah status sudah berubah otomatis oleh Trigger DB?
            // Jika status masih belum lunas, baru kita bantu update lewat Laravel.

            if ($transaksi->status_bayar != 'lunas') {

                // Hitung manual sisa tagihan (Total Biaya - Total Bayar)
                // Gunakan Raw Query agar akurat bypass cache model
                $totalMasuk = DB::table('pembayaran')->where('id_transaksi', $id)->sum('jumlah_bayar');

                // Panggil function DB untuk total tagihan (biar konsisten sama SP)
                $cekTotal = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$id]);
                $totalTagihan = $cekTotal[0]->total ?? $transaksi->total_biaya;

                // Logic Penentuan Status
                if ($totalMasuk >= $totalTagihan) {
                    $transaksi->status_bayar = 'lunas';
                } else {
                    $transaksi->status_bayar = 'dp';
                }

                // Simpan perubahan status (Hanya update kolom status, jangan sentuh yang lain)
                if ($transaksi->isDirty('status_bayar')) {
                    $transaksi->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil! Status sekarang: ' . strtoupper($transaksi->status_bayar));

        } catch (\Exception $e) {
            DB::rollback();
            // Tampilkan error lengkap untuk debugging
            return redirect()->back()->with('error', 'Error Database: ' . $e->getMessage());
        }
    }

    public function status()
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan', 'pembayaran', 'inventaris'])
            // 1. Ambil kolom asli
            ->select('transaksi.*')

            // 2. AMBIL TOTAL BIAYA (Virtual Column)
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')

            // 3. AMBIL SISA TAGIHAN (Virtual Column)
            // Biar kita gak perlu hitung manual (total - bayar) di view
            ->selectRaw('fn_sisa_tagihan(id_transaksi) as sisa_tagihan')
            ->orderBy('updated_at', 'desc')
            ->get();

        // 2. AMBIL JUMLAH STATUS (PANGGIL PROCEDURE)
        // Hasilnya berupa array object: [{'status_kategori': 'dicuci', 'total': 5}, ...]
        $rawCounts = DB::select("CALL sp_get_status_counts()");

        // 3. Siapkan Template Default (Supaya view tidak error jika ada status kosong)
        $counts = [
            'diterima' => 0,
            'dicuci' => 0,
            'dikeringkan' => 0,
            'disetrika' => 0,
            'packing' => 0,
            'siap' => 0,
            'selesai' => 0
        ];

        // 4. Masukkan Data dari Database ke Template
        foreach ($rawCounts as $row) {
            // Karena procedure sudah merapikan nama (lowercase & normalize),
            // kita tinggal pakai langsung sebagai key array.
            if (isset($counts[$row->status_kategori])) {
                $counts[$row->status_kategori] = $row->total;
            }
        }

        return view('admin.transaksi.status', compact('transaksi', 'counts'));
    }

    public function updateStatus(Request $request, $id)
    {
        // PERBAIKAN: Validasi harus menerima 'siap diambil' (pakai spasi)
        $request->validate([
            'status' => 'required|in:dicuci,dikeringkan,disetrika,packing,siap diambil,selesai'
        ]);

        try {
            if ($request->status == 'selesai') {
                // ... logic selesai (sama) ...
                DB::statement("CALL sp_ambil_cucian(?, ?)", [$id, Auth::id() ?? 1]);
                $msg = 'Order berhasil diselesaikan.';
            } else {
                // ... logic status lain ...
                DB::statement("CALL sp_update_status_transaksi(?, ?, ?)", [
                    $id,
                    $request->status, // Ini akan mengirim 'siap diambil' ke DB
                    Auth::id() ?? 1
                ]);
                $msg = 'Status order berhasil diperbarui.';
            }

            return back()->with('success', $msg);

        } catch (\Exception $e) {
            $pesan = $e->getMessage();

            // Bersihkan pesan error SQL biar enak dibaca user
            if (str_contains($pesan, 'GAGAL:')) {
                // Ambil teks setelah kata GAGAL:
                $pesan = substr($pesan, strpos($pesan, 'GAGAL:'));
            } else if (str_contains($pesan, 'Security Alert:')) {
                $pesan = substr($pesan, strpos($pesan, 'Security Alert:'));
            }

            return back()->with('error', $pesan);
        }
    }

    public function bayarCicilan(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'required',
            'jumlah_bayar' => 'required|numeric|min:1'
        ]);

        $trx = Transaksi::findOrFail($request->id_transaksi);

        Pembayaran::create([
            'id_transaksi' => $trx->id_transaksi,
            'id_user' => auth()->user()->id_user ?? 1,
            'jlh_pembayaran' => $request->jumlah_bayar,
            'keterangan' => 'Cicilan Tunai',
            'tgl_bayar' => now()
        ]);

        $totalSudahBayar = $trx->pembayaran()->sum('jlh_pembayaran');

        if ($totalSudahBayar >= $trx->total_biaya) {
            $trx->update(['status_bayar' => 'lunas']);
        } else {
            $trx->update(['status_bayar' => 'dp']);
        }

        return back()->with('success', 'Pembayaran berhasil dicatat!');
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan', 'pembayaran', 'inventaris'])
            // 1. Ambil kolom asli
            ->select('transaksi.*')

            // 2. AMBIL TOTAL BIAYA (Virtual Column)
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')

            // 3. AMBIL SISA TAGIHAN (Virtual Column)
            // Biar kita gak perlu hitung manual (total - bayar) di view
            ->selectRaw('fn_sisa_tagihan(id_transaksi) as sisa_tagihan')

            ->findOrFail($id);

        return view('admin.transaksi.show', compact('transaksi'));
    }


    public function destroy($id)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->back()->with('error', 'Admin tidak diizinkan untuk membatalkan transaksi.');
        }

        // Cari transaksi
        $transaksi = Transaksi::findOrFail($id);

        // Ubah status pesanan jadi 'dibatalkan'
        // Ubah juga status bayar jadi 'batal' (opsional, biar jelas di laporan keuangan)
        $transaksi->update([
            'status_pesanan' => 'batal'
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan (Status: Dibatalkan)');
    }
}