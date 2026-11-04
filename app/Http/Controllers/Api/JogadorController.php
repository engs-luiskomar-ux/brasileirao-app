<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FiltroRequest;
use App\Http\Requests\JogadorRequest;
use App\Http\Requests\TransferenciaRequest;
use App\Http\Resources\JogadorResource;
use App\Jogador;
use App\Services\JogadorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class JogadorController extends Controller
{
    public function __construct(private readonly JogadorService $jogadores) {}

    public function index(FiltroRequest $request): AnonymousResourceCollection
    {
        return JogadorResource::collection(
            $this->jogadores->listar($request->only('busca', 'clube_id', 'posicao'))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JogadorRequest $request): JsonResponse
    {
        $jogador = $this->jogadores->criar($request->validated());

        return (new JogadorResource($jogador))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Jogador $jogador): JogadorResource
    {
        return (new JogadorResource($jogador->load('clube')))
            ->additional(['estatisticas' => $this->jogadores->estatisticas($jogador)]);
    }

    public function update(JogadorRequest $request, Jogador $jogador): JogadorResource
    {
        return new JogadorResource($this->jogadores->atualizar($jogador, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jogador $jogador): Response
    {
        $this->jogadores->excluir($jogador);

        return response()->noContent();
    }

    public function transferir(TransferenciaRequest $request, Jogador $jogador): JogadorResource
    {
        $jogador = $this->jogadores->transferir(
            $jogador,
            $request->integer('clube_destino_id'),
            $request->filled('numero_camisa') ? $request->integer('numero_camisa') : null,
        );

        return (new JogadorResource($jogador))
            ->additional(['historico' => $this->jogadores->historico($jogador)->map(fn ($h) => [
                'de' => $h->origem->nome,
                'para' => $h->destino->nome,
                'data' => $h->data_transferencia->toIso8601String(),
            ])]);
    }
}
