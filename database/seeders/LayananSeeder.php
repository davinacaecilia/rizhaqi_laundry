<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Import Str untuk UUID
use Carbon\Carbon; // Import Carbon untuk Waktu

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $layanan = [
            // --- KATEGORI: REGULAR (Kiloan) ---
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Cuci Kering Setrika - Pakaian',
                'satuan'        => 'kg',
                'harga_satuan'  => 10000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Cuci Kering Setrika - Pakaian Dalam',
                'satuan'        => 'kg',
                'harga_satuan'  => 14500,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Cuci Kering Setrika - Sprei/Selimut/Bed Cover',
                'satuan'        => 'kg',
                'harga_satuan'  => 14000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Cuci Kering Setrika - Fitrasi/Gordyn',
                'satuan'        => 'kg',
                'harga_satuan'  => 14000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Setrika Saja',
                'satuan'        => 'kg',
                'harga_satuan'  => 8000, // Disesuaikan dengan brosur 8.000
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // --- KATEGORI: PAKET (Borongan) ---
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Paket',
                'nama_layanan'  => 'Cuci Kering (Tanpa Setrika)',
                'satuan'        => 'kg',
                'harga_satuan'  => 20000, // Harga paket misal per load/ketentuan khusus
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Paket',
                'nama_layanan'  => 'Cuci Kering Lipat',
                'satuan'        => 'kg',
                'harga_satuan'  => 25000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            
            // --- KATEGORI: SATUAN (Ada Harga Rentang) ---
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Satuan',
                'nama_layanan'  => 'Pakaian Satuan (Kemeja/Celana)',
                'satuan'        => 'pcs',
                'harga_satuan'  => 15000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Satuan',
                'nama_layanan'  => 'Jas',
                'satuan'        => 'pcs',
                'harga_satuan'  => 20000, // Harga Min
                'harga_maksimum'=> 35000, // Harga Max (Rentang)
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Satuan',
                'nama_layanan'  => 'Kebaya/Gaun',
                'satuan'        => 'pcs',
                'harga_satuan'  => 30000, // Harga Min
                'harga_maksimum'=> 50000, // Harga Max (Rentang)
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // --- KATEGORI: KARPET ---
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Karpet',
                'nama_layanan'  => 'Karpet Tipis',
                'satuan'        => 'm2',
                'harga_satuan'  => 18500,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Karpet',
                'nama_layanan'  => 'Karpet Tebal/Berbulu',
                'satuan'        => 'm2',
                'harga_satuan'  => 20000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // --- KATEGORI: ADD ON ---
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Add On',
                'nama_layanan'  => 'Layanan Ekspress',
                'satuan'        => 'kg',
                'harga_satuan'  => 5000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Add On',
                'nama_layanan'  => 'Hanger',
                'satuan'        => 'pcs',
                'harga_satuan'  => 3000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Add On',
                'nama_layanan'  => 'Plastik Packing',
                'satuan'        => 'pcs',
                'harga_satuan'  => 3000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id_layanan'    => Str::uuid(),
                'kategori'      => 'Add On',
                'nama_layanan'  => 'Hanger + Plastik',
                'satuan'        => 'pcs',
                'harga_satuan'  => 5000,
                'harga_maksimum'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        DB::table('layanan')->insert($layanan);
    }
}