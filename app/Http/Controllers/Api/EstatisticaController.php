<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EstatisticaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Thiago - GET /api/artilharia?ano=2019&limite=10 chama a function fn_artilharia.
class EstatisticaController extends Controller
{
    public function artilharia(Request $request, EstatisticaService $estatisticas): JsonResponse
    {
        $request->validate([
            'ano' => ['nullable', 'integer'],
            'limite' => ['nullable', 'integer', 'between:1,100'],
        ]);

        $ano = $request->integer('ano') ?: null;

        return response()->json([
            'ano' => $ano ?? 'todas',
            'data' => $estatisticas->artilharia($ano, $request->integer('limite', 20)),
        ]);
    }
}