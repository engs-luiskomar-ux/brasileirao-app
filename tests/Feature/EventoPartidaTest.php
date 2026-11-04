<?php

namespace Tests\Feature;

use App\Jogador;
use App\Models\Cartao;
use App\Models\Clube;
use App\Models\Gol;
use App\Models\Partida;
use App\Models\Temporada;
use Tests\Support\BancoTestCase;

// Thiago - testes de gols, cartões, estatísticas e painel.
class EventoPartidaTest extends BancoTestCase
{
    private Partida $partida;

    private Jogador $jogadorMandante;

    protected function setUp(): void
    {
        parent::setUp();

        $this->partida = Partida::factory()->create();
        $this->jogadorMandante = Jogador::factory()->create(['clube_id' => $this->partida->mandante_id]);
    }

    // WEB

    public function test_trocar_jogador_de_gol_antigo_exige_informar_o_tipo(): void
    {
        $gol = Gol::factory()->create([
            'partida_id' => $this->partida->id_partida, 'jogador_id' => $this->jogadorMandante->id_jogador, 'contra' => null,
        ]);
        $visitante = Jogador::factory()->create(['clube_id' => $this->partida->visitante_id]);
        $this->patchJson('/api/gols/'.$gol->id_gol, ['jogador_id' => $visitante->id_jogador])
            ->assertUnprocessable()->assertJsonValidationErrors('contra');
        $this->patchJson('/api/gols/'.$gol->id_gol, ['jogador_id' => $visitante->id_jogador, 'contra' => false])
            ->assertOk()->assertJsonPath('data.clube_creditado.id', $this->partida->visitante_id);
    }

    public function test_editar_gol_contra_sem_repetir_tipo_preserva_o_placar(): void
    {
        $gol = Gol::factory()->create([
            'partida_id' => $this->partida->id_partida,
            'jogador_id' => $this->jogadorMandante->id_jogador, 'contra' => true,
        ]);
        $this->patchJson('/api/gols/'.$gol->id_gol, ['jogador_id' => $gol->jogador_id, 'minuto' => 40])
            ->assertOk()->assertJsonPath('data.contra', true)
            ->assertJsonPath('data.clube_creditado.id', $this->partida->visitante_id);
    }

    public function test_gol_contra_conta_no_placar_mas_nao_na_artilharia_ou_gols_do_jogador(): void
    {
        $this->postJson('/api/gols', [
            'partida_id' => $this->partida->id_partida, 'jogador_id' => $this->jogadorMandante->id_jogador,
            'minuto' => 10, 'contra' => true,
        ])->assertCreated();
        $this->getJson('/api/partidas/'.$this->partida->id_partida)->assertJsonPath('data.placar.visitante', 1);
        $this->getJson('/api/artilharia')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/jogadores/'.$this->jogadorMandante->id_jogador)
            ->assertOk()->assertJsonPath('estatisticas.gols', 0)->assertJsonPath('estatisticas.temporadas_com_gol', 0);
    }

    public function test_editar_minuto_do_gol_apos_transferencia_preserva_clube_historico(): void
    {
        $gol = Gol::factory()->create(['partida_id' => $this->partida->id_partida, 'jogador_id' => $this->jogadorMandante->id_jogador]);
        $destino = Clube::factory()->create();
        $this->postJson(route('api.jogadores.transferir', $this->jogadorMandante), ['clube_destino_id' => $destino->id_clube])->assertOk();
        $this->patchJson('/api/gols/'.$gol->id_gol, ['minuto' => 35])
            ->assertOk()->assertJsonPath('data.clube_creditado.id', $this->partida->mandante_id);
    }

    public function test_mudar_partida_do_gol_e_recusado_em_vez_de_ignorar_o_campo(): void
    {
        $gol = Gol::factory()->create(['partida_id' => $this->partida->id_partida, 'jogador_id' => $this->jogadorMandante->id_jogador]);
        $outra = Partida::factory()->create();
        $this->patchJson('/api/gols/'.$gol->id_gol, ['partida_id' => $outra->id_partida])
            ->assertUnprocessable()->assertJsonValidationErrors('partida_id');
    }

    public function test_jogador_de_fora_nao_pode_marcar_gol_normal_ou_contra(): void
    {
        $jogador = Jogador::factory()->create();
        foreach ([false, true] as $contra) {
            $this->postJson('/api/gols', [
                'partida_id' => $this->partida->id_partida, 'jogador_id' => $jogador->id_jogador,
                'minuto' => 30, 'contra' => $contra,
            ])->assertUnprocessable();
        }
        $this->assertDatabaseCount('gol', 0);
    }

    public function test_remover_um_amarelo_cancela_apenas_a_expulsao_automatica(): void
    {
        $url = '/api/partidas/'.$this->partida->id_partida.'/cartoes';
        $dados = ['jogador_id' => $this->jogadorMandante->id_jogador, 'tipo' => 'amarelo'];
        $this->postJson($url, $dados + ['minuto' => 20])->assertCreated();
        $this->postJson($url, $dados + ['minuto' => 60])->assertCreated();
        $amarelo = Cartao::where('tipo', 'amarelo')->firstOrFail();
        $this->delete(route('cartoes.destroy', $amarelo))->assertSessionHas('sucesso');
        $this->assertDatabaseCount('cartao', 1);
        $this->assertSame('amarelo', Cartao::firstOrFail()->tipo);
    }

    public function test_vermelho_automatico_nao_pode_ser_removido_sozinho(): void
    {
        $url = '/api/partidas/'.$this->partida->id_partida.'/cartoes';
        $dados = ['jogador_id' => $this->jogadorMandante->id_jogador, 'tipo' => 'amarelo'];
        $this->postJson($url, $dados + ['minuto' => 20])->assertCreated();
        $this->postJson($url, $dados + ['minuto' => 60])->assertCreated();
        $vermelho = Cartao::where('tipo', 'vermelho')->firstOrFail();
        $this->delete(route('cartoes.destroy', $vermelho))->assertSessionHas('erro');
        $this->assertDatabaseCount('cartao', 3);
    }

    public function test_amarelos_fora_de_ordem_geram_expulsao_no_minuto_do_segundo_cronologico(): void
    {
        $url = '/api/partidas/'.$this->partida->id_partida.'/cartoes';
        $dados = ['jogador_id' => $this->jogadorMandante->id_jogador, 'tipo' => 'amarelo'];
        $this->postJson($url, $dados + ['minuto' => 70])->assertCreated();
        $this->postJson($url, $dados + ['minuto' => 20])->assertCreated();
        $this->assertDatabaseHas('cartao', ['tipo' => 'vermelho', 'minuto' => 70, 'automatico' => true]);
    }

    public function test_expulsao_retroativa_nao_pode_invalidar_gol_ja_salvo(): void
    {
        Gol::factory()->create(['partida_id' => $this->partida->id_partida, 'jogador_id' => $this->jogadorMandante->id_jogador, 'minuto' => 80]);
        $url = '/api/partidas/'.$this->partida->id_partida.'/cartoes';
        $dados = ['jogador_id' => $this->jogadorMandante->id_jogador];
        $this->postJson($url, $dados + ['minuto' => 50, 'tipo' => 'vermelho'])->assertUnprocessable();
        $this->postJson($url, $dados + ['minuto' => 20, 'tipo' => 'amarelo'])->assertCreated();
        $this->postJson($url, $dados + ['minuto' => 60, 'tipo' => 'amarelo'])->assertUnprocessable();
        $this->assertDatabaseCount('cartao', 1);
    }

    public function test_vermelho_direto_nao_e_apagado_ao_remover_um_amarelo(): void
    {
        $url = '/api/partidas/'.$this->partida->id_partida.'/cartoes';
        $dados = ['jogador_id' => $this->jogadorMandante->id_jogador];
        $this->postJson($url, $dados + ['minuto' => 20, 'tipo' => 'amarelo'])->assertCreated();
        $this->postJson($url, $dados + ['minuto' => 60, 'tipo' => 'vermelho'])->assertCreated();
        $this->delete(route('cartoes.destroy', Cartao::where('tipo', 'amarelo')->firstOrFail()))->assertSessionHas('sucesso');
        $this->assertDatabaseHas('cartao', ['tipo' => 'vermelho', 'automatico' => false]);
    }

    public function test_media_de_gols_nao_e_reduzida_por_partida_futura(): void
    {
        Gol::factory()->count(2)->create(['partida_id' => $this->partida->id_partida, 'jogador_id' => $this->jogadorMandante->id_jogador]);
        $temporada = Temporada::factory()->create(['ano' => now()->year + 1]);
        Partida::factory()->create(['temporada_id' => $temporada->id_temporada, 'data_partida' => now()->addYear()->format('Y-m-d H:i:s')]);
        $this->get('/')->assertOk()->assertViewHas('totais', fn ($totais) => $totais['media_gols'] == 2);
    }

    public function test_limite_vazio_da_artilharia_usa_o_padrao(): void
    {
        Gol::factory()->create(['partida_id' => $this->partida->id_partida, 'jogador_id' => $this->jogadorMandante->id_jogador]);
        $this->getJson('/api/artilharia?limite=')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_trigger_define_o_clube_do_gol_pelo_jogador(): void
    {
        $this->post("/partidas/{$this->partida->id_partida}/gols", [
            'jogador_id' => $this->jogadorMandante->id_jogador,
            'minuto' => 30,
        ])->assertRedirect()->assertSessionHas('sucesso');

        $this->assertDatabaseHas('gol', [
            'partida_id' => $this->partida->id_partida,
            'jogador_id' => $this->jogadorMandante->id_jogador,
            'clube_id' => $this->partida->mandante_id,
        ]);
    }

    public function test_gol_contra_e_creditado_ao_adversario(): void
    {
        $this->post("/partidas/{$this->partida->id_partida}/gols", [
            'jogador_id' => $this->jogadorMandante->id_jogador,
            'minuto' => 44,
            'contra' => '1',
        ])->assertSessionHas('sucesso');

        $this->assertDatabaseHas('gol', ['minuto' => 44, 'clube_id' => $this->partida->visitante_id]);

        $this->get("/partidas/{$this->partida->id_partida}")
            ->assertOk()
            ->assertSee('0 × 1');
    }

    public function test_procedure_transforma_segundo_amarelo_em_vermelho(): void
    {
        $url = "/partidas/{$this->partida->id_partida}/cartoes";
        $dados = ['jogador_id' => $this->jogadorMandante->id_jogador, 'tipo' => 'amarelo'];

        $this->post($url, $dados + ['minuto' => 20])->assertSessionHas('sucesso', 'Cartão amarelo registrado.');
        $this->post($url, $dados + ['minuto' => 60])
            ->assertSessionHas('sucesso', 'Segundo amarelo: cartão vermelho aplicado automaticamente (jogador expulso).');

        $this->assertSame(1, Cartao::where('tipo', 'vermelho')->count());

        // jogador expulso não recebe outro cartão
        $this->post($url, $dados + ['minuto' => 70])
            ->assertSessionHas('erro', 'O jogador já foi expulso nesta partida.');
    }

    public function test_dashboard_e_estatisticas_abrem(): void
    {
        Gol::factory()->create(['partida_id' => $this->partida->id_partida, 'jogador_id' => $this->jogadorMandante->id_jogador]);

        $this->get('/')->assertOk()->assertSee($this->jogadorMandante->nome);
        $this->get('/estatisticas/artilharia')->assertOk()->assertSee($this->jogadorMandante->nome);
        $this->get('/estatisticas/cartoes')->assertOk();
    }

    // API

    public function test_api_trigger_bloqueia_gol_de_jogador_expulso(): void
    {
        Cartao::factory()->create([
            'partida_id' => $this->partida->id_partida,
            'jogador_id' => $this->jogadorMandante->id_jogador,
            'tipo' => 'vermelho',
            'minuto' => 50,
        ]);

        $this->postJson('/api/gols', [
            'partida_id' => $this->partida->id_partida,
            'jogador_id' => $this->jogadorMandante->id_jogador,
            'minuto' => 80,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', 'O jogador foi expulso antes do minuto 80 e não pode ter marcado esse gol.');
    }

    public function test_api_crud_de_gols(): void
    {
        $id = $this->postJson('/api/gols', [
            'partida_id' => $this->partida->id_partida,
            'jogador_id' => $this->jogadorMandante->id_jogador,
            'minuto' => 10,
        ])
            ->assertCreated()
            ->assertJsonPath('data.clube_creditado.id', $this->partida->mandante_id)
            ->json('data.id');

        $this->getJson("/api/gols?partida_id={$this->partida->id_partida}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'partida_id', 'minuto', 'jogador', 'clube_creditado']], 'links', 'meta']);

        $this->patchJson("/api/gols/{$id}", ['minuto' => 15])->assertOk()->assertJsonPath('data.minuto', 15);
        $this->deleteJson("/api/gols/{$id}")->assertNoContent();
        $this->getJson("/api/gols/{$id}")->assertNotFound();
    }

    public function test_api_artilharia_usa_fn_artilharia(): void
    {
        Gol::factory()->count(2)->create([
            'partida_id' => $this->partida->id_partida,
            'jogador_id' => $this->jogadorMandante->id_jogador,
        ]);

        $this->getJson('/api/artilharia?ano='.$this->partida->temporada->ano)
            ->assertOk()
            ->assertJsonPath('data.0.jogador', $this->jogadorMandante->nome)
            ->assertJsonPath('data.0.gols', 2);
    }

    public function test_api_cartao_retorna_201_com_mensagem_da_procedure(): void
    {
        $this->postJson("/api/partidas/{$this->partida->id_partida}/cartoes", [
            'jogador_id' => $this->jogadorMandante->id_jogador,
            'minuto' => 33,
            'tipo' => 'vermelho',
        ])
            ->assertCreated()
            ->assertJsonPath('message', 'Cartão vermelho registrado.')
            ->assertJsonCount(1, 'cartoes_do_jogador');
    }
}
