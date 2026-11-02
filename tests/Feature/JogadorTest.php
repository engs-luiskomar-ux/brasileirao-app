<?php

namespace Tests\Feature;

use App\Models\Clube;
use App\Models\Gol;
use App\Jogador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JogadorTest extends TestCase
{
    use RefreshDatabase;


    public function test_pagina_do_jogador_mostra_estatisticas_da_function(): void
    {
        $gol = Gol::factory()->create();
        $jogador = $gol->jogador;

        $this->get("/jogadores/{$jogador->id_jogador}")
            ->assertOk()
            ->assertSee($jogador->nome)
            ->assertSee("fn_estatisticas_jogador({$jogador->id_jogador})")
            ->assertViewHas('estatisticas', fn ($e) => $e->gols === 1);
    }

}
