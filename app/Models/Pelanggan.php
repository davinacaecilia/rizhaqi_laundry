<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    protected $primaryKey = 'id_pelanggan';
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'nama',
        'alamat',
        'telepon',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Jika uuid kosong, buatkan uuid baru
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

}