<?php

namespace Database\Factories;

use App\Jogador;
use App\Models\Partida;
use Illuminate\Database\Eloquent\Factories\Factory;

// Thiago - gera um gol falso para os testes.
// o jogador é do mandante da partida e o clube_id fica nulo para a trigger preencher.
class GolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'minuto' => fake()->numberBetween(1, 90),
            'partida_id' => Partida::factory(),
            'jogador_id' => fn (array $atributos) => Jogador::factory()->create([
                'clube_id' => Partida::find($atributos['partida_id'])->mandante_id,
            ])->id_jogador,
            'clube_id' => null,
            'contra' => false,
        ];
    }
}
