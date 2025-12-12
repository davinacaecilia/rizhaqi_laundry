<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'Log';

    protected $fillable = [
        'id_user',
        'aksi',
        'keterangan',
        'waktu'
    ];

    public $timestamps = false; // karena tabel kamu pakai 'waktu' bukan created_at/updated_at

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
