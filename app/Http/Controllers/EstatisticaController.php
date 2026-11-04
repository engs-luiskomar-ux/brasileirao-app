<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroRequest;
use App\Models\Temporada;
use App\Services\EstatisticaService;
use Illuminate\View\View;

// Thiago - telas de estatísticas artilharia e ranking de cartões.
class EstatisticaController extends Controller
{
    public function __construct(private readonly EstatisticaService $estatisticas) {}

    public function artilharia(FiltroRequest $request): View
    {
        $ano = $request->integer('ano') ?: null;

        return view('estatisticas.artilharia', [
            'ano' => $ano,
            'anos' => Temporada::query()->orderByDesc('ano')->pluck('ano'),
            'artilheiros' => $this->estatisticas->artilharia($ano, 30),
        ]);
    }

    public function cartoes(FiltroRequest $request): View
    {
        $ano = $request->integer('ano') ?: null;

        return view('estatisticas.cartoes', [
            'ano' => $ano,
            'anos' => Temporada::query()->orderByDesc('ano')->pluck('ano'),
            'ranking' => $this->estatisticas->rankingCartoes($ano, 30),
        ]);
    }
}
