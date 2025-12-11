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
        // 1. Ambil data transaksi
        // 2. Eager load 'pelanggan' biar query cepat
        // 3. Urutkan dari yang terbaru (tgl_masuk desc)
        $transaksi = Transaksi::with('pelanggan')
            ->orderBy('tgl_masuk', 'desc')
            ->paginate(10); // Pakai pagination biar rapi

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        // Ambil data untuk Form
        $pelanggan = Pelanggan::orderBy('nama', 'asc')->get();
        
        // Urutkan Layanan: Kategori dulu, baru Nama
        $layanan = Layanan::orderBy('kategori', 'asc')
                          ->orderBy('nama_layanan', 'asc')
                          ->get();

        return view('admin.transaksi.create', compact('pelanggan', 'layanan'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'no_hp'          => 'required',
            'id_layanan'     => 'required',
            'berat'          => 'required|numeric|min:0.1',
            'harga_deal'     => 'required|numeric|min:0', // Validasi Harga Deal Manual
            'tgl_selesai'    => 'required|date',
            'status_bayar'   => 'required|in:belum,lunas,dp',
        ]);

        DB::beginTransaction();

        try {
            // 2. Cek/Buat Pelanggan
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

            // 3. Generate Invoice (A0001 - Z6000)
            $lastTrx = Transaksi::latest('created_at')->first();
            if (!$lastTrx) {
                $kodeInvoice = 'A0001';
            } else {
                $lastCode = $lastTrx->kode_invoice;
                $huruf = substr($lastCode, 0, 1);
                $angka = intval(substr($lastCode, 1));

                if ($angka >= 6000) {
                    $huruf++; // A -> B
                    $angka = 1;
                } else {
                    $angka++;
                }
                $kodeInvoice = $huruf . str_pad($angka, 4, '0', STR_PAD_LEFT);
            }

            // 4. Hitung Total Biaya (Server Side)
            // Ambil dari input harga_deal * berat
            $totalBiaya = $request->harga_deal * $request->berat;

            // Tambah biaya Addons
            $listAddons = [];
            if ($request->has('addons')) {
                foreach ($request->addons as $layananId => $dataAddon) {
                    if (isset($dataAddon['checked'])) {
                        $layananDb = Layanan::find($layananId);
                        if ($layananDb) {
                            $qty = $dataAddon['qty'] ?? 1;
                            $subtotalAddon = $layananDb->harga_satuan * $qty;
                            $totalBiaya += $subtotalAddon;

                            $listAddons[] = [
                                'id_layanan' => $layananId,
                                'jumlah'     => $qty,
                                'harga'      => $layananDb->harga_satuan
                            ];
                        }
                    }
                }
            }

            // 5. Hitung Bayar
            $jumlahBayar = 0;
            if ($request->status_bayar == 'lunas') $jumlahBayar = $totalBiaya;
            elseif ($request->status_bayar == 'dp') $jumlahBayar = $request->jumlah_dp;

            // 6. Simpan Header Transaksi
            $transaksi = Transaksi::create([
                'kode_invoice'   => $kodeInvoice,
                'id_pelanggan'   => $pelanggan->id_pelanggan,
                'id_user'        => Auth::id() ?? 1,
                'tgl_masuk'      => Carbon::now(),
                'tgl_selesai'    => $request->tgl_selesai,
                'berat'          => $request->berat,
                'total_biaya'    => $totalBiaya,
                'jumlah_bayar'   => $jumlahBayar,
                'status_bayar'   => $request->status_bayar,
                'status_pesanan' => 'diterima',
                'catatan'        => $request->catatan,
            ]);

            // 7. Simpan Detail Utama (Pakai Harga Deal)
            DetailTransaksi::create([
                'id_transaksi'         => $transaksi->id_transaksi,
                'id_layanan'           => $request->id_layanan,
                'jumlah'               => $request->berat,
                'harga_saat_transaksi' => $request->harga_deal, 
            ]);

            // 8. Simpan Detail Addons
            foreach ($listAddons as $addon) {
                DetailTransaksi::create([
                    'id_transaksi'         => $transaksi->id_transaksi,
                    'id_layanan'           => $addon['id_layanan'],
                    'jumlah'               => $addon['jumlah'],
                    'harga_saat_transaksi' => $addon['harga'],
                ]);
            }

            // 9. Simpan Inventaris
            if ($request->has('inventaris')) {
                foreach ($request->inventaris as $namaBarang => $qty) {
                    if ($qty > 0) {
                        TransaksiInventaris::create([
                            'id_transaksi' => $transaksi->id_transaksi,
                            'nama_barang'  => $namaBarang,
                            'jumlah'       => $qty
                        ]);
                    }
                }
            }

            // 10. Simpan Pembayaran
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
            return redirect()->back()
                             ->with('error', 'Gagal menyimpan: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function status()
    {
        $transaksi = Transaksi::with('pelanggan')->orderBy('id_transaksi', 'desc')->get();

        // 2. Hitung Jumlah per Status (Buat Card diatas)
        $counts = [
            'diterima' => Transaksi::where('status_pesanan', 'diterima')->count(),
            'dicuci' => Transaksi::where('status_pesanan', 'dicuci')->count(),
            'dikeringkan' => Transaksi::where('status_pesanan', 'dikeringkan')->count(),
            'disetrika' => Transaksi::where('status_pesanan', 'disetrika')->count(),
            'packing' => Transaksi::where('status_pesanan', 'packing')->count(),
            'siap' => Transaksi::where('status_pesanan', 'siap')->count(),
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

        // 1. Simpan ke tabel pembayaran
        Pembayaran::create([
            'id_transaksi'   => $trx->id_transaksi,
            'id_user'        => auth()->user()->id_user ?? 1,
            'jlh_pembayaran' => $request->jumlah_bayar,
            'metode_bayar'   => 'Tunai', // Bisa dikembangkan jadi input select
            'tgl_bayar'      => now()
        ]);

        // 2. Cek apakah sudah lunas?
        // Hitung total yg sudah dibayar (termasuk yg barusan)
        $totalSudahBayar = $trx->pembayaran()->sum('jlh_pembayaran');

        if ($totalSudahBayar >= $trx->total_biaya) {
            $trx->update(['status_bayar' => 'lunas']);
        } else {
            $trx->update(['status_bayar' => 'dp']); // Masih nyicil
        }

        return back()->with('success', 'Pembayaran berhasil dicatat!');
    }

    public function show($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return view('admin.transaksi.show', compact('transaksi'));
    }

    public function edit($id)
    {
        // 1. Ambil Transaksi + Relasi (Pelanggan, Pembayaran, Detail)
        $transaksi = Transaksi::with(['pelanggan', 'pembayaran'])->findOrFail($id);
        
        // 2. Ambil Data Master (Buat Dropdown)
        $layanan = Layanan::all(); 
        $pelanggan = Pelanggan::all();
        $detail = DetailTransaksi::all();
        
        return view('admin.transaksi.edit', compact('transaksi', 'pelanggan', 'layanan', 'detail'));
    }
    
    public function update(Request $request, string $id) { }
    public function destroy($id)
    {
        Transaksi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Transaksi Dihapus!');
    }

}