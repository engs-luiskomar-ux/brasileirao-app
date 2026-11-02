<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartidaRequest;
use App\Http\Resources\PartidaResource;
use App\Models\Partida;
use App\Services\PartidaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PartidaController extends Controller
{
    public function __construct(private readonly PartidaService $partidas)
    {}
    public function index(Request $request): AnonymousResourceCollection
    {
            return PartidaResource::collection(
                $this->partidas->listar($request->only('temporada_id', 'rodada', 'clube_id'))
            );
    }

  
    public function store(PartidaRequest $request): JsonResponse
    {
            $partida = $this->partidas->criar($request->validated());

            return(new PartidaResource($partida))->response()->setStatusCode(201);
    }

   
    public function show(Partida $partida): PartidaResource
    {
       return new PartidaResource($this->partidas->detalhar($partida));
    }

  
    public function update(PartidaRequest $request, Partida $partida): PartidaResource
    {
        return new PartidaResource($this->partidas->atualizar($partida, $request->validated()));
    }

    public function destroy(Partida $partida): Response
    {
        $this->partidas->excluir($partida);

        return response()->noContent();
    }
}
