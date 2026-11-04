<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClubeRequest;
use App\Http\Requests\FiltroRequest;
use App\Models\Clube;
use App\Services\ClassificacaoService;
use App\Services\ClubeService;
use App\Services\TemporadaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClubeController extends Controller
{
    public function __construct(private readonly ClubeService $clubes) {}

    public function index(FiltroRequest $request): View
    {
        return view('clubes.index', [
            'clubes' => $this->clubes->listar($request->query('busca'), $request->query('estado')),
            'estados' => $this->clubes->estados(),
        ]);
    }

    public function create(): View
    {
        return view('clubes.create', ['clube' => new Clube]);
    }

    public function store(ClubeRequest $request): RedirectResponse
    {
        $clube = $this->clubes->criar($request->validated());

        return redirect()->route('clubes.show', $clube)->with('sucesso', "Clube {$clube->nome} cadastrado!");
    }

    public function show(Clube $clube, TemporadaService $temporadas, ClassificacaoService $classificacao): View
    {
        $ano = $temporadas->anoMaisRecente();

        return view('clubes.show', [
            'clube' => $clube->loadCount('jogadores'),
            'elenco' => $clube->jogadores()->orderBy('posicao')->orderBy('nome')->get(),
            'ano' => $ano,
            'campanha' => $ano ? $classificacao->campanhaDoClube($clube->id_clube, $ano) : null,
        ]);
    }

    public function edit(Clube $clube): View
    {
        return view('clubes.edit', ['clube' => $clube]);
    }

    public function update(ClubeRequest $request, Clube $clube): RedirectResponse
    {
        $this->clubes->atualizar($clube, $request->validated());

        return redirect()->route('clubes.show', $clube)->with('sucesso', 'Clube atualizado.');
    }

    public function destroy(Clube $clube): RedirectResponse
    {
        $this->clubes->excluir($clube);

        return redirect()->route('clubes.index')->with('sucesso', 'Clube excluído.');
    }
}
