<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Import UUID
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'Log';
    protected $primaryKey = 'id_log'; // PK Custom
    public $incrementing = false; // Matikan Auto Increment
    protected $keyType = 'string';

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
