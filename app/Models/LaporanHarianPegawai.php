<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanHarianPegawai extends Model
{
    use HasFactory;

    // Nama tabel di database (jika tidak mengikuti konvensi jamak: laporan_harian_pegawai)
    protected $table = 'laporan_harian_pegawai';

    // Kolom-kolom yang boleh diisi (opsional, tergantung kebutuhan)
    protected $fillable = [
        'id_transaksi',
        'id_user',
        'tgl_dikerjakan',
        // tambahkan kolom lain
    ];

    // Definisikan hubungan ke tabel User/Pegawai
    public function pegawai()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Definisikan hubungan ke tabel Transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
    }
}
