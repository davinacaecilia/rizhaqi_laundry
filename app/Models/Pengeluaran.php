<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    
    protected $table = 'pengeluaran'; 
    protected $primaryKey = 'id_pengeluaran'; 
    public $timestamps = false; // Karena tabel hanya punya kolom tanggal

    protected $fillable = [
        'id_user',
        'keterangan', 
        'jumlah', 
        'tanggal'
    ];
    
    // Relasi dengan Use
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
