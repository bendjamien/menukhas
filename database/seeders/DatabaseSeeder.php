<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Produk;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Kategori (Penting karena Produk butuh ini)
        Kategori::factory(5)->create();

        // 2. Seed Produk
        Produk::factory(20)->create();

        // 3. Seed Pelanggan
        Pelanggan::factory(15)->create();

        // 4. Seed User (Karyawan & Kasir)
        User::factory(3)->kasir()->create();
        User::factory(5)->karyawan()->create();

        // 5. User Default Admin (Jika belum ada)
        if (!User::where('role', 'admin')->exists()) {
            User::factory()->create([
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@menukhas.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'jabatan' => 'IT Manager'
            ]);
        }
    }
}
