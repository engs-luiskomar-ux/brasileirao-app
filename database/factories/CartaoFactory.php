<?php

namespace Database\Factories;

use App\Models\Jogador;
use App\Models\Partida;
use Illuminate\Database\Eloquent\Factories\Factory;

// Thiago - gera um cartão falso por exemplo amarelo de um jogador do mandante.
class CartaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'minuto' => fake()->numberBetween(1, 90),
            'tipo' => 'amarelo',
            'partida_id' => Partida::factory(),
            'jogador_id' => fn (array $atributos) => Jogador::factory()->create([
                'clube_id' => Partida::find($atributos['partida_id'])->mandante_id,
            ])->id_jogador,
        ];
    }
}