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
    public function test_transferencia_pela_tela_chama_procedure_e_trigger_grava_historico(): void
    {
        $jogador = Jogador::factory()->create(['numero_camisa' => 10]);
        $destino = Clube::factory()->create();
        $origemId = $jogador->clube_id;

        $this->post("/jogadores/{$jogador->id_jogador}/transferir", ['clube_destino_id' => $destino->id_clube])
            ->assertRedirect("/jogadores/{$jogador->id_jogador}")
            ->assertSessionHas('sucesso');

        $this->assertDatabaseHas('jogador', [
            'id_jogador' => $jogador->id_jogador,
            'clube_id' => $destino->id_clube,
            'numero_camisa' => 10, 
        ]);

        $this->assertDatabaseHas('historico_transferencia', [
            'jogador_id' => $jogador->id_jogador,
            'clube_origem_id' => $origemId,
            'clube_destino_id' => $destino->id_clube,
        ]);
    }
    public function test_api_cria_jogador_e_retorna_201(): void
    {
        $clube = Clube::factory()->create();

        $this->postJson('/api/jogadores', [
            'nome' => 'Jogador da API',
            'posicao' => 'Atacante',
            'data_nascimento' => '2000-01-15',
            'numero_camisa' => 9,
            'clube_id' => $clube->id_clube,
        ])
            ->assertCreated()
            ->assertJsonPath('data.nome', 'Jogador da API')
            ->assertJsonPath('data.clube.id', $clube->id_clube);
    }
    public function test_api_procedure_bloqueia_transferencia_para_o_mesmo_clube(): void
    {
        $jogador = Jogador::factory()->create();

        $this->postJson("/api/jogadores/{$jogador->id_jogador}/transferencias", ['clube_destino_id' => $jogador->clube_id])
            ->assertUnprocessable()
            ->assertJsonPath('message', "{$jogador->nome} já pertence a esse clube.");
    }
    public function test_api_show_traz_estatisticas(): void
    {
        $jogador = Jogador::factory()->create();

        $this->getJson("/api/jogadores/{$jogador->id_jogador}")
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'nome', 'posicao', 'clube'],
                'estatisticas' => ['gols', 'amarelos', 'vermelhos', 'temporadas_com_gol', 'idade'],
            ]);
    }
    public function test_api_procedure_bloqueia_camisa_ocupada_no_destino(): void
    {
        $destino = Clube::factory()->create();
        Jogador::factory()->create(['clube_id' => $destino->id_clube, 'numero_camisa' => 7]);
        $jogador = Jogador::factory()->create();

        $this->postJson("/api/jogadores/{$jogador->id_jogador}/transferencias", [
            'clube_destino_id' => $destino->id_clube,
            'numero_camisa' => 7,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'A camisa 7 já está em uso no clube de destino.');

        $this->assertDatabaseCount('historico_transferencia', 0);
    }

}
