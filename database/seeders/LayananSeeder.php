<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanan = [
            // --- KATEGORI: REGULAR (Harga per Kg) ---
            [
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Cuci Kering Setrika - Pakaian',
                'satuan'        => 'kg',
                'harga_satuan'  => 10000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Cuci Kering Setrika - Pakaian Dalam',
                'satuan'        => 'kg',
                'harga_satuan'  => 14500,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Cuci Kering Setrika - Sprei/Selimut/Bed Cover',
                'satuan'        => 'kg',
                'harga_satuan'  => 14000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Cuci Kering Setrika - Fitrasi/Gordyn',
                'satuan'        => 'kg',
                'harga_satuan'  => 14000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Regular',
                'nama_layanan'  => 'Setrika',
                'satuan'        => 'kg',
                'harga_satuan'  => 6000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],

            // --- KATEGORI: PAKET (Hitungan per Paket/Pcs) ---
            [
                'kategori'      => 'Paket',
                'nama_layanan'  => 'Cuci Kering (6 Kg)',
                'satuan'        => 'kg', 
                'harga_satuan'  => 20000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Paket',
                'nama_layanan'  => 'Cuci Kering Lipat (6 Kg)',
                'satuan'        => 'kg',
                'harga_satuan'  => 25000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Paket',
                'nama_layanan'  => 'Setrika (50 Kg)',
                'satuan'        => 'kg',
                'harga_satuan'  => 250000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],

            // --- KATEGORI: SATUAN (Ada yang Flexible) ---
            [
                'kategori'      => 'Satuan',
                'nama_layanan'  => 'Pakaian',
                'satuan'        => 'pcs',
                'harga_satuan'  => 15000,
                'is_flexible'   => 0, // Harga Tetap
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Satuan',
                'nama_layanan'  => 'Jas',
                'satuan'        => 'pcs',
                'harga_satuan'  => 20000, // Harga dasar (min)
                'is_flexible'   => 1,     // FLEKSIBEL
                'harga_min'     => 20000,
                'harga_max'     => 35000
            ],
            [
                'kategori'      => 'Satuan',
                'nama_layanan'  => 'Kebaya/Gaun',
                'satuan'        => 'pcs',
                'harga_satuan'  => 30000, // Harga dasar (min)
                'is_flexible'   => 1,     // FLEKSIBEL
                'harga_min'     => 30000,
                'harga_max'     => 50000
            ],

            // --- KATEGORI: KARPET (Per Meter Persegi) ---
            [
                'kategori'      => 'Karpet',
                'nama_layanan'  => 'Karpet Tipis',
                'satuan'        => 'm2',
                'harga_satuan'  => 18500,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Karpet',
                'nama_layanan'  => 'Karpet Tebal/Berbulu',
                'satuan'        => 'm2',
                'harga_satuan'  => 20000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],

            // --- KATEGORI: ADD ON (Tambahan) ---
            [
                'kategori'      => 'Add On',
                'nama_layanan'  => 'Ekspress',
                'satuan'        => 'kg',
                'harga_satuan'  => 5000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Add On',
                'nama_layanan'  => 'Hanger',
                'satuan'        => 'pcs',
                'harga_satuan'  => 3000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Add On',
                'nama_layanan'  => 'Plastik',
                'satuan'        => 'pcs',
                'harga_satuan'  => 3000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
            [
                'kategori'      => 'Add On',
                'nama_layanan'  => 'Hanger + Plastik',
                'satuan'        => 'pcs',
                'harga_satuan'  => 5000,
                'is_flexible'   => 0,
                'harga_min'     => null,
                'harga_max'     => null
            ],
        ];

        DB::table('layanan')->insert($layanan);
    }
}
