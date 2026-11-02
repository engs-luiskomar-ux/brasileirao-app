<?php

namespace Tests\Feature;

use App\Models\Clube;
use App\Models\Gol;
use App\Models\Partida;
use App\Models\Temporada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartidaTest extends TestCase
{
        use RefreshDatabase;
    
        private function partidaComPlacar(int $golsMandante): Partida {
            $partida = Partida::factory()->create(['rodada => 5']);
            Gol::factory()->count($golsMandante)->create(['partida_id' => $partida_id_partida]);

            return $partida;
        }

        
}
