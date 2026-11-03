<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemporadaRequest;
use App\Models\Temporada;
use App\Services\TemporadaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TemporadaController extends Controller
{
    public function __construct(private readonly TemporadaService $temporadas) {}

    public function index(): View
    {
        return view('temporadas.index', ['temporadas' => $this->temporadas->listar()]);
    }

    public function create(): View
    {
        return view('temporadas.create', ['temporada' => new Temporada]);
    }

    public function store(TemporadaRequest $request): RedirectResponse
    {
        $temporada = $this->temporadas->criar($request->integer('ano'), $request->input('nome'));

        return redirect()->route('temporadas.index')->with('sucesso', "{$temporada->nome} cadastrada!");
    }

    public function edit(Temporada $temporada): View
    {
        return view('temporadas.edit', ['temporada' => $temporada]);
    }

    public function update(TemporadaRequest $request, Temporada $temporada): RedirectResponse
    {
        $this->temporadas->atualizar($temporada, $request->validated());

        return redirect()->route('temporadas.index')->with('sucesso', 'Temporada atualizada.');
    }

    public function destroy(Temporada $temporada): RedirectResponse
    {
        $this->temporadas->excluir($temporada);

        return redirect()->route('temporadas.index')->with('sucesso', 'Temporada excluída.');
    }
}
