<?php

namespace App\Services;

use App\Jogador;
use App\Support\BancoDeDados;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
    public function estatisticas(Jogador $jogador): object
    {
        return DB::selectOne('SELECT * FROM fn_estatisticas_jogador(?)', [$jogador->id_jogador]);
    }
    public function transferir(Jogador $jogador, int $clubeDestinoId, ?int $numeroCamisa = null): Jogador
    {
        return BancoDeDados::executar(function () use ($jogador, $clubeDestinoId, $numeroCamisa) {
            DB::statement('CALL sp_transferir_jogador(?, ?, ?)', [
                $jogador->id_jogador,
                $clubeDestinoId,
                $numeroCamisa,
            ]);

            return $jogador->refresh()->load('clube');
        });
    }
    public function historico(Jogador $jogador): Collection
    {
        return $jogador->transferencias()
            ->with(['origem', 'destino'])
            ->orderByDesc('data_transferencia')
            ->orderByDesc('id_transferencia')
            ->get();
    }
}
