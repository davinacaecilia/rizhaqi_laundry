<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Wajib Import UUID

class Transaksi extends Model
{
    use HasFactory, HasUuids; // 2. Gunakan Trait UUID

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi'; // PK Custom
    public $incrementing = false; // Matikan Auto Increment
    protected $keyType = 'string'; // Tipe data PK String

    protected $fillable = [
        'kode_invoice',
        'id_pelanggan',
        'id_user',       // Pegawai yang input
        'tgl_masuk',
        'tgl_selesai',
        'berat',
        'total_biaya',   // Grand Total Tagihan
        'jumlah_bayar',  // Total uang yang sudah masuk
        'status_bayar',  // 'belum', 'dp', 'lunas'
        'status_pesanan',// 'diterima', 'dicuci', dll
        'catatan'
    ];

    // --- RELASI (RELATIONSHIPS) ---

    // 1. Ke Pelanggan (Pemilik Cucian)
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    // 2. Ke User (Pegawai Admin/Owner yang input)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // 3. Ke Detail Transaksi (Rincian Biaya & Layanan)
    // Contoh: Layanan Kiloan, Addon Hanger, Addon Plastik
    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // 4. Ke Transaksi Inventaris (Rincian Pakaian) - FITUR BARU
    // Contoh: Kemeja 3 pcs, Kaos 5 pcs
    public function inventaris()
    {
        return $this->hasMany(TransaksiInventaris::class, 'id_transaksi', 'id_transaksi');
    }

    // 5. Ke Riwayat Pembayaran (History Cicilan/DP)
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_transaksi', 'id_transaksi');
    }

    // --- ACCESSOR (PEMANIS TAMPILAN) ---

    // Panggil: $transaksi->total_biaya_format
    public function getTotalBiayaFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya, 0, ',', '.');
    }

    // Panggil: $transaksi->sisa_tagihan
    public function getSisaTagihanAttribute()
    {
        return $this->total_biaya - $this->jumlah_bayar;
    }
}