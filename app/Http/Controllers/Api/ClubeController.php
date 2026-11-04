<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubeRequest;
use App\Http\Requests\FiltroRequest;
use App\Http\Resources\ClubeResource;
use App\Models\Clube;
use App\Services\ClubeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClubeController extends Controller
{
    public function __construct(private readonly ClubeService $clubes) {}

    public function index(FiltroRequest $request): AnonymousResourceCollection
    {
        return ClubeResource::collection(
            $this->clubes->listar($request->query('busca'), $request->query('estado'))
        );
    }

    public function store(ClubeRequest $request): JsonResponse
    {
        $clube = $this->clubes->criar($request->validated());

        return (new ClubeResource($clube))->response()->setStatusCode(201);
    }

    public function show(Clube $clube): ClubeResource
    {
        return new ClubeResource($clube->loadCount('jogadores'));
    }

    public function update(ClubeRequest $request, Clube $clube): ClubeResource
    {
        return new ClubeResource($this->clubes->atualizar($clube, $request->validated()));
    }

    public function destroy(Clube $clube): Response
    {
        $this->clubes->excluir($clube);

        return response()->noContent();
    }
}
