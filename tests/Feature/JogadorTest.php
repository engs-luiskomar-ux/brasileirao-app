<?php

namespace Tests\Feature;

use App\Jogador;
use App\Models\Clube;
use App\Models\Gol;
use Tests\Support\BancoTestCase;

class JogadorTest extends BancoTestCase
{
    public function test_editar_clube_pelo_crud_tambem_usa_regra_da_transferencia(): void
    {
        $jogador = Jogador::factory()->create(['numero_camisa' => 10]);
        $origemId = $jogador->clube_id;
        $destino = Clube::factory()->create();
        Jogador::factory()->create(['clube_id' => $destino->id_clube, 'numero_camisa' => 10]);

        $this->patchJson('/api/jogadores/'.$jogador->id_jogador, ['clube_id' => $destino->id_clube])
            ->assertOk()->assertJsonPath('data.numero_camisa', null);
        $this->assertDatabaseHas('historico_transferencia', [
            'jogador_id' => $jogador->id_jogador, 'clube_origem_id' => $origemId, 'clube_destino_id' => $destino->id_clube,
        ]);
    }

    public function test_editar_clube_e_camisa_ocupada_recusa_toda_a_alteracao(): void
    {
        $jogador = Jogador::factory()->create();
        $destino = Clube::factory()->create();
        Jogador::factory()->create(['clube_id' => $destino->id_clube, 'numero_camisa' => 7]);
        $this->patchJson('/api/jogadores/'.$jogador->id_jogador, [
            'nome' => 'Nome que não deve ser salvo', 'clube_id' => $destino->id_clube, 'numero_camisa' => 7,
        ])->assertUnprocessable();
        $this->assertDatabaseHas('jogador', ['id_jogador' => $jogador->id_jogador, 'clube_id' => $jogador->clube_id, 'nome' => $jogador->nome]);
        $this->assertDatabaseCount('historico_transferencia', 0);
    }

    public function test_jogador_com_exatamente_quinze_anos_pode_ser_cadastrado(): void
    {
        $clube = Clube::factory()->create();
        $this->postJson('/api/jogadores', [
            'nome' => 'Jogador jovem', 'posicao' => 'Atacante',
            'data_nascimento' => now()->subYears(15)->toDateString(), 'clube_id' => $clube->id_clube,
        ])->assertCreated();
    }

    public function test_crud_da_api_atualiza_camisa_e_exclui_jogador_sem_eventos(): void
    {
        $jogador = Jogador::factory()->create();
        $this->patchJson('/api/jogadores/'.$jogador->id_jogador, ['numero_camisa' => 99])
            ->assertOk()->assertJsonPath('data.numero_camisa', 99);
        $this->deleteJson('/api/jogadores/'.$jogador->id_jogador)->assertNoContent();
        $this->getJson('/api/jogadores/'.$jogador->id_jogador)->assertNotFound();
    }

    public function test_lista_e_formularios_de_jogador_abrem(): void
    {
        $jogador = Jogador::factory()->create();

        $this->get('/jogadores')->assertOk()->assertSee($jogador->nome);
        $this->get('/jogadores/create')->assertOk();
        $this->get('/jogadores/'.$jogador->id_jogador.'/edit')->assertOk();
    }

    public function test_api_transferencia_mostra_nomes_dos_clubes_no_historico(): void
    {
        $jogador = Jogador::factory()->create();
        $origem = $jogador->clube;
        $destino = Clube::factory()->create();

        $this->postJson(route('api.jogadores.transferir', $jogador), ['clube_destino_id' => $destino->id_clube])
            ->assertOk()
            ->assertJsonPath('data.clube.id', $destino->id_clube)
            ->assertJsonPath('historico.0.de', $origem->nome)
            ->assertJsonPath('historico.0.para', $destino->nome);

        $this->get('/jogadores/'.$jogador->id_jogador)->assertOk()->assertSee($origem->nome)->assertSee($destino->nome);
    }

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

        $this->post(route('jogadores.transferir', $jogador), ['clube_destino_id' => $destino->id_clube])
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
