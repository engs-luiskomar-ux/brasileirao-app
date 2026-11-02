<?php

namespace App\Services;

use App\Jogador;
use App\Support\BancoDeDados;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JogadorService
{
    public function listar(array $filtros = [], int $porPagina = 20): LengthAwarePaginator
    {
        return Jogador::query()
            ->with('clube')
            ->when($filtros['busca'] ?? null, fn ($q, $busca) => $q->where('nome', 'ILIKE', '%'.trim($busca).'%'))
            ->when($filtros['clube_id'] ?? null, fn ($q, $clubeId) => $q->where('clube_id', $clubeId))
            ->when($filtros['posicao'] ?? null, fn ($q, $posicao) => $q->where('posicao', $posicao))
            ->orderBy('nome')
            ->paginate($porPagina)
            ->withQueryString();
    }
    public function criar(array $dados): Jogador
    {
        return BancoDeDados::executar(fn () => Jogador::create($dados)->load('clube'));
    }
    public function atualizar(Jogador $jogador, array $dados): Jogador
    {
        return BancoDeDados::executar(function () use ($jogador, $dados) {
            $jogador->update($dados);

            return $jogador->refresh()->load('clube');
        });
    }
    public function excluir(Jogador $jogador): void
    {
        BancoDeDados::executar(fn () => $jogador->delete());
    }
}
