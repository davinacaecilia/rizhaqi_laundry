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
        // === DEBUGGING: LOG DATA REQUEST ===
        \Log::info('=== DATA REQUEST MASUK ===');
        \Log::info('Semua Data:', $request->all());
        \Log::info('Harga Satuan:', $request->harga_satuan);
        \Log::info('Berat:', $request->berat);
        \Log::info('Status Bayar:', $request->status_bayar);
        
        // 1. VALIDASI (DIPERBAIKI: min:1 untuk harga_satuan)
        try {
            $validated = $request->validate([
                'nama_pelanggan' => 'required|string|max:100',
                'no_hp'          => 'required|string|max:15',
                'layanan_id'     => 'required|exists:layanan,id_layanan',
                'berat'          => 'required|numeric|min:0.1',
                'harga_satuan'   => 'required|numeric|min:1', // ← PERBAIKAN: min:1 (tidak boleh 0)
                'tgl_selesai'    => 'required|date|after_or_equal:today',
                'status_bayar'   => 'required|in:belum,lunas,dp',
                'jumlah_dp'      => 'required_if:status_bayar,dp|nullable|numeric|min:1', // ← PERBAIKAN: Validasi DP
            ]);
            
            \Log::info('✅ Validasi Berhasil');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Validasi Gagal:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Data tidak valid! Periksa kembali form Anda.');
        }

        DB::beginTransaction();

        try {
            \Log::info('🔄 Mulai Transaksi Database...');
            
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
                \Log::info('✅ Pelanggan Baru Dibuat:', ['id' => $pelanggan->id_pelanggan]);
            } else {
                if($request->filled('alamat')) {
                    $pelanggan->update(['alamat' => $request->alamat]);
                }
                \Log::info('✅ Pelanggan Existing:', ['id' => $pelanggan->id_pelanggan]);
            }

            // 3. SIMPAN HEADER TRANSAKSI
            $transaksi = Transaksi::create([
                'kode_invoice'   => 'AUTO', 
                'id_pelanggan'   => $pelanggan->id_pelanggan,
                'id_user'        => Auth::id() ?? 1,
                'tgl_masuk'      => Carbon::now(),
                'tgl_selesai'    => $request->tgl_selesai,
                'berat'          => $request->berat,
                'jumlah_bayar'   => 0,
                'status_bayar'   => 'belum',
                'status_pesanan' => 'diterima',
                'catatan'        => $request->catatan,
            ]);

            $transaksi->refresh(); 
            \Log::info('✅ Transaksi Header Dibuat:', ['invoice' => $transaksi->kode_invoice]);

            // 4. SIMPAN DETAIL LAYANAN UTAMA
            $layananDb = Layanan::find($request->layanan_id);
            
            // PERBAIKAN: Validasi harga untuk flexible pricing
            if ($layananDb->is_flexible == 1) {
                $hargaMin = $layananDb->harga_satuan;
                $hargaMax = $layananDb->harga_maksimum;
                $hargaInput = $request->harga_satuan;
                
                // Cek apakah harga dalam rentang yang valid
                if ($hargaInput < $hargaMin || $hargaInput > $hargaMax) {
                    throw new \Exception("Harga tidak valid! Harus antara Rp " . number_format($hargaMin) . " - Rp " . number_format($hargaMax));
                }
                
                $hargaFinal = $hargaInput;
            } else {
                $hargaFinal = $layananDb->harga_satuan;
            }

            DetailTransaksi::create([
                'id_transaksi'         => $transaksi->id_transaksi,
                'id_layanan'           => $request->layanan_id,
                'jumlah'               => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);
            
            \Log::info('✅ Detail Layanan Utama Disimpan:', [
                'layanan' => $layananDb->nama_layanan,
                'harga' => $hargaFinal,
                'jumlah' => $request->berat
            ]);

            // 5. SIMPAN DETAIL ADDON (DIPERBAIKI: Cek qty > 0)
            $listAddons = ['ekspress', 'hanger', 'plastik', 'hanger_plastik'];
            foreach ($listAddons as $key) {
                // PERBAIKAN: Cek checkbox DAN qty > 0
                if ($request->has("addon_$key") && $request->filled("qty_$key")) {
                    $qty = $request->input("qty_$key", 0);
                    
                    if ($qty > 0) {
                        $keyword = str_replace('_', ' ', $key);
                        $addonDb = Layanan::where('nama_layanan', 'LIKE', "%$keyword%")->first();
                        
                        if ($addonDb) {
                            DetailTransaksi::create([
                                'id_transaksi'         => $transaksi->id_transaksi,
                                'id_layanan'           => $addonDb->id_layanan,
                                'jumlah'               => $qty,
                                'harga_saat_transaksi' => $addonDb->harga_satuan,
                            ]);
                            
                            \Log::info("✅ Addon '{$addonDb->nama_layanan}' Disimpan: {$qty} x Rp " . number_format($addonDb->harga_satuan));
                        }
                    }
                }
            }

            // 6. SIMPAN INVENTARIS
            if ($request->has('toggleDetail') && $request->toggleDetail == 'on') {
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
                \Log::info('✅ Inventaris Pakaian Disimpan');
            }

            // 7. PROSES PEMBAYARAN
            if ($request->status_bayar != 'belum') {
                
                // Hitung total dari database
                $totalTagihan = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$transaksi->id_transaksi])[0]->total;
                \Log::info('💰 Total Tagihan dari DB:', ['total' => $totalTagihan]);
                
                $uangBayar = 0;

                if ($request->status_bayar == 'lunas') {
                    $uangBayar = $totalTagihan;
                } elseif ($request->status_bayar == 'dp') {
                    $uangBayar = $request->jumlah_dp;
                    
                    // PERBAIKAN: Validasi DP tidak boleh lebih dari total
                    if ($uangBayar > $totalTagihan) {
                        throw new \Exception("Jumlah DP (Rp " . number_format($uangBayar) . ") tidak boleh lebih dari total tagihan (Rp " . number_format($totalTagihan) . ")");
                    }
                }

                if ($uangBayar > 0) {
                    DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                        $transaksi->id_transaksi,
                        Auth::id() ?? 1,
                        $uangBayar,
                        $request->status_bayar == 'lunas' ? 'Lunas Awal' : 'DP Awal'
                    ]);
                    
                    \Log::info('✅ Pembayaran Dicatat:', [
                        'status' => $request->status_bayar,
                        'jumlah' => $uangBayar
                    ]);
                }
            }

            DB::commit();
            \Log::info('✅ TRANSAKSI BERHASIL DISIMPAN!', ['invoice' => $transaksi->kode_invoice]);

            $transaksi->refresh();

            return redirect()->route('admin.transaksi.index')
                            ->with('success', 'Order berhasil dibuat! Invoice: ' . $transaksi->kode_invoice);

        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('❌ ERROR SAAT MENYIMPAN:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())
                ->withInput();
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
            'harga_satuan'   => 'required|numeric|min:1', // ← PERBAIKAN
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

            // 3. UPDATE HEADER TRANSAKSI
            $transaksi->update([
                'tgl_selesai' => $request->tgl_selesai,
                'berat'       => $request->berat,
                'status_bayar'=> $request->status_bayar,
                'catatan'     => $request->catatan,
            ]);

            // 4. RESET & UPDATE DETAIL LAYANAN UTAMA
            $transaksi->detailTransaksi()->delete();
            
            $layananDb = Layanan::find($request->layanan_id);
            $hargaFinal = ($layananDb->is_flexible == 1) ? $request->harga_satuan : $layananDb->harga_satuan;

            DetailTransaksi::create([
                'id_transaksi'         => $transaksi->id_transaksi,
                'id_layanan'           => $request->layanan_id,
                'jumlah'               => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);

            // 5. UPDATE ADDONS (DIPERBAIKI)
            $listAddons = ['ekspress', 'hanger', 'plastik', 'hanger_plastik'];
            foreach ($listAddons as $key) {
                if ($request->has("addon_$key") && $request->filled("qty_$key")) {
                    $qty = $request->input("qty_$key", 0);
                    
                    if ($qty > 0) {
                        $keyword = str_replace('_', ' ', $key);
                        $addonDb = Layanan::where('nama_layanan', 'LIKE', "%$keyword%")->first();
                        
                        if ($addonDb) {
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

            // 7. LOGIKA KEUANGAN PASCA-EDIT
            $totalBaru = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$transaksi->id_transaksi])[0]->total;
            $sudahBayar = $transaksi->jumlah_bayar;

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
            } else {
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
            ->select('transaksi.*')
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')
            ->selectRaw('fn_sisa_tagihan(id_transaksi) as sisa_tagihan')
            ->orderBy('updated_at', 'desc')
            ->get();

        $rawCounts = DB::select("CALL sp_get_status_counts()");

        $counts = [
            'diterima' => 0, 'dicuci' => 0, 'dikeringkan' => 0, 
            'disetrika' => 0, 'packing' => 0, 'siap' => 0, 'selesai' => 0
        ];

        foreach ($rawCounts as $row) {
            if (isset($counts[$row->status_kategori])) {
                $counts[$row->status_kategori] = $row->total;
            }
        }

        return view('admin.transaksi.status', compact('transaksi', 'counts'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:dicuci,dikeringkan,disetrika,packing,siap diambil,selesai'
        ]);

        try {
            if ($request->status == 'selesai') {
                DB::statement("CALL sp_ambil_cucian(?, ?)", [$id, Auth::id() ?? 1]);
                $msg = 'Order berhasil diselesaikan.';
            } else {
                DB::statement("CALL sp_update_status_transaksi(?, ?, ?)", [
                    $id,
                    $request->status,
                    Auth::id() ?? 1
                ]);
                $msg = 'Status order berhasil diperbarui.';
            }

            return back()->with('success', $msg);

        } catch (\Exception $e) {
            $pesan = $e->getMessage();
            
            if (str_contains($pesan, 'GAGAL:')) {
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
            ->select('transaksi.*')
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')
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
        $transaksi = Transaksi::findOrFail($id);

        $transaksi->update([
            'status_pesanan' => 'dibatalkan',
            'status_bayar'   => 'batal' 
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan (Status: Dibatalkan)');
    }
}