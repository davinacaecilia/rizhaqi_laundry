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
            $query->where(function($q) use ($search) {
                $q->where('kode_invoice', 'like', "%$search%")
                  ->orWhereHas('pelanggan', function($p) use ($search) {
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

        // Sorting Kategori
        $kategori = Layanan::select('kategori')
            ->where('kategori', '!=', 'ADD ON')
            ->distinct()
            ->get()
            ->sortBy(function($item) {
                $urutan = [
                    'REGULAR SERVICES'      => 1,
                    'PACKAGE SERVICES'      => 2,
                    'KARPET'                => 3,
                    'DISCOUNT JUMAT BERKAH' => 4,
                    'DISCOUNT SELASA CERIA' => 5,
                    'CUCI SATUAN'           => 6
                ];
                return $urutan[$item->kategori] ?? 99;
            });

        return view('admin.transaksi.create', compact('pelanggan', 'layanan', 'kategori'));
    }

    public function store(Request $request)
    {
        // 1. VALIDASI
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'no_hp'          => 'required',
            'layanan_id'     => 'required',
            'berat'          => 'required|numeric|min:0.1',
            'harga_satuan'   => 'required|numeric|min:0',
            'tgl_selesai'    => 'required|date',
            'status_bayar'   => 'required|in:belum,lunas,dp',
        ]);

        DB::beginTransaction();

        try {
            // 2. CEK / BUAT PELANGGAN
            $pelanggan = Pelanggan::where('nama', $request->nama_pelanggan)
                ->orWhere('telepon', $request->no_hp)
                ->first();

            if (!$pelanggan) {
                $pelanggan = Pelanggan::create([
                    'nama'    => $request->nama_pelanggan,
                    'telepon' => $request->no_hp,
                    'alamat'  => $request->alamat
                ]);
            } else {
                if($request->filled('alamat')) {
                    $pelanggan->update(['alamat' => $request->alamat]);
                }
            }

            // 3. SIMPAN HEADER TRANSAKSI (VERSI TANPA KOLOM TOTAL BIAYA)
            $transaksi = Transaksi::create([
                'kode_invoice'   => 'AUTO', 
                'id_pelanggan'   => $pelanggan->id_pelanggan,
                'id_user'        => Auth::id() ?? 1,
                'tgl_masuk'      => Carbon::now(),
                'tgl_selesai'    => $request->tgl_selesai,
                'berat'          => $request->berat,
                
                // 'total_biaya' => 0,  <-- INI DIHAPUS, JANGAN ADA LAGI
                // 'jumlah_bayar' => 0, <-- INI JUGA BOLEH DIHAPUS (Default DB biasanya 0)
                // Tapi kalau di migration kamu jumlah_bayar tidak ada default, biarkan 0:
                'jumlah_bayar'   => 0,
                
                'status_bayar'   => 'belum',
                'status_pesanan' => 'diterima',
                'catatan'        => $request->catatan,
            ]);

            // Refresh untuk dapat ID UUID & Kode Invoice
            $transaksi->refresh(); 

            // 4. SIMPAN DETAIL UTAMA
            $layananDb = Layanan::find($request->layanan_id);
            $hargaFinal = ($layananDb->is_flexible == 1) ? $request->harga_satuan : $layananDb->harga_satuan;

            DetailTransaksi::create([
                'id_transaksi'         => $transaksi->id_transaksi,
                'id_layanan'           => $request->layanan_id,
                'jumlah'               => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);

            // 5. SIMPAN DETAIL ADDON
            $listAddons = ['ekspress', 'hanger', 'plastik', 'hanger_plastik'];
            foreach ($listAddons as $key) {
                if ($request->has("addon_$key")) {
                    $keyword = str_replace('_', ' ', $key);
                    $addonDb = Layanan::where('nama_layanan', 'LIKE', "%$keyword%")->first();
                    
                    if ($addonDb) {
                        $qty = $request->input("qty_$key", 0);
                        if ($qty > 0) {
                            DetailTransaksi::create([
                                'id_transaksi'         => $transaksi->id_transaksi,
                                'id_layanan'           => $addonDb->id_layanan,
                                'jumlah'               => $qty,
                                'harga_saat_transaksi' => $addonDb->harga_satuan,
                            ]);
                        }
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
                            'nama_barang'  => $namaBarang,
                            'jumlah'       => $qty
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

    public function update(Request $request, string $id) 
    { 
        // 1. VALIDASI
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'no_hp'          => 'required',
            'layanan_id'     => 'required',
            'berat'          => 'required|numeric|min:0.1',
            'harga_satuan'   => 'required|numeric|min:0',
            'tgl_selesai'    => 'required|date',
            'status_bayar'   => 'required|in:belum,lunas,dp',
        ]);

        DB::beginTransaction();

        try {
            $transaksi = Transaksi::findOrFail($id);

            // 2. UPDATE PELANGGAN
            $transaksi->pelanggan->update([
                'nama'    => $request->nama_pelanggan,
                'telepon' => $request->no_hp,
                'alamat'  => $request->alamat
            ]);

            // 3. UPDATE HEADER TRANSAKSI (Tanpa kolom total_biaya & jumlah_bayar)
            // Kita hanya update data administrasi. Keuangan biar DB yang urus.
            $transaksi->update([
                'tgl_selesai' => $request->tgl_selesai,
                'berat'       => $request->berat,
                'status_bayar'=> $request->status_bayar, // Ini sementara, nanti divalidasi ulang di bawah
                'catatan'     => $request->catatan,
            ]);

            // 4. RESET & UPDATE DETAIL LAYANAN UTAMA
            // Hapus detail lama, ganti baru
            $transaksi->detailTransaksi()->delete();
            
            $layananDb = Layanan::find($request->layanan_id);
            $hargaFinal = ($layananDb->is_flexible == 1) ? $request->harga_satuan : $layananDb->harga_satuan;

            DetailTransaksi::create([
                'id_transaksi'         => $transaksi->id_transaksi,
                'id_layanan'           => $request->layanan_id,
                'jumlah'               => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);

            // 5. UPDATE ADDONS
            $listAddons = ['ekspress', 'hanger', 'plastik', 'hanger_plastik'];
            foreach ($listAddons as $key) {
                if ($request->has("addon_$key")) { // Cek name="addon_..."
                    $keyword = str_replace('_', ' ', $key);
                    $addonDb = Layanan::where('nama_layanan', 'LIKE', "%$keyword%")->first();
                    
                    if ($addonDb) {
                        $qty = $request->input("qty_$key", 0);
                        if ($qty > 0) {
                            DetailTransaksi::create([
                                'id_transaksi'         => $transaksi->id_transaksi,
                                'id_layanan'           => $addonDb->id_layanan,
                                'jumlah'               => $qty,
                                'harga_saat_transaksi' => $addonDb->harga_satuan,
                            ]);
                        }
                    }
                }
            }

            // 6. UPDATE INVENTARIS
            // Hapus lama, simpan baru (Logika tanpa wajib centang toggle)
            $transaksi->inventaris()->delete(); 
            $bajuOps = ['qty_baju', 'qty_kaos', 'qty_celana_panjang', 'qty_celana_pendek', 'qty_jilbab', 'qty_jaket', 'qty_kaos_kaki', 'qty_sarung', 'qty_lainnya'];
            foreach ($bajuOps as $field) {
                $qty = $request->input($field, 0);
                if ($qty > 0) {
                    $namaBarang = ucwords(str_replace(['qty_', '_'], ['', ' '], $field));
                    TransaksiInventaris::create([
                        'id_transaksi' => $transaksi->id_transaksi,
                        'nama_barang'  => $namaBarang,
                        'jumlah'       => $qty
                    ]);
                }
            }

            // 7. LOGIKA KEUANGAN PASCA-EDIT (PENTING!)
            // Karena order berubah (berat naik/turun), total tagihan pasti berubah.
            // Kita harus cek apakah uang yang sudah masuk masih cukup?
            
            // Hitung Total Tagihan Baru dari Database
            $totalBaru = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$transaksi->id_transaksi])[0]->total;
            $sudahBayar = $transaksi->jumlah_bayar; // Uang yang sudah ada di kasir

            // Case A: Jika User pilih "LUNAS" di form, tapi uang kurang -> Panggil Procedure Pelunasan
            if ($request->status_bayar == 'lunas') {
                $kurang = $totalBaru - $sudahBayar;
                if ($kurang > 0) {
                    DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                        $transaksi->id_transaksi,
                        Auth::id() ?? 1,
                        $kurang,
                        'Pelunasan (Update Order)'
                    ]);
                }
            } 
            // Case B: Jika User tidak pilih Lunas, biarkan sistem cek statusnya otomatis
            // (Misal: Awalnya Lunas 50rb, diedit jadi 100rb -> Status harus turun jadi DP)
            else {
                if ($sudahBayar >= $totalBaru) {
                    $transaksi->update(['status_bayar' => 'lunas']);
                } else {
                    $transaksi->update(['status_bayar' => 'dp']);
                }
            }

            DB::commit();
            return redirect()->route('admin.transaksi.index')
                             ->with('success', 'Data Transaksi Berhasil Diupdate!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal Update: ' . $e->getMessage())->withInput();
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
            'diterima' => 0, 'dicuci' => 0, 'dikeringkan' => 0, 
            'disetrika' => 0, 'packing' => 0, 'siap' => 0, 'selesai' => 0
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
            'id_transaksi'   => $trx->id_transaksi,
            'id_user'        => auth()->user()->id_user ?? 1,
            'jlh_pembayaran' => $request->jumlah_bayar,
            'keterangan'     => 'Cicilan Tunai',
            'tgl_bayar'      => now()
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

    public function edit($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'pembayaran', 'detailTransaksi.layanan', 'inventaris'])
            ->select('transaksi.*')
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')
            ->selectRaw('fn_sisa_tagihan(id_transaksi) as sisa_tagihan')
            ->findOrFail($id);
        
        $pelanggan = Pelanggan::orderBy('nama', 'asc')->get();
        $layanan = Layanan::all(); 

        $kategori = Layanan::select('kategori')
            ->where('kategori', '!=', 'ADD ON')
            ->distinct()
            ->get()
            ->sortBy(function($item) {
                $urutan = [
                    'REGULAR SERVICES'      => 1,
                    'PACKAGE SERVICES'      => 2,
                    'KARPET'                => 3,
                    'DISCOUNT JUMAT BERKAH' => 4,
                    'DISCOUNT SELASA CERIA' => 5,
                    'CUCI SATUAN'           => 6
                ];
                return $urutan[$item->kategori] ?? 99;
            });
        
        return view('admin.transaksi.edit', compact('transaksi', 'pelanggan', 'layanan', 'kategori'));
    }

    public function destroy($id)
    {
        // Cari transaksi
        $transaksi = Transaksi::findOrFail($id);

        // Ubah status pesanan jadi 'dibatalkan'
        // Ubah juga status bayar jadi 'batal' (opsional, biar jelas di laporan keuangan)
        $transaksi->update([
            'status_pesanan' => 'dibatalkan',
            'status_bayar'   => 'batal' 
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan (Status: Dibatalkan)');
    }
}