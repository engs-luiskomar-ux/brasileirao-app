<?php

namespace App\Support;

use App\Exceptions\RegraNegocioException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class BancoDeDados
{
    public static function executar(callable $operacao): mixed
    {
        try {
            return DB::transaction(fn () => $operacao());
        } catch (QueryException $e) {
            throw self::traduzir($e);
        }
    }

    public static function traduzir(QueryException $e): Throwable
    {
        $detalhe = $e->errorInfo[2] ?? $e->getMessage();

        return match ((string) $e->getCode()) {
            'P0001' => new RegraNegocioException(
                self::mensagemDoRaise($detalhe)
            ),
            '23503' => new RegraNegocioException(
                'Operação não permitida: existem registros vinculados a este item.',
                409
            ),
            '23505' => new RegraNegocioException(
                'Já existe um registro com esses dados.',
                409
            ),
            '23514' => new RegraNegocioException(
                'Os dados informados violam uma regra (CHECK) do banco de dados.'
            ),
            default => $e,
        };
    }

    private static function mensagemDoRaise(string $detalhe): string
    {
        if (preg_match('/ERROR:\s+(.+?)(?:\R|$)/u', $detalhe, $m)) {
            return trim($m[1]);
        }

        return $detalhe;
    }
}
