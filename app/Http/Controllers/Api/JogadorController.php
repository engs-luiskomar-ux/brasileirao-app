<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jogador;
use Illuminate\Http\Request;
use App\Services\JogadorService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\JogadorResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JogadorController extends Controller
{
    public function __construct(private readonly JogadorService $jogadores) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return JogadorResource::collection(
            $this->jogadores->listar($request->only('busca', 'clube_id', 'posicao'))
        );
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
