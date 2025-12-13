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
        $query = Transaksi::with('pelanggan')
            ->select('transaksi.*')
            ->selectRaw('fn_hitung_total_transaksi(transaksi.id_transaksi) as total_biaya');

        // Filter Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_invoice', 'like', "%$search%")
                  ->orWhereHas('pelanggan', function($p) use ($search) {
                      $p->where('nama', 'like', "%$search%");
                  });
            });
        }

        // Filter Status Bayar
        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('tgl_masuk', $request->date);
        }

        // --- SHOW ALL SEMENTARA (PAGINATION DIKOMEN) ---
        // $transaksi = $query->orderBy('tgl_masuk', 'desc')->paginate(10);
        // $transaksi->appends($request->all());

        $transaksi = $query->orderBy('tgl_masuk', 'desc')->get();

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
        // VALIDASI
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_hp'          => 'required|string|max:15',
            'layanan_id'     => 'required|exists:layanan,id_layanan',
            'berat'          => 'required|numeric|min:0.1',
            'harga_satuan'   => 'required|numeric|min:1',
            'tgl_selesai'    => 'required|date|after_or_equal:today',
            'status_bayar'   => 'required|in:belum,lunas,dp',
            'jumlah_dp'      => 'required_if:status_bayar,dp|nullable|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            // CEK / BUAT PELANGGAN
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

            // SIMPAN HEADER TRANSAKSI
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

            // SIMPAN DETAIL LAYANAN UTAMA
            $layananDb = Layanan::find($request->layanan_id);
            
            if ($layananDb->is_flexible == 1) {
                $hargaMin = $layananDb->harga_satuan;
                $hargaMax = $layananDb->harga_maksimum;
                $hargaInput = $request->harga_satuan;
                
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

            // SIMPAN DETAIL ADDON
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

            // SIMPAN INVENTARIS
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
            }

            // PROSES PEMBAYARAN
            if ($request->status_bayar != 'belum') {
                $totalTagihan = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$transaksi->id_transaksi])[0]->total;
                $uangBayar = 0;

                if ($request->status_bayar == 'lunas') {
                    $uangBayar = $totalTagihan;
                } elseif ($request->status_bayar == 'dp') {
                    $uangBayar = $request->jumlah_dp;
                    if ($uangBayar > $totalTagihan) {
                        throw new \Exception("Jumlah DP tidak boleh lebih dari total tagihan.");
                    }
                }

                if ($uangBayar > 0) {
                    DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                        $transaksi->id_transaksi,
                        Auth::id() ?? 1,
                        $uangBayar,
                        $request->status_bayar == 'lunas' ? 'Lunas Awal' : 'DP Awal'
                    ]);
                }
            }

            DB::commit();
            $transaksi->refresh();

            return redirect()->route('admin.transaksi.index')
                            ->with('success', 'Order berhasil dibuat! Invoice: ' . $transaksi->kode_invoice);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan', 'inventaris'])
            ->select('transaksi.*')
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')
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

    public function update(Request $request, $id) 
    { 
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'no_hp'          => 'required',
            'layanan_id'     => 'required',
            'berat'          => 'required|numeric|min:0.1',
            'harga_satuan'   => 'required|numeric|min:1',
            'tgl_selesai'    => 'required|date',
            'status_bayar'   => 'required|in:belum,lunas,dp',
            'jumlah_dp'      => 'required_if:status_bayar,dp|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $transaksi = Transaksi::findOrFail($id);

            // UPDATE DATA PELANGGAN
            $transaksi->pelanggan->update([
                'nama'    => $request->nama_pelanggan,
                'telepon' => $request->no_hp,
                'alamat'  => $request->alamat
            ]);

            // UPDATE HEADER TRANSAKSI
            $transaksi->update([
                'tgl_selesai' => $request->tgl_selesai,
                'berat'       => $request->berat,
                'catatan'     => $request->catatan,
            ]);

            // UPDATE DETAIL LAYANAN
            $transaksi->detailTransaksi()->delete();
            
            $layananDb = Layanan::find($request->layanan_id);
            $hargaFinal = ($layananDb->is_flexible == 1) ? $request->harga_satuan : $layananDb->harga_satuan;

            DetailTransaksi::create([
                'id_transaksi'         => $transaksi->id_transaksi,
                'id_layanan'           => $request->layanan_id,
                'jumlah'               => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);

            // UPDATE ADDONS
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

            // UPDATE INVENTARIS
            $transaksi->inventaris()->delete();
            if($request->has('toggleDetail')) {
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
            }

            // LOGIKA KEUANGAN (AUTO-CORRECT STATUS BAYAR)
            $totalBaru = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$transaksi->id_transaksi])[0]->total;
            $sudahBayar = $transaksi->jumlah_bayar; 

            if ($request->status_bayar == 'lunas') {
                $kurang = $totalBaru - $sudahBayar;
                if ($kurang > 0) {
                    DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                        $transaksi->id_transaksi, Auth::id() ?? 1, $kurang, 'Pelunasan (Edit Order)'
                    ]);
                }
                $transaksi->update(['status_bayar' => 'lunas']);
            } 
            elseif ($request->status_bayar == 'dp') {
                $inputDP = $request->input('jumlah_dp', 0);
                $selisih = $inputDP - $sudahBayar;

                if($selisih > 0) {
                    DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                        $transaksi->id_transaksi, Auth::id() ?? 1, $selisih, 'Tambahan DP (Edit Order)'
                    ]);
                }
                $transaksi->update(['status_bayar' => 'dp']);
            }
            else {
                if ($sudahBayar >= $totalBaru && $totalBaru > 0) {
                    $transaksi->update(['status_bayar' => 'lunas']);
                } else if ($sudahBayar > 0) {
                    $transaksi->update(['status_bayar' => 'dp']);
                } else {
                    $transaksi->update(['status_bayar' => 'belum']);
                }
            }

            DB::commit();
            
            return redirect()->route('admin.transaksi.index')
                             ->with('success', 'Transaksi berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal Update: ' . $e->getMessage())->withInput();
        }
    }

    public function bayarCepat(Request $request, $id)
    {
        $request->validate([
            'nominal_bayar' => 'required|numeric|min:1'
        ]);

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::findOrFail($id);
            
            DB::statement("CALL sp_input_pembayaran(?, ?, ?, ?)", [
                $transaksi->id_transaksi,
                Auth::id() ?? 1, 
                $request->nominal_bayar,
                'Pelunasan via Menu Cepat'
            ]);

            $transaksi->refresh(); 

            if ($transaksi->status_bayar != 'lunas') {
                $totalMasuk = DB::table('pembayaran')->where('id_transaksi', $id)->sum('jumlah_bayar');
                $cekTotal = DB::select("SELECT fn_hitung_total_transaksi(?) as total", [$id]);
                $totalTagihan = $cekTotal[0]->total ?? $transaksi->total_biaya;

                if ($totalMasuk >= $totalTagihan) {
                    $transaksi->status_bayar = 'lunas';
                } else {
                    $transaksi->status_bayar = 'dp';
                }
                
                if($transaksi->isDirty('status_bayar')){
                    $transaksi->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil! Status sekarang: ' . strtoupper($transaksi->status_bayar));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error Database: ' . $e->getMessage());
        }
    }

    public function status(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'detailTransaksi.layanan', 'pembayaran', 'inventaris'])
            ->select('transaksi.*')
            ->selectRaw('fn_hitung_total_transaksi(id_transaksi) as total_biaya')
            ->selectRaw('fn_sisa_tagihan(id_transaksi) as sisa_tagihan');

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status_pesanan', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('tgl_masuk', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_invoice', 'like', "%$search%")
                  ->orWhereHas('pelanggan', function($p) use ($search) {
                      $p->where('nama', 'like', "%$search%");
                  });
            });
        }

        // --- SHOW ALL SEMENTARA (PAGINATION DIKOMEN) ---
        // $transaksi = $query->orderBy('updated_at', 'desc')->paginate(10);
        // $transaksi->appends($request->all());

        $transaksi = $query->orderBy('updated_at', 'desc')->get();

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

    public function destroy($id)
    {
        // Cari transaksi
        $transaksi = Transaksi::findOrFail($id);

        $transaksi->update([
            'status_pesanan' => 'batal', // sesuai standar database 'batal' atau 'dibatalkan'
            'status_bayar'   => 'batal' 
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan (Status: Dibatalkan)');
    }
}