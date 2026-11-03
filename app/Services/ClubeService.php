<?php

namespace App\Services;

use App\Models\Clube;
use App\Support\BancoDeDados;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ClubeService
{
    public function listar(?string $busca = null, ?string $estado = null, int $porPagina = 15): LengthAwarePaginator
    {
        return Clube::query()
            ->withCount('jogadores')
            ->when($busca, fn ($q) => $q->whereRaw('lower(nome) LIKE ?', [mb_strtolower(trim($busca)).'%']))
            ->when($estado, fn ($q) => $q->where('estado', mb_strtoupper($estado)))
            ->orderBy('nome')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function estados(): Collection
    {
        return Clube::query()->distinct()->orderBy('estado')->pluck('estado');
    }

    public function opcoes(): Collection
    {
        return Clube::query()->orderBy('nome')->get(['id_clube', 'nome']);
    }

    public function criar(array $dados): Clube
    {
        // Relê o registro para retornar os dados normalizados pela trigger.
        return BancoDeDados::executar(fn () => Clube::create($dados)->refresh());
    }

    public function atualizar(Clube $clube, array $dados): Clube
    {
        return BancoDeDados::executar(function () use ($clube, $dados) {
            $clube->update($dados);

            return $clube->refresh();
        });
    }

    public function excluir(Clube $clube): void
    {
        BancoDeDados::executar(fn () => $clube->delete());
    }
}
