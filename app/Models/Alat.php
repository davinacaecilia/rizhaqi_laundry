<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Alat extends Model
{
    use HasFactory;
    
    protected $table = 'alat'; 

    protected $primaryKey = 'id_alat'; 

    public $timestamps = false;

    protected $fillable = [
        'nama_alat', 
        'jumlah', 
        'tgl_maintenance_terakhir'
    ];
}
