<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Wajib Import Ini

class Pelanggan extends Model
{
    use HasFactory, HasUuids; // 2. Pasang Trait HasUuids

    // 3. Definisi Nama Tabel (Opsional jika sesuai konvensi, tapi kita tulis biar aman)
    protected $table = 'pelanggan';

    // 4. Definisi Primary Key (WAJIB KARENA GANTI JADI id_pelanggan)
    protected $primaryKey = 'id_pelanggan';

    // 5. Matikan Auto Increment (WAJIB UNTUK UUID)
    public $incrementing = false;

    // 6. Set Tipe Data Key (WAJIB UNTUK UUID)
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'telepon',
        'alamat'
    ];
}