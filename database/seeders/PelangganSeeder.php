<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pelanggan')->insert([
            'uuid'      => (string) Str::uuid(),
            'nama' => 'Emih',
            'alamat' => 'Jl. Universitas',
            'telepon' => '081269224252',
        ]);
    }
}
