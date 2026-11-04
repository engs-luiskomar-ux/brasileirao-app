<?php

namespace App\Services;

use App\Jogador;
use App\Models\Cartao;
use App\Models\Clube;
use App\Models\Gol;
use App\Models\Partida;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

// Thiago - estatísticas e números do painel inicial
class EstatisticaService
{
    // chama a function fn_artilharia de todas as temporadas
    public function artilharia(?int $ano = null, int $limite = 20): Collection
    {
        return collect(DB::select('SELECT * FROM fn_artilharia(?, ?)', [$ano, $limite]));
    }

    // ranking de cartões com o Query Builder
    public function rankingCartoes(?int $ano = null, int $limite = 20): Collection
    {
        return DB::table('cartao as c')
            ->join('jogador as j', 'j.id_jogador', '=', 'c.jogador_id')
            ->join('clube as cl', 'cl.id_clube', '=', 'j.clube_id')
            ->join('partida as p', 'p.id_partida', '=', 'c.partida_id')
            ->join('temporada as t', 't.id_temporada', '=', 'p.temporada_id')
            ->when($ano, fn ($q) => $q->where('t.ano', $ano))
            ->groupBy('j.id_jogador', 'j.nome', 'cl.nome')
            ->select('j.id_jogador', 'j.nome as jogador', 'cl.nome as clube_atual')
            ->selectRaw("count(*) FILTER (WHERE c.tipo = 'amarelo')  AS amarelos")
            ->selectRaw("count(*) FILTER (WHERE c.tipo = 'vermelho') AS vermelhos")
            ->selectRaw('count(*) AS total')
            ->orderByDesc('total')
            ->orderByDesc('vermelhos')
            ->limit($limite)
            ->get();
    }

    // números gerais para os cartões do painel
    public function totais(): array
    {
        $partidas = Partida::count();
        $gols = Gol::count();
        $realizadas = Partida::query()->where('data_partida', '<=', now())->count();
        $golsRealizados = Gol::query()->whereHas('partida', fn ($q) => $q->where('data_partida', '<=', now()))->count();

        return [
            'clubes' => Clube::count(),
            'jogadores' => Jogador::count(),
            'partidas' => $partidas,
            'gols' => $gols,
            'cartoes' => Cartao::count(),
            'media_gols' => $realizadas ? round($golsRealizados / $realizadas, 2) : 0,
        ];
    }
}
