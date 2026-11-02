<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartaoRequest;
use App\Models\Cartao;
use App\Models\Partida;
use App\Services\EventoPartidaService;
use Illuminate\Http\RedirectResponse;

// Thiago - registra e apaga cartões a partir da tela da partida.
class CartaoController extends Controller
{
    public function __construct(private readonly EventoPartidaService $eventos) {}

    public function store(CartaoRequest $request, Partida $partida): RedirectResponse
    {
        $mensagem = $this->eventos->registrarCartao(
            $partida,
            $request->integer('jogador_id'),
            $request->integer('minuto'),
            $request->string('tipo')->toString(),
        );

        return redirect()->to(route('partidas.show', $partida).'#sumula')->with('sucesso', $mensagem);
    }

    public function destroy(Cartao $cartao): RedirectResponse
    {
        $partidaId = $cartao->partida_id;
        $this->eventos->removerCartao($cartao);

        return redirect()->to(route('partidas.show', $partidaId).'#sumula')->with('sucesso', 'Cartão removido.');
    }
}