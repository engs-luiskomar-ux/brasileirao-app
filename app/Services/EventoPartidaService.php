<?php

namespace App\Services;

use App\Exceptions\RegraNegocioException;
use App\Jogador;
use App\Models\Cartao;
use App\Models\Gol;
use App\Models\Partida;
use App\Support\BancoDeDados;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

// Thiago - gols e cartões da partida
// gol - insert comum, a trigger trg_gol_valida define o clube e barra jogador expulso
// cartão - chama a procedure sp_registrar_cartao que caso ganhar o 2º amarelo vira vermelho
class EventoPartidaService
{
    // dados da súmula, usados na tela da partida
    public function sumula(Partida $partida): array
    {
        $elenco = fn (int $clubeId) => Jogador::query()
            ->where('clube_id', $clubeId)
            ->orderBy('nome')
            ->get(['id_jogador', 'nome', 'numero_camisa', 'clube_id']);

        return [
            'gols' => $partida->gols()->with(['jogador', 'clube'])->orderBy('minuto')->orderBy('id_gol')->get(),
            'cartoes' => $partida->cartoes()->with('jogador.clube')->orderBy('minuto')->orderBy('id_cartao')->get(),
            'elencoMandante' => $elenco($partida->mandante_id),
            'elencoVisitante' => $elenco($partida->visitante_id),
        ];
    }

    public function listarGols(?int $partidaId = null, int $porPagina = 30): LengthAwarePaginator
    {
        return Gol::query()
            ->with(['jogador', 'clube'])
            ->when($partidaId, fn ($q) => $q->where('partida_id', $partidaId))
            ->orderByDesc('id_gol')
            ->paginate($porPagina)
            ->withQueryString();
    }

    // A trigger define o clube creditado de acordo com o tipo do gol.
    public function registrarGol(Partida $partida, int $jogadorId, int $minuto, bool $contra = false): Gol
    {
        return BancoDeDados::executar(function () use ($partida, $jogadorId, $minuto, $contra) {
            $gol = Gol::create([
                'partida_id' => $partida->id_partida,
                'jogador_id' => $jogadorId,
                'minuto' => $minuto,
                'clube_id' => null,
                'contra' => $contra,
            ]);

            return $gol->refresh()->load(['jogador', 'clube']);
        });
    }

    public function atualizarGol(Gol $gol, array $dados): Gol
    {
        return BancoDeDados::executar(function () use ($gol, $dados) {
            // Omitir "contra" preserva o tipo e o clube creditado, inclusive após transferência.
            $gol->update(array_intersect_key($dados, array_flip(['minuto', 'jogador_id', 'contra'])));

            return $gol->refresh()->load(['jogador', 'clube']);
        });
    }

    public function removerGol(Gol $gol): void
    {
        BancoDeDados::executar(fn () => $gol->delete());
    }

    // chama a procedure e devolve a mensagem do parâmetro INOUT p_mensagem
    public function registrarCartao(Partida $partida, int $jogadorId, int $minuto, string $tipo): string
    {
        return BancoDeDados::executar(function () use ($partida, $jogadorId, $minuto, $tipo) {
            $resultado = DB::selectOne('CALL sp_registrar_cartao(?, ?, ?, ?, NULL)', [
                $partida->id_partida,
                $jogadorId,
                $minuto,
                $tipo,
            ]);

            return $resultado->p_mensagem;
        });
    }

    public function removerCartao(Cartao $cartao): void
    {
        BancoDeDados::executar(function () use ($cartao) {
            Partida::query()->lockForUpdate()->findOrFail($cartao->partida_id);
            $cartao->refresh();
            if ($cartao->automatico) {
                throw new RegraNegocioException('Remova um dos cartões amarelos para cancelar esta expulsão automática.');
            }
            $cartao->delete();
            if ($cartao->tipo === 'amarelo') {
                $cartoes = Cartao::query()->where('partida_id', $cartao->partida_id)->where('jogador_id', $cartao->jogador_id);
                if ((clone $cartoes)->where('tipo', 'amarelo')->count() < 2) {
                    $cartoes->where('automatico', true)->delete();
                }
            }
        });
    }
}
