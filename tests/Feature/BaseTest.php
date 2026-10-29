<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BaseTest extends TestCase
{
    public function test_pagina_inicial_abre(): void
    {
        $this->get('/')->assertOk()->assertSee('Brasileirão DB')->assertSee('em desenvolvimento');
    }

    public function test_verificacao_confirma_schema_e_ssl(): void
    {
        config(['database.connections.pgsql.search_path' => 'nelson_brasileirao_db,public']);
        config(['database.connections.pgsql.sslmode' => 'require']);
        DB::shouldReceive('selectOne')->once()
            ->with('SELECT current_database() AS banco, current_schema() AS schema')
            ->andReturn((object) ['banco' => 'brasileirao_test', 'schema' => 'nelson_brasileirao_db']);

        $this->artisan('db:verificar')
            ->expectsOutput('Conexão com PostgreSQL confirmada (SSL obrigatório).')
            ->assertExitCode(0);
    }

    public function test_verificacao_nao_expoe_credenciais_em_caso_de_falha(): void
    {
        DB::shouldReceive('selectOne')->once()->andThrow(new \RuntimeException('senha-de-teste'));

        $this->artisan('db:verificar')
            ->expectsOutput('Não foi possível conectar ao banco. Confira os dados do .env e o acesso ao Neon.')
            ->doesntExpectOutputToContain('senha-de-teste')
            ->assertExitCode(1);
    }
}
