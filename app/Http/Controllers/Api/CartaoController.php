<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartaoRequest;
use App\Models\Partida;
use App\Services\EventoPartidaService;
use Illuminate\Http\JsonResponse;

// Thiago - resposta 201 com a mensagem da procedure e os cartões do jogador na partida.
class CartaoController extends Controller
{
    public function __construct(private readonly EventoPartidaService $eventos) {}

    public function store(CartaoRequest $request, Partida $partida): JsonResponse
    {
        $mensagem = $this->eventos->registrarCartao(
            $partida,
            $request->integer('jogador_id'),
            $request->integer('minuto'),
            $request->string('tipo')->toString(),
        );

        $cartoes = $partida->cartoes()
            ->where('jogador_id', $request->integer('jogador_id'))
            ->orderBy('id_cartao')
            ->get(['id_cartao as id', 'minuto', 'tipo']);

        return response()->json(['message' => $mensagem, 'cartoes_do_jogador' => $cartoes], 201);
    }
}