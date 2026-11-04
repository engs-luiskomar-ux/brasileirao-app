<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BancoTestCase;

class FiltrosTest extends BancoTestCase
{
    public static function filtrosInvalidos(): array
    {
        return [
            'busca como lista' => ['/api/clubes?busca[]=x', 'busca'],
            'UF como lista' => ['/api/clubes?estado[]=PR', 'estado'],
            'clube com texto' => ['/api/jogadores?clube_id=abc', 'clube_id'],
            'posição como lista' => ['/api/jogadores?posicao[]=Atacante', 'posicao'],
            'temporada como lista' => ['/api/partidas?temporada_id[]=1', 'temporada_id'],
            'clube fora do limite do banco' => ['/api/partidas?clube_id=999999999999', 'clube_id'],
            'rodada inválida' => ['/api/partidas?rodada=39', 'rodada'],
            'partida como lista' => ['/api/gols?partida_id[]=1', 'partida_id'],
            'ano fora do limite' => ['/api/artilharia?ano=99999999999999999', 'ano'],
            'limite inválido' => ['/api/artilharia?limite=0', 'limite'],
            'página como lista' => ['/api/clubes?page[]=1', 'page'],
        ];
    }

    #[DataProvider('filtrosInvalidos')]
    public function test_filtros_invalidos_retornam_422(string $url, string $campo): void
    {
        $this->getJson($url)->assertUnprocessable()->assertJsonValidationErrors($campo);
    }

    public function test_ano_enorme_na_rota_da_classificacao_retorna_404(): void
    {
        $this->getJson('/api/classificacao/99999999999999999999')->assertNotFound();
    }
}
