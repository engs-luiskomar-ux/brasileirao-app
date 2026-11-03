<?php

namespace Database\Factories;

use App\Models\Clube;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clube>
 */
class ClubeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => 'EC '.fake()->unique()->numerify('Teste ####'),
            'cidade' => fake()->city(),
            'estado' => fake()->randomElement(['PR', 'SP', 'RJ', 'MG', 'RS', 'SC', 'BA']),
            'ano_fundacao' => fake()->numberBetween(1900, 2000),
        ];
    }
}
