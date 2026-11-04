<?php

namespace Tests\Feature;

use App\Models\Clube;
use App\Models\Gol;
use App\Models\Partida;
use App\Models\Temporada;
use Tests\Support\BancoTestCase;

class PartidaTest extends BancoTestCase
{
    private function partidaComPlacar(int $golsMandante): Partida
    {
        $partida = Partida::factory()->create(['rodada' => 5]);
        Gol::factory()->count($golsMandante)->create(['partida_id' => $partida->id_partida]);

        return $partida;
    }

    // WEB
    public function test_lista_de_partidas_mostra_o_placar_calculado_pela_function(): void
    {
        $partida = $this->partidaComPlacar(2);

        $this->get("/partidas?temporada_id={$partida->temporada_id}&rodada=5")
            ->assertOk()
            ->assertSee($partida->mandante->nome)
            ->assertSee($partida->visitante->nome)
            ->assertSee('<b>2</b>×<b>0</b>', false);
    }

    public function test_cadastro_pela_tela_usa_a_procedure_sp_registrar_partida(): void
    {
        $temporada = Temporada::factory()->create(['ano' => 2012]);
        [$mandante, $visitante] = Clube::factory()->count(2)->create();

        $this->post('/partidas', [
            'temporada_id' => $temporada->id_temporada,
            'rodada' => 1,
            'data_partida' => '2012-05-20T16:00',
            'mandante_id' => $mandante->id_clube,
            'visitante_id' => $visitante->id_clube,
        ])->assertRedirect()->assertSessionHas('sucesso');

        $this->assertDatabaseHas('partida', [
            'temporada_id' => $temporada->id_temporada,
            'rodada' => 1,
            'mandante_id' => $mandante->id_clube,
        ]);
    }

    public function test_procedure_impede_clube_com_dois_jogos_na_mesma_rodada(): void
    {
        $existente = Partida::factory()->create(['rodada' => 3]);
        $outro = Clube::factory()->create();

        $this->from('/partidas/create')->post('/partidas', [
            'temporada_id' => $existente->temporada_id,
            'rodada' => 3,
            'data_partida' => $existente->temporada->ano.'-07-01 16:00',
            'mandante_id' => $existente->mandante_id,   // já joga na rodada 3
            'visitante_id' => $outro->id_clube,
        ])
            ->assertRedirect('/partidas/create')
            ->assertSessionHas('erro', "O clube {$existente->mandante->nome} já tem uma partida na rodada 3.");

        $this->assertDatabaseCount('partida', 1);
    }

    // Api

    public function test_patch_do_mandante_nao_pode_igualar_o_visitante_ja_salvo(): void
    {
        $partida = Partida::factory()->create();
        $this->patchJson('/api/partidas/'.$partida->id_partida, ['mandante_id' => $partida->visitante_id])
            ->assertUnprocessable();
        $this->assertDatabaseHas('partida', ['id_partida' => $partida->id_partida, 'mandante_id' => $partida->mandante_id]);
    }

    public function test_clubes_da_partida_com_eventos_nao_podem_ser_trocados(): void
    {
        $partida = $this->partidaComPlacar(1);
        $clube = Clube::factory()->create();
        $this->patchJson('/api/partidas/'.$partida->id_partida, ['visitante_id' => $clube->id_clube])
            ->assertUnprocessable()->assertJsonPath('message', 'Não é possível trocar os clubes de uma partida que já tem gols ou cartões registrados.');
    }

    public function test_edicao_nao_permite_clube_jogar_duas_vezes_na_mesma_rodada(): void
    {
        $existente = Partida::factory()->create(['rodada' => 3]);
        $outra = Partida::factory()->create([
            'temporada_id' => $existente->temporada_id,
            'mandante_id' => $existente->mandante_id,
            'rodada' => 4,
        ]);

        $this->patchJson('/api/partidas/'.$outra->id_partida, ['rodada' => 3])
            ->assertUnprocessable()
            ->assertJsonPath('message', "O clube {$existente->mandante->nome} já tem uma partida na rodada 3.");

        $this->assertDatabaseHas('partida', ['id_partida' => $outra->id_partida, 'rodada' => 4]);
    }

    public function test_edicao_da_propria_partida_nao_e_considerada_duplicada(): void
    {
        $partida = Partida::factory()->create();

        $this->patchJson('/api/partidas/'.$partida->id_partida, ['rodada' => $partida->rodada])
            ->assertOk()->assertJsonPath('data.id', $partida->id_partida);
    }

    public function test_filtro_de_clube_inclui_mandante_e_visitante_sem_trazer_outros_jogos(): void
    {
        $partida = $this->partidaComPlacar(2);
        Partida::factory()->create();

        foreach ([$partida->mandante_id, $partida->visitante_id] as $clubeId) {
            $this->getJson('/api/partidas?clube_id='.$clubeId)
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $partida->id_partida)
                ->assertJsonPath('data.0.placar.mandante', 2)
                ->assertJsonPath('data.0.placar.visitante', 0);
        }
    }

    public function test_formularios_de_partida_abrem(): void
    {
        $partida = Partida::factory()->create();

        $this->get('/partidas/create')->assertOk();
        $this->get('/partidas/'.$partida->id_partida.'/edit')->assertOk();
    }

    public function test_api_mostra_partida_com_placar_em_json(): void
    {
        $partida = $this->partidaComPlacar(3);

        $this->getJson("/api/partidas/{$partida->id_partida}")
            ->assertOk()
            ->assertJsonStructure(['data' => ['id', 'temporada', 'rodada', 'data_partida', 'realizada', 'mandante', 'visitante', 'placar']])
            ->assertJsonPath('data.realizada', true)
            ->assertJsonPath('data.placar.mandante', 3)
            ->assertJsonPath('data.placar.visitante', 0);
    }

    public function test_api_trigger_recusa_data_fora_da_temporada_com_422(): void
    {
        $partida = Partida::factory()->create();
        $anoErrado = $partida->temporada->ano + 3;

        $this->putJson("/api/partidas/{$partida->id_partida}", [
            'temporada_id' => $partida->temporada_id,
            'rodada' => $partida->rodada,
            'data_partida' => "{$anoErrado}-05-01 16:00",
            'mandante_id' => $partida->mandante_id,
            'visitante_id' => $partida->visitante_id,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('message', fn (string $mensagem) => str_contains($mensagem, 'está fora da temporada'));
    }

    public function test_api_cria_e_exclui_partida(): void
    {
        $temporada = Temporada::factory()->create(['ano' => 2011]);
        [$mandante, $visitante] = Clube::factory()->count(2)->create();

        $id = $this->postJson('/api/partidas', [
            'temporada_id' => $temporada->id_temporada,
            'rodada' => 10,
            'data_partida' => '2011-08-10 21:30',
            'mandante_id' => $mandante->id_clube,
            'visitante_id' => $visitante->id_clube,
        ])->assertCreated()->json('data.id');

        $this->deleteJson("/api/partidas/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('partida', ['id_partida' => $id]);
    }
}
