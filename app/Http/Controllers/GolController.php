<?php

namespace App\Http\Controllers;

use App\Http\Requests\GolRequest;
use App\Models\Gol;
use App\Models\Partida;
use App\Services\EventoPartidaService;
use Illuminate\Http\RedirectResponse;

// Thiago - registra e apaga gols a partir da tela da partida.
class GolController extends Controller
{
    public function __construct(private readonly EventoPartidaService $eventos) {}

    public function store(GolRequest $request, Partida $partida): RedirectResponse
    {
        $gol = $this->eventos->registrarGol(
            $partida,
            $request->integer('jogador_id'),
            $request->integer('minuto'),
            $request->boolean('contra'),
        );

        return redirect()->to(route('partidas.show', $partida).'#sumula')
            ->with('sucesso', "Gol de {$gol->jogador->nome} aos {$gol->minuto}' para o {$gol->clube->nome}!");
    }

    public function destroy(Gol $gol): RedirectResponse
    {
        $partidaId = $gol->partida_id;
        $this->eventos->removerGol($gol);

        return redirect()->to(route('partidas.show', $partidaId).'#sumula')->with('sucesso', 'Gol removido.');
    }
}