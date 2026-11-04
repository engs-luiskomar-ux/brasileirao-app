<?php

namespace App\Services;

use App\Exceptions\RegraNegocioException;
use App\Models\Temporada;
use App\Support\BancoDeDados;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TemporadaService
{
    public function listar(): Collection
    {
        return Temporada::query()->withCount('partidas')->orderByDesc('ano')->get();
    }

    public function criar(int $ano, ?string $nome = null): Temporada
    {
        return BancoDeDados::executar(function () use ($ano, $nome) {
            $resultado = DB::selectOne('CALL sp_criar_temporada(?, ?, NULL)', [$ano, $nome]);

            return Temporada::findOrFail($resultado->p_id_temporada);
        });
    }

    public function atualizar(Temporada $temporada, array $dados): Temporada
    {
        return BancoDeDados::executar(function () use ($temporada, $dados) {
            $temporada = Temporada::query()->lockForUpdate()->findOrFail($temporada->id_temporada);
            if ((int) $dados['ano'] !== $temporada->ano && $temporada->partidas()->exists()) {
                throw new RegraNegocioException('Não é possível mudar o ano de uma temporada que já tem partidas.');
            }

            $temporada->update([
                'ano' => $dados['ano'],
                'nome' => ($dados['nome'] ?? null) ?: "Brasileirão {$dados['ano']}",
            ]);

            return $temporada;
        });
    }

    public function excluir(Temporada $temporada): void
    {
        BancoDeDados::executar(fn () => $temporada->delete());
    }

    public function anoMaisRecente(): ?int
    {
        return Temporada::query()->has('partidas')->max('ano');
    }
}
