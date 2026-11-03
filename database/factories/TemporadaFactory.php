<?php

namespace Database\Factories;

use App\Models\Temporada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Temporada>
 */
class TemporadaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ano = fake()->unique()->numberBetween(1971, 2013);

        return [
            'ano' => $ano,
            'nome' => "Brasileirão {$ano}",
        ];
    }
}
