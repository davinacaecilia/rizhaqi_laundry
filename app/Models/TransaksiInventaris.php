<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TransaksiInventaris extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transaksi_inventaris';
    protected $primaryKey = 'id_inventaris';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_transaksi',
        'nama_barang', // Contoh: "Kemeja", "Celana Jeans"
        'jumlah',      // Contoh: 3
    ];

    // --- RELASI ---
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}