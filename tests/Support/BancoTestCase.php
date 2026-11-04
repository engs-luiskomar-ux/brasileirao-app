<?php

namespace Tests\Support;

use App\Jogador;
use App\Models\Partida;
use App\Models\Temporada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

abstract class BancoTestCase extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        $conexao = config('database.connections.pgsql');

        if (! $this->app->environment('testing')
            || config('database.default') !== 'pgsql'
            || $conexao['database'] !== 'brasileirao_test'
            || ! in_array($conexao['host'], ['127.0.0.1', 'localhost', '::1'], true)
            || ! empty($conexao['url'])) {
            throw new RuntimeException('Use somente o PostgreSQL local brasileirao_test para executar estes testes.');
        }

        // A primeira migration cria o schema. O histórico precisa ficar no mesmo
        // lugar antes e depois disso, inclusive ao rodar a suíte novamente.
        config(['database.migrations.table' => 'public.migrations']);
    }

    protected function partidaComGol(int $ano = 2010): Partida
    {
        $temporada = Temporada::factory()->create(['ano' => $ano, 'nome' => "Brasileirão {$ano}"]);
        $partida = Partida::factory()->create(['temporada_id' => $temporada->id_temporada]);
        $jogador = Jogador::factory()->create(['clube_id' => $partida->mandante_id, 'posicao' => 'Atacante']);

        DB::table('gol')->insert([
            'minuto' => 30,
            'partida_id' => $partida->id_partida,
            'jogador_id' => $jogador->id_jogador,
            'clube_id' => $partida->mandante_id,
        ]);

        return $partida;
    }
}
