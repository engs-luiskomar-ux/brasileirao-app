<?php

namespace App\Services;
use App\Models\Partida;
use App\Models\Temporada;
use App\Support\BancoDeDados;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use League\Uri\UrlPattern\Part;

class PartidaService
{
    private const RELACOES = ['temporada', 'mandante', 'visitante'];
   
    public function listar(array $filtros = [], int $porPagina = 20): LengthAwarePaginator
    {
        $temporadaId = $filtros['temporada_id'] ?? null;

        return Partida::query()
            ->select('partida.*', 'placar.gols_mandante', 'placar_gols_visitante')
            ->crossJoin(DB::raw('LATERAL fn_placar_partida(partida.id_partida) as placar'))
            ->with(self::RELACOES)
            ->when($temporadaId, fn ($q) => $q->where('partida.temporada_id', $temporadaId))
            ->when($filtros['rodada'] ?? null, fn ($q, $rodada) => $q->where('partida.rodada', $rodada))
            ->when($filtros['clube_id'] ?? null, fn ($q, $clubeId) => $q->where(
                fn ($w) => $w->where('partida.mandante_id', $clubeId)-orWhere('partida.visitante_id', $clubeId)
            ))
            -> when(
                $temporadaId,
                 fn ($q) => $q->orderBy('partida.rodada')->orderBy('partida.data_partida'),
                fn ($q) => $q->orderByDesc('partida.data_partida'),
            )
            ->paginate($porPagina)
            ->withQueryString();
    }

        // Função para retornar os gols do mandante e visitante
        public function placar(Partida $partida): object{
            return DB::selectOne('SELECT * FROM fn_placar_partida(?)', [$partida->id_partida]);
        }

        public function detalhar(Partida $partida): Partida
        {
            $placar = $this->placar($partida);

            return $partida->load(self::RELACOES)->forceFill([
                'gols_mandante' => $placar->gols_mandante,
                'gols_visitante' => $placar->gols_visitante,
            ]);
        }

        public function criar(array $dados): Partida {
            return BancoDeDados::executar(function () use ($dados) {
                $resultado = DB::selectOne('CALL sp_registrar_partida(?, ?, ?, ?, ?, NULL', [
                        $dados['temporada_id'],
                        $dados['rodada'],
                        Carbon::parse($dados['data_partida']->format('Y-m-d H:i:s')),
                        $dados['mandante_id'],
                        $dados['visitante_id'],
                ]);

                return $this->detalhar(Partida::findOrFail($resultado->p_id_partida));
            });
        }

        public function atualizar(Partida $partida, array $dados): Partida {

            return BancoDeDados::executar(function () use ($partida, $dados) {
                    $partida->update($dados);

                    return $this->detalhar($partida->refresh());
            });
        }

        public function excluir(Partida $partida): void {
            BancoDeDados::executar(fn () => $partida->delete());
        }

          public function temporadas(): Collection
    {
        return Temporada::query()->orderByDesc('ano')->get(['id_temporada', 'ano', 'nome']);
    }
}
