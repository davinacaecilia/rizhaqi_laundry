<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $primaryKey = 'id_layanan';
    public $timestamps = false;

    protected $fillable = [
        'kategori',
        'nama_layanan',
        'satuan',
        'harga_satuan',        // Ini akan jadi harga default/dasar
        'is_flexible',  // Tambahan baru
        'harga_min',    // Tambahan baru
        'harga_max',    // Tambahan baru
    ];

}