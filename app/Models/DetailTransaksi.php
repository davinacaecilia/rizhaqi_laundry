<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DetailTransaksi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'detail_transaksi';
    protected $primaryKey = 'id_detail';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_transaksi',
        'id_layanan',
        'jumlah',               // Qty (kg/pcs)
        'harga_saat_transaksi', // Harga deal saat itu
    ];

    // --- RELASI ---
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }

    // --- ACCESSOR (Hitung Subtotal Otomatis) ---
    // Panggil: $detail->subtotal
    public function getSubtotalAttribute()
    {
        return $this->jumlah * $this->harga_saat_transaksi;
    }

    // Panggil: $detail->subtotal_format (Rp 20.000)
    public function getSubtotalFormatAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}