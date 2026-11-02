<?php

namespace App\Http\Controllers;

use App\Services\ClassificacaoService;
use App\Services\EstatisticaService;
use App\Services\TemporadaService;
use Illuminate\View\View;

// Thiago - página inicial, junta números dos 4 módulos.
class DashboardController extends Controller
{
    public function __invoke(
        EstatisticaService $estatisticas,
        ClassificacaoService $classificacao,
        TemporadaService $temporadas,
    ): View {
        $ano = $temporadas->anoMaisRecente();

        return view('dashboard', [
            'totais' => $estatisticas->totais(),
            'ano' => $ano,
            'lideres' => $ano ? $classificacao->tabela($ano)->take(6) : collect(),
            'artilheiros' => $estatisticas->artilharia($ano, 5),
        ]);
    }
}