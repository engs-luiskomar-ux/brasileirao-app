<?php

namespace App\Http\Controllers;

use App\Services;
use App\Jogador;
use Illuminate\Http\Request;
use Illuminate\View\View;


class JogadorController extends Controller
{

    public function __construct(
        private readonly JogadorService $jogadores,
        private readonly ClubeService $clubes,
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('jogadores.index', [
            'jogadores' => $this->jogadores->listar($request->only('busca', 'clube_id', 'posicao')),
            'clubes' => $this->clubes->opcoes(),
            'posicoes' => Jogador::POSICOES,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('jogadores.create', [
            'jogador' => new Jogador,
            'clubes' => $this->clubes->opcoes(),
            'posicoes' => Jogador::POSICOES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Jogador $jogador)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jogador $jogador)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jogador $jogador)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jogador $jogador)
    {
        //
    }
}
