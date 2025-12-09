<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Murid>
 */
class MuridFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'kelas' => fake()->randomElement(['X IPA 1', 'X IPA 2', 'XI IPA 1', 'XII IPA 1']),
            'kelas_id' => null,
            'is_active' => true,
            'photo' => null,
            'qr_code_id' => null,
            'user_id' => null,
        ];
    }

    /**
     * Indicate that the murid has a user account.
     */
    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }

    /**
     * Indicate that the murid has a kelas.
     */
    public function withKelas(): static
    {
        return $this->state(fn (array $attributes) => [
            'kelas_id' => Kelas::factory(),
        ]);
    }
}
