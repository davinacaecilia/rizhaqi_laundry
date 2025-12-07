<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin', // ROLE: admin
        ]);

        User::create([
            'nama' => 'Owner',
            'email' => 'owner@gmail.com',
            'password' => Hash::make('owner123'),
            'role' => 'owner', // ROLE: owner
        ]);

        User::create([
            'nama' => 'Pegawai',
            'email' => 'pegawai@gmail.com',
            'password' => Hash::make('pegawai123'),
            'role' => 'pegawai', // ROLE: pegawai
        ]);

        User::create([
            'nama' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('user123'),
            'role' => 'user', // ROLE: user
        ]);
    }
}
