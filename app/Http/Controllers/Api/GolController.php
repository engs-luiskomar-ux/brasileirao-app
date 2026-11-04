<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FiltroRequest;
use App\Http\Requests\GolRequest;
use App\Http\Resources\GolResource;
use App\Models\Gol;
use App\Models\Partida;
use App\Services\EventoPartidaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

// Thiago - API REST de gols.
class GolController extends Controller
{
    public function __construct(private readonly EventoPartidaService $eventos) {}

    public function index(FiltroRequest $request): AnonymousResourceCollection
    {
        return GolResource::collection($this->eventos->listarGols($request->integer('partida_id') ?: null));
    }

    public function store(GolRequest $request): JsonResponse
    {
        $gol = $this->eventos->registrarGol(
            Partida::findOrFail($request->integer('partida_id')),
            $request->integer('jogador_id'),
            $request->integer('minuto'),
            $request->boolean('contra'),
        );

        return (new GolResource($gol))->response()->setStatusCode(201);
    }

    public function show(Gol $gol): GolResource
    {
        return new GolResource($gol->load(['jogador', 'clube']));
    }

    public function update(GolRequest $request, Gol $gol): GolResource
    {
        return new GolResource($this->eventos->atualizarGol($gol, $request->validated()));
    }

    public function destroy(Gol $gol): Response
    {
        $this->eventos->removerGol($gol);

        return response()->noContent();
    }
}
