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

        dd([
            'PESAN ERROR' => $e->getMessage(),
            'DI FILE' => $e->getFile(),
            'BARIS KE' => $e->getLine(),
            'TRACE' => $e->getTraceAsString() // Opsional, buat liat urutan proses
        ]);
        // return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
    }
}

    public function update(Request $request, string $id) 
    { 
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'no_hp'          => 'required',
            'layanan_id'     => 'required',
            'berat'          => 'required|numeric|min:1',
            'harga_satuan'   => 'required|numeric|min:0',
            'tgl_selesai'    => 'required|date',
            'status_bayar'   => 'required|in:belum,lunas,dp',
        ]);

        DB::beginTransaction();

        try {
            $transaksi = Transaksi::findOrFail($id);

            // 1. Update Data Pelanggan
            $transaksi->pelanggan->update([
                'nama'    => $request->nama_pelanggan,
                'telepon' => $request->no_hp,
                'alamat'  => $request->alamat
            ]);

            // 2. Hitung Ulang Total (Copy Logic from Store)
            $layananDb = Layanan::find($request->layanan_id);
            $hargaFinal = ($layananDb->is_flexible == 1) ? $request->harga_satuan : $layananDb->harga_satuan;
            
            $subtotalLayanan = $hargaFinal * $request->berat;
            $grandTotal = $subtotalLayanan;
            $listDetailToSave = [];

            // Helper Addon
            $addDetail = function($keywordName, $qtyForm, $inputCheck) use (&$grandTotal, &$listDetailToSave, $request) {
                if ($request->has($inputCheck)) {
                    $layananAddon = Layanan::where('nama_layanan', 'LIKE', "%$keywordName%")->first();
                    if ($layananAddon) {
                        $qty = $request->input($qtyForm, 0);
                        $subtotal = $layananAddon->harga_satuan * $qty;
                        $grandTotal += $subtotal;
                        $listDetailToSave[] = [
                            'id_layanan' => $layananAddon->id_layanan,
                            'jumlah'     => $qty,
                            'harga'      => $layananAddon->harga_satuan
                        ];
                    }
                }
            };

            $addDetail('Ekspress', 'qty_ekspress', 'addon_ekspress');
            $addDetail('Hanger', 'qty_hanger', 'addon_hanger');
            $addDetail('Plastik', 'qty_plastik', 'addon_plastik');
            $addDetail('Hanger + Plastik', 'qty_hanger_plastik', 'addon_hanger_plastik');

            // 3. Update Transaksi
            $jumlahBayar = ($request->status_bayar == 'lunas') ? $grandTotal : (($request->status_bayar == 'dp') ? $request->jumlah_dp : 0);

            $transaksi->update([
                'tgl_selesai'  => $request->tgl_selesai,
                'berat'        => $request->berat,
                'total_biaya'  => $grandTotal,
                'jumlah_bayar' => $jumlahBayar,
                'status_bayar' => $request->status_bayar,
                'catatan'      => $request->catatan,
            ]);

            // 4. Update Detail (Hapus lama, buat baru)
            $transaksi->detailTransaksi()->delete();
            
            DetailTransaksi::create([
                'id_transaksi'         => $transaksi->id_transaksi,
                'id_layanan'           => $request->layanan_id,
                'jumlah'               => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);

            foreach ($listDetailToSave as $detail) {
                DetailTransaksi::create([
                    'id_transaksi'         => $transaksi->id_transaksi,
                    'id_layanan'           => $detail['id_layanan'],
                    'jumlah'               => $detail['jumlah'],
                    'harga_saat_transaksi' => $detail['harga'],
                ]);
            }

            // 5. Update Inventaris
            if ($request->has('toggleDetail')) {
                $transaksi->inventaris()->delete(); 
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
        $transaksi = Transaksi::with('pelanggan')->orderBy('id_transaksi', 'desc')->get();
        
        $counts = [
            'diterima' => Transaksi::where('status_pesanan', 'diterima')->count(),
            'dicuci' => Transaksi::where('status_pesanan', 'dicuci')->count(),
            'dikeringkan' => Transaksi::where('status_pesanan', 'dikeringkan')->count(),
            'disetrika' => Transaksi::where('status_pesanan', 'disetrika')->count(),
            'packing' => Transaksi::where('status_pesanan', 'packing')->count(),
            'siap' => Transaksi::where('status_pesanan', 'siap')->orWhere('status_pesanan', 'siap diambil')->count(),
            'selesai' => Transaksi::where('status_pesanan', 'selesai')->count(),
        ];

        return view('admin.transaksi.status', compact('transaksi', 'counts'));
    }

    public function updateStatus(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $statusBaru = $request->status;

        $dataUpdate = ['status_pesanan' => $statusBaru];

        // --- LOGIKA AUTO LUNAS ---
        if ($statusBaru == 'selesai') {
            $dataUpdate['status_bayar'] = 'lunas';
            $dataUpdate['jumlah_bayar'] = $transaksi->total_biaya;
        }

        $transaksi->update($dataUpdate);

        return back()->with('success', 'Status berhasil diupdate!');
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
        Transaksi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Transaksi Dihapus!');
    }
}