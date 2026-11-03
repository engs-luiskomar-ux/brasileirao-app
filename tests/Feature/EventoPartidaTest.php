<?php

namespace Tests\Feature;

use App\Jogador;
use App\Models\Cartao;
use App\Models\Gol;
use App\Models\Partida;
use Tests\Support\BancoDoLuisTestCase;

// Thiago - testes de gols, cartões, estatísticas e painel.
class EventoPartidaTest extends BancoDoLuisTestCase
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