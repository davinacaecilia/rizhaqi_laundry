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
    public function index()
    {
        $transaksi = Transaksi::with('pelanggan')
            ->orderBy('tgl_masuk', 'desc')
            ->paginate(10);

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        // 1. Ambil Pelanggan
        $pelanggan = Pelanggan::orderBy('nama', 'asc')->get();
        
        // 2. Ambil Semua Layanan (Penting buat JS)
        $layanan = Layanan::all();

        // 3. AMBIL KATEGORI & URUTKAN (KANAN -> KIRI)
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
            // 1. Cek/Buat Pelanggan
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

            // 2. Generate Invoice
            $lastTrx = Transaksi::latest('created_at')->first();
            if (!$lastTrx) {
                $kodeInvoice = 'A0001';
            } else {
                $lastCode = $lastTrx->kode_invoice;
                $huruf = substr($lastCode, 0, 1);
                $angka = intval(substr($lastCode, 1));

                if ($angka >= 9999) {
                    $huruf++; 
                    $angka = 1;
                } else {
                    $angka++;
                }
                $kodeInvoice = $huruf . str_pad($angka, 4, '0', STR_PAD_LEFT);
            }

            // 3. Hitung Total
            $layananDb = Layanan::find($request->layanan_id);
            $hargaFinal = ($layananDb->is_flexible == 1) ? $request->harga_satuan : $layananDb->harga_satuan;

            $subtotalLayanan = $hargaFinal * $request->berat;
            $grandTotal = $subtotalLayanan;
            $listDetailToSave = [];

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

            // 4. Hitung Bayar
            $jumlahBayar = 0;
            if ($request->status_bayar == 'lunas') $jumlahBayar = $grandTotal;
            elseif ($request->status_bayar == 'dp') $jumlahBayar = $request->jumlah_dp;

            // 5. Simpan Transaksi
            $transaksi = Transaksi::create([
                'kode_invoice'   => $kodeInvoice,
                'id_pelanggan'   => $pelanggan->id_pelanggan,
                'id_user'        => Auth::id() ?? 1,
                'tgl_masuk'      => Carbon::now(),
                'tgl_selesai'    => $request->tgl_selesai,
                'berat'          => $request->berat,
                'total_biaya'    => $grandTotal,
                'jumlah_bayar'   => $jumlahBayar,
                'status_bayar'   => $request->status_bayar,
                'status_pesanan' => 'diterima',
                'catatan'        => $request->catatan,
            ]);

            // 6. Simpan Detail Utama
            DetailTransaksi::create([
                'id_transaksi'         => $transaksi->id_transaksi,
                'id_layanan'           => $request->layanan_id,
                'jumlah'               => $request->berat,
                'harga_saat_transaksi' => $hargaFinal,
            ]);

            // 7. Simpan Detail Addon
            foreach ($listDetailToSave as $detail) {
                DetailTransaksi::create([
                    'id_transaksi'         => $transaksi->id_transaksi,
                    'id_layanan'           => $detail['id_layanan'],
                    'jumlah'               => $detail['jumlah'],
                    'harga_saat_transaksi' => $detail['harga'],
                ]);
            }

            // 8. Simpan Inventaris
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

            // 9. Simpan Pembayaran
            if ($jumlahBayar > 0) {
                Pembayaran::create([
                    'id_transaksi'   => $transaksi->id_transaksi,
                    'id_user'        => Auth::id() ?? 1,
                    'jlh_pembayaran' => $jumlahBayar,
                    'tgl_bayar'      => Carbon::now(),
                    'keterangan'     => $request->status_bayar == 'lunas' ? 'Lunas Awal' : 'DP Awal',
                ]);
            }

            DB::commit();
            return redirect()->route('admin.transaksi.index')
                             ->with('success', 'Order berhasil! Invoice: ' . $kodeInvoice);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
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
            'siap' => Transaksi::where('status_pesanan', 'siap diambil')->count(),
            'selesai' => Transaksi::where('status_pesanan', 'selesai')->count(),
        ];

        return view('admin.transaksi.status', compact('transaksi', 'counts'));
    }

    public function updateStatus(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update(['status_pesanan' => $request->status]);
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
        $transaksi = Transaksi::with(['pelanggan', 'detailTransaksi.layanan', 'pembayaran', 'inventaris'])->findOrFail($id);
        return view('admin.transaksi.show', compact('transaksi'));
    }

    public function edit($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'pembayaran', 'detailTransaksi.layanan', 'inventaris'])
                        ->findOrFail($id);
        
        $pelanggan = Pelanggan::orderBy('nama', 'asc')->get();
        $layanan = Layanan::all(); 

        // Sorting Kategori (Sama persis kayak Create)
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
    
    public function update(Request $request, string $id) 
    { 
        // Logic update header transaksi kalau perlu
        // Biasanya untuk edit transaksi laundry cukup kompleks (harus hapus detail lama, insert baru)
        // Kalau mau simple, update data pelanggan/status aja dulu.
    }

    public function destroy($id)
    {
        Transaksi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Transaksi Dihapus!');
    }
}