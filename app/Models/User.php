<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Import Trait

class User extends Authenticatable
{
    use HasFactory, HasUuids; // Pakai Trait

    protected $table = 'users';
    protected $primaryKey = 'id_user'; // Ganti ID default jadi id_user
    public $incrementing = false;      // Matikan auto increment integer
    protected $keyType = 'string';     // Tipe data string (UUID)

    protected $fillable = [
        'id_user', // Masukkan ini agar bisa diisi manual lewat Seeder
        'nama',
        'email',
        'password',
        'role',
    ];

}