<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->randomElement(['Makanan', 'Minuman', 'Snack', 'Dessert', 'Paket Hemat', 'Kopi', 'Non-Kopi']),
        ];
    }
}
