<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Alat extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'alat'; 

    protected $primaryKey = 'id_alat'; 

    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_alat', 
        'jumlah', 
        'tgl_maintenance_terakhir'
    ];
}
