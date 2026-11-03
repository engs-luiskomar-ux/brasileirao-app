<?php

namespace Tests\Feature;

use App\Models\Clube;
use App\Models\Partida;
use App\Models\Temporada;
use Tests\Support\BancoDoLuisTestCase;

class ClubeWebTest extends BancoDoLuisTestCase
{
    public function test_listagem_filtra_por_nome_e_uf(): void
    {
        Clube::factory()->create(['nome' => 'Campo Real FC', 'estado' => 'PR']);
        Clube::factory()->create(['nome' => 'Outro Clube', 'estado' => 'SP']);

        $this->get('/clubes?busca=campo&estado=PR')
            ->assertOk()->assertSee('Campo Real FC')->assertDontSee('Outro Clube');
    }

    public function test_formulario_de_cadastro_abre(): void
    {
        $this->get('/clubes/create')->assertOk()->assertSee('Novo clube')->assertSee('name="nome"', false);
    }

    public function test_cadastro_normaliza_dados_pela_trigger_e_abre_os_detalhes(): void
    {
        $this->post('/clubes', ['nome' => '  Guarapuava   Esporte  ', 'cidade' => ' Guarapuava ', 'estado' => 'pr', 'ano_fundacao' => 1990])
            ->assertRedirect()->assertSessionHas('sucesso');

        $this->assertDatabaseHas('clube', ['nome' => 'Guarapuava Esporte', 'cidade' => 'Guarapuava', 'estado' => 'PR']);
        $clube = Clube::firstOrFail();
        $this->get("/clubes/{$clube->id_clube}")->assertOk()->assertSee('Guarapuava Esporte');
    }

    public function test_trigger_bloqueia_nome_duplicado_sem_diferenciar_maiusculas(): void
    {
        Clube::factory()->create(['nome' => 'Flamengo']);

        $this->from('/clubes/create')->post('/clubes', ['nome' => 'FLAMENGO', 'cidade' => 'Rio', 'estado' => 'RJ'])
            ->assertRedirect('/clubes/create')->assertSessionHas('erro', 'Já existe um clube chamado "FLAMENGO".');
        $this->assertDatabaseCount('clube', 1);
    }

    public function test_edicao_atualiza_clube_e_exclusao_remove_clube_sem_vinculos(): void
    {
        $clube = Clube::factory()->create();
        $this->get("/clubes/{$clube->id_clube}/edit")->assertOk()->assertSee($clube->nome);
        $this->put("/clubes/{$clube->id_clube}", ['nome' => 'Clube Editado', 'cidade' => 'Londrina', 'estado' => 'pr'])
            ->assertRedirect()->assertSessionHas('sucesso');
        $this->assertDatabaseHas('clube', ['id_clube' => $clube->id_clube, 'nome' => 'Clube Editado', 'estado' => 'PR']);
        $this->delete("/clubes/{$clube->id_clube}")->assertRedirect('/clubes')->assertSessionHas('sucesso');
        $this->assertDatabaseMissing('clube', ['id_clube' => $clube->id_clube]);
    }

    public function test_temporada_e_criada_pela_procedure_com_nome_automatico(): void
    {
        $this->get('/temporadas/create')->assertOk();
        $this->post('/temporadas', ['ano' => 2010, 'nome' => ''])->assertRedirect('/temporadas')->assertSessionHas('sucesso');
        $this->assertDatabaseHas('temporada', ['ano' => 2010, 'nome' => 'Brasileirão 2010']);
        $this->get('/temporadas')->assertOk()->assertSee('Brasileirão 2010');
    }

    public function test_temporada_pode_ser_editada_e_excluida_sem_partidas(): void
    {
        $temporada = Temporada::factory()->create();
        $this->get("/temporadas/{$temporada->id_temporada}/edit")->assertOk();
        $this->put("/temporadas/{$temporada->id_temporada}", ['ano' => 2010, 'nome' => 'Temporada Editada'])->assertRedirect('/temporadas');
        $this->assertDatabaseHas('temporada', ['id_temporada' => $temporada->id_temporada, 'nome' => 'Temporada Editada']);
        $this->delete("/temporadas/{$temporada->id_temporada}")->assertRedirect('/temporadas');
        $this->assertDatabaseMissing('temporada', ['id_temporada' => $temporada->id_temporada]);
    }

    public function test_temporada_com_partidas_nao_pode_ser_excluida(): void
    {
        $partida = Partida::factory()->create();
        $this->from('/temporadas')->delete("/temporadas/{$partida->temporada_id}")
            ->assertRedirect('/temporadas')->assertSessionHas('erro');
        $this->assertDatabaseHas('temporada', ['id_temporada' => $partida->temporada_id]);
    }

    public function test_classificacao_web_usa_resultado_da_function(): void
    {
        $partida = $this->partidaComGol();
        $this->get('/classificacao?ano=2010')->assertOk()->assertSee($partida->mandante->nome)
            ->assertViewHas('tabela', fn ($tabela) => $tabela->first()->pontos === 3 && $tabela->last()->pontos === 0);
    }
}
