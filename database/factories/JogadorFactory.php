<?php

namespace Database\Factories;

use App\Jogador;
use App\Models\Clube;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jogador>
 */
class JogadorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'posicao' => fake()->randomElement(Jogador::POSICOES),
            'data_nascimento' => fake()->dateTimeBetween('-35 years', '-18 years')->format('Y-m-d'),
            'numero_camisa' => fake()->numberBetween(1, 99),
            'clube_id' => Clube::factory(),
        ];
    }
}
