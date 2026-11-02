<?php

namespace Database\Factories;

use App\Models\Clube;
use App\Models\Temporada;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Gera partidas falsas para os testes.
 * A data é sempre dentro do ano da temporada (senão a trigger
 * trg_partida_valida recusa o INSERT).
 *
 * @extends Factory<\App\Models\Partida>
 */
class PartidaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rodada' => fake()->numberBetween(1, 38),
            'temporada_id' => Temporada::factory(),
            'mandante_id' => Clube::factory(),
            'visitante_id' => Clube::factory(),
            'data_partida' => fn (array $atributos) => Temporada::find($atributos['temporada_id'])->ano.'-06-15 16:00:00',
        ];
    }
}
