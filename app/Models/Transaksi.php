<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Wajib buat UUID

class Transaksi extends Model
{
    use HasFactory, HasUuids; // Aktifkan UUID

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi'; // PK Custom
    public $incrementing = false; // Matikan Auto Increment
    protected $keyType = 'string'; // Tipe data PK String

    protected $guarded = []; // Biar semua kolom bisa diisi (termasuk total_biaya, dll)

    // --- RELASI (HARUS SINKRON SAMA CONTROLLER) ---

    // 1. Ke Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    // 2. Ke User (Pegawai)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // 3. Ke Detail Transaksi 
    // PENTING: Namanya 'detailTransaksi' (bukan details) biar cocok sama Controller with('detailTransaksi')
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // 4. Ke Transaksi Inventaris (Rincian Pakaian)
    public function inventaris()
    {
        return $this->hasMany(TransaksiInventaris::class, 'id_transaksi', 'id_transaksi');
    }

    // 5. Ke Riwayat Pembayaran
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'id_transaksi', 'id_transaksi');
    }

    // --- ACCESSOR (PEMANIS TAMPILAN BLADE) ---

    // Panggil di blade: {{ $transaksi->total_biaya_format }}
    public function getTotalBiayaFormatAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya, 0, ',', '.');
    }

    // Panggil di blade: {{ $transaksi->sisa_tagihan }}
    public function getSisaTagihanAttribute()
    {
        return $this->total_biaya - $this->jumlah_bayar;
    }
}