<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdukFactory extends Factory
{
    public function definition(): array
    {
        $hargaBeli = $this->faker->numberBetween(5000, 50000);
        return [
            'kategori_id' => Kategori::all()->random()->id,
            'nama_produk' => $this->faker->words(2, true),
            'kode_barcode' => $this->faker->unique()->ean13(),
            'harga_beli' => $hargaBeli,
            'harga_jual' => $hargaBeli + $this->faker->numberBetween(2000, 15000),
            'stok' => $this->faker->numberBetween(0, 100),
            'satuan' => $this->faker->randomElement(['Porsi', 'Gelas', 'Pcs', 'Bks']),
            'status' => true,
        ];
    }
}
