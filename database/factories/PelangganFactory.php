<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PelangganFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'no_hp' => '08' . $this->faker->numerify('##########'),
            'alamat' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
            'member_level' => $this->faker->randomElement(['Member', 'Silver', 'Gold']),
            'poin' => $this->faker->numberBetween(0, 2000),
        ];
    }
}
