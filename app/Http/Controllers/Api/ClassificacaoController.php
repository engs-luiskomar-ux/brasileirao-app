<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClassificacaoService;
use Illuminate\Http\JsonResponse;

class ClassificacaoController extends Controller
{
    public function __invoke(int $ano, ClassificacaoService $classificacao): JsonResponse
    {
        $tabela = $classificacao->tabela($ano);

        if ($tabela->isEmpty()) {
            return response()->json(['message' => "Nenhuma partida realizada em {$ano}."], 404);
        }

        return response()->json(['ano' => $ano, 'data' => $tabela]);
    }
}
