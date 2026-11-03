<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClassificacaoService
{
    public function tabela(int $ano): Collection
    {
        return collect(DB::select('SELECT * FROM fn_classificacao(?)', [$ano]));
    }

    public function campanhaDoClube(int $clubeId, int $ano): ?object
    {
        return $this->tabela($ano)->firstWhere('clube_id', $clubeId);
    }
}
