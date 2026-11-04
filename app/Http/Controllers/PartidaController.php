<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroRequest;
use App\Http\Requests\PartidaRequest;
use App\Models\Partida;
use App\Services\ClubeService;
use App\Services\PartidaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PartidaController extends Controller
{
    public function __construct(
        private readonly PartidaService $partidas,
        private readonly ClubeService $clubes,
    ) {}

    public function index(FiltroRequest $request): View
    {
        $filtros = $request->only('temporada_id', 'rodada', 'clube_id');

        if (! $request->has('temporada_id')) {
            $filtros['temporada_id'] = $this->partidas->temporadaPadrao();
        }

        return view('partidas.index', [
            'partidas' => $this->partidas->listar($filtros),
            'filtros' => $filtros,
            'temporadas' => $this->partidas->temporadas(),
            'clubes' => $this->clubes->opcoes(),
        ]);
    }

    public function create(): View
    {
        return view('partidas.create', $this->dadosFormulario(new Partida));
    }

    public function store(PartidaRequest $request): RedirectResponse
    {
        $partida = $this->partidas->criar($request->validated());

        return redirect()->route('partidas.show', $partida)->with('sucesso', 'Partida cadastrada');
    }

    public function show(Partida $partida): View
    {
        return view('partidas.show', ['partida' => $this->partidas->detalhar($partida)]);
    }

    public function edit(Partida $partida): View
    {
        return view('partidas.edit', $this->dadosFormulario($partida));
    }

    public function update(PartidaRequest $request, Partida $partida): RedirectResponse
    {
        $this->partidas->atualizar($partida, $request->validated());

        return redirect()->route('partidas.show', $partida)->with('sucesso', 'Partida atualizada');
    }

    public function destroy(Partida $partida): RedirectResponse
    {
        $this->partidas->excluir($partida);

        return redirect()->route('partidas.index')->with('sucesso', 'Partida excluida');
    }

    private function dadosFormulario(Partida $partida): array
    {
        return [
            'partida' => $partida,
            'temporadas' => $this->partidas->temporadas(),
            'clubes' => $this->clubes->opcoes(),
        ];
    }
}
