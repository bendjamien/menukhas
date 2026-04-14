<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'karyawan',
            'status' => true,
            'jabatan' => 'Staff',
            'remember_token' => Str::random(10),
        ];
    }

    public function kasir(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'kasir',
            'jabatan' => 'Kasir',
        ]);
    }

    public function karyawan(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'karyawan',
            'jabatan' => 'Staff Produksi',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
