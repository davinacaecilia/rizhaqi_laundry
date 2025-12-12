<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('layanan')->truncate();
        Schema::enableForeignKeyConstraints();

        $layanan = [
            // --- 3. CUCI SATUAN ---
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'CUCI SATUAN', 'nama_layanan' => 'Pakaian', 'satuan' => 'pcs', 'harga_satuan' => 15000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'CUCI SATUAN', 'nama_layanan' => 'Jas', 'satuan' => 'pcs', 'harga_satuan' => 20000, 'is_flexible' => 1, 'harga_min' => 20000, 'harga_max' => 35000,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'CUCI SATUAN', 'nama_layanan' => 'Kebaya/Gaun', 'satuan' => 'pcs', 'harga_satuan' => 30000, 'is_flexible' => 1, 'harga_min' => 30000, 'harga_max' => 50000,
            ],

            // --- 4. REGULAR SERVICES ---
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'REGULAR SERVICES', 'nama_layanan' => 'Cuci Kering Setrika - Pakaian', 'satuan' => 'kg', 'harga_satuan' => 10000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'REGULAR SERVICES', 'nama_layanan' => 'CKS - Pakaian Dalam', 'satuan' => 'kg', 'harga_satuan' => 14500, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'REGULAR SERVICES', 'nama_layanan' => 'CKS - Sprei/Selimut/B.Cover', 'satuan' => 'kg', 'harga_satuan' => 14000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'REGULAR SERVICES', 'nama_layanan' => 'CKS - Fitrasi/Gordyn', 'satuan' => 'kg', 'harga_satuan' => 14000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'REGULAR SERVICES', 'nama_layanan' => 'Setrika', 'satuan' => 'kg', 'harga_satuan' => 6000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],

            // --- 5. PACKAGE SERVICES ---
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'PACKAGE SERVICES', 'nama_layanan' => 'Cuci Kering', 'satuan' => 'pcs', 'harga_satuan' => 20000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'PACKAGE SERVICES', 'nama_layanan' => 'Cuci Kering Lipat', 'satuan' => 'pcs', 'harga_satuan' => 25000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'PACKAGE SERVICES', 'nama_layanan' => 'Setrika', 'satuan' => 'pcs', 'harga_satuan' => 250000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],

            // --- 6. KARPET ---
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'KARPET', 'nama_layanan' => 'Karpet Tipis', 'satuan' => 'm2', 'harga_satuan' => 18500, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'KARPET', 'nama_layanan' => 'Karpet Tebal/Berbulu', 'satuan' => 'm2', 'harga_satuan' => 20000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],

            // --- 7. ADD ON (SESUAI BROSUR POJOK KANAN BAWAH) ---
            // Wajib ada di DB, tapi nanti kita hide dari dropdown
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'ADD ON', 'nama_layanan' => 'Ekspress', 'satuan' => 'kg', 'harga_satuan' => 5000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'ADD ON', 'nama_layanan' => 'Hanger', 'satuan' => 'pcs', 'harga_satuan' => 3000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'ADD ON', 'nama_layanan' => 'Plastik', 'satuan' => 'pcs', 'harga_satuan' => 3000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
            [
                'id_layanan' => Str::uuid(), 'kategori' => 'ADD ON', 'nama_layanan' => 'Hanger + Plastik', 'satuan' => 'pcs', 'harga_satuan' => 5000, 'is_flexible' => 0, 'harga_min' => null, 'harga_max' => null,
            ],
        ];

        DB::table('layanan')->insert($layanan);
    }
}