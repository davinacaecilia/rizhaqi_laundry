<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // 1. Wajib import Str untuk UUID

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun ADMIN
        User::create([
            'id_user'  => Str::uuid(), // Generate UUID
            'nama'     => 'Admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // 2. Akun OWNER
        User::create([
            'id_user'  => Str::uuid(),
            'nama'     => 'Owner',
            'email'    => 'owner@gmail.com',
            'password' => Hash::make('owner123'),
            'role'     => 'owner',
        ]);

        // 3. Akun PEGAWAI
        User::create([
            'id_user'  => Str::uuid(),
            'nama'     => 'Pegawai',
            'email'    => 'pegawai@gmail.com',
            'password' => Hash::make('pegawai123'),
            'role'     => 'pegawai',
        ]);
    }
}