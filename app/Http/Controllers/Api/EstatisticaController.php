<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FiltroRequest;
use App\Services\EstatisticaService;
use Illuminate\Http\JsonResponse;

// Thiago - GET /api/artilharia?ano=2019&limite=10 chama a function fn_artilharia.
class EstatisticaController extends Controller
{
    public function artilharia(FiltroRequest $request, EstatisticaService $estatisticas): JsonResponse
    {
        $ano = $request->integer('ano') ?: null;

        return response()->json([
            'ano' => $ano ?? 'todas',
            'data' => $estatisticas->artilharia($ano, $request->filled('limite') ? $request->integer('limite') : 20),
        ]);
    }
}
