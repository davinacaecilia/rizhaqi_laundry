<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Import UUID

class Layanan extends Model
{
    use HasFactory, HasUuids; // 2. Gunakan Trait

    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan'; // PK Custom
    public $incrementing = false; // Matikan Auto Increment
    protected $keyType = 'string'; // Tipe data PK adalah String

    protected $fillable = [
        'kategori',       // 'Regular', 'Paket', 'Satuan', 'Karpet', 'Add On'
        'nama_layanan',
        'satuan',         // 'kg', 'pcs', 'm2'
        'harga_satuan',   // Harga Min/Tetap
        'is_flexible',
        'harga_min',
        'harga_max'  // Harga Max (Nullable)
    ];

    // --- AKSESOR TAMBAHAN (FITUR KEREN) ---
    // Biar di Blade gampang manggil harga format Rupiah.
    // Cara Panggil: {{ $item->harga_format }}
    
    public function getHargaFormatAttribute()
    {
        // Jika ada harga maksimum (Berarti harga rentang)
        if ($this->harga_maksimum) {
            return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.') . ' - ' . number_format($this->harga_maksimum, 0, ',', '.');
        }
        
        // Jika harga tetap
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }
}