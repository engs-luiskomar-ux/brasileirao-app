<?php

namespace App\Http\Controllers;

use App\Models\Temporada;
use App\Services\ClassificacaoService;
use App\Services\TemporadaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassificacaoController extends Controller
{
    public function index(Request $request, ClassificacaoService $classificacao, TemporadaService $temporadas): View
    {
        $ano = $request->integer('ano') ?: $temporadas->anoMaisRecente();

        return view('classificacao.index', [
            'ano' => $ano,
            'anos' => Temporada::query()->orderByDesc('ano')->pluck('ano'),
            'tabela' => $ano ? $classificacao->tabela($ano) : collect(),
        ]);
    }
}
