<?php

namespace Tests\Feature;

use App\Jogador;
use App\Models\Clube;
use App\Models\Partida;
use App\Models\Temporada;
use Tests\Support\BancoDoLuisTestCase;

class ClubeApiTest extends BancoDoLuisTestCase
{
    public function test_cadastro_retorna_201_e_resource_com_dados_normalizados(): void
    {
        $this->postJson('/api/clubes', ['nome' => '  Clube   da API ', 'cidade' => ' Curitiba ', 'estado' => 'pr', 'ano_fundacao' => 2000])
            ->assertCreated()->assertJsonStructure(['data' => ['id', 'nome', 'cidade', 'estado', 'fundacao', 'links' => ['self']]])
            ->assertJsonPath('data.nome', 'Clube da API')->assertJsonPath('data.estado', 'PR')->assertJsonPath('data.fundacao', 2000);
    }

    public function test_validacao_retorna_422_com_erros_por_campo(): void
    {
        $this->postJson('/api/clubes', ['estado' => 'XYZ'])->assertUnprocessable()
            ->assertJsonValidationErrors(['nome', 'cidade', 'estado']);
    }

    public function test_listagem_filtra_por_prefixo_e_uf_e_retorna_elenco_e_paginacao(): void
    {
        $clube = Clube::factory()->create(['nome' => 'Campo Real FC', 'estado' => 'PR']);
        Clube::factory()->create(['nome' => 'Campo de Fora', 'estado' => 'SP']);
        Jogador::factory()->create(['clube_id' => $clube->id_clube]);

        $this->getJson('/api/clubes?busca=CAMPO&estado=pr')->assertOk()
            ->assertJsonPath('meta.total', 1)->assertJsonPath('data.0.id', $clube->id_clube)
            ->assertJsonPath('data.0.total_jogadores', 1)->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_consulta_retorna_resource_com_link_e_404_quando_nao_existe(): void
    {
        $clube = Clube::factory()->create();
        $this->getJson("/api/clubes/{$clube->id_clube}")->assertOk()->assertJsonPath('data.id', $clube->id_clube)
            ->assertJsonPath('data.links.self', route('api.clubes.show', $clube))->assertJsonPath('data.total_jogadores', 0);
        $this->get('/api/clubes/999999', ['Accept' => 'text/html'])->assertNotFound()->assertHeader('Content-Type', 'application/json');
    }

    public function test_put_atualiza_e_patch_permite_alterar_somente_a_cidade(): void
    {
        $clube = Clube::factory()->create(['nome' => 'Nome Original']);
        $this->putJson("/api/clubes/{$clube->id_clube}", ['nome' => 'Nome Original', 'cidade' => 'Curitiba', 'estado' => 'pr'])
            ->assertOk()->assertJsonPath('data.estado', 'PR');
        $this->patchJson("/api/clubes/{$clube->id_clube}", ['cidade' => '  Ponta   Grossa  '])->assertOk()
            ->assertJsonPath('data.cidade', 'Ponta Grossa')->assertJsonPath('data.nome', 'Nome Original');
    }

    public function test_trigger_retorna_422_para_nome_duplicado_ignorando_maiusculas(): void
    {
        Clube::factory()->create(['nome' => 'Flamengo']);
        $this->postJson('/api/clubes', ['nome' => 'FLAMENGO', 'cidade' => 'Rio', 'estado' => 'RJ'])
            ->assertUnprocessable()->assertJsonPath('message', 'Já existe um clube chamado "FLAMENGO".');
        $this->assertDatabaseCount('clube', 1);
    }

    public function test_exclusao_com_jogadores_retorna_409_e_sem_vinculos_retorna_204(): void
    {
        $clube = Clube::factory()->create();
        Jogador::factory()->create(['clube_id' => $clube->id_clube]);
        $this->deleteJson("/api/clubes/{$clube->id_clube}")->assertConflict();
        $this->assertDatabaseHas('clube', ['id_clube' => $clube->id_clube]);
        $vazio = Clube::factory()->create();
        $this->deleteJson("/api/clubes/{$vazio->id_clube}")->assertNoContent();
        $this->assertDatabaseMissing('clube', ['id_clube' => $vazio->id_clube]);
    }

    public function test_classificacao_retorna_pontos_calculados_pela_function(): void
    {
        $partida = $this->partidaComGol();
        $this->getJson('/api/classificacao/2010')->assertOk()->assertJsonPath('ano', 2010)
            ->assertJsonPath('data.0.clube_id', $partida->mandante_id)->assertJsonPath('data.0.pontos', 3)
            ->assertJsonPath('data.1.pontos', 0);
    }

    public function test_classificacao_sem_partidas_e_ano_nao_numerico_retornam_404(): void
    {
        $this->getJson('/api/classificacao/2010')->assertNotFound()->assertJsonStructure(['message']);
        $this->get('/api/classificacao/abc', ['Accept' => 'text/html'])->assertNotFound()->assertHeader('Content-Type', 'application/json');
    }

    public function test_partida_futura_nao_entra_na_classificacao(): void
    {
        $ano = now()->year + 1;
        $temporada = Temporada::factory()->create(['ano' => $ano, 'nome' => "Brasileirão {$ano}"]);
        Partida::factory()->create(['temporada_id' => $temporada->id_temporada]);
        $this->getJson("/api/classificacao/{$ano}")->assertNotFound();
    }
}
