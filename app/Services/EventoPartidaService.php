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

    // gol normal - clube_id vai nulo e a trigger usa o clube do jogador
    // gol contra - o PHP calcula o adversário e manda o clube_id pronto
    public function registrarGol(Partida $partida, int $jogadorId, int $minuto, bool $contra = false): Gol
    {
        return BancoDeDados::executar(function () use ($partida, $jogadorId, $minuto, $contra) {
            $gol = Gol::create([
                'partida_id' => $partida->id_partida,
                'jogador_id' => $jogadorId,
                'minuto' => $minuto,
                'clube_id' => $contra ? $this->adversarioDoJogador($partida, $jogadorId) : null,
            ]);

            return $gol->refresh()->load(['jogador', 'clube']);
        });
    }

    public function atualizarGol(Gol $gol, array $dados): Gol
    {
        return BancoDeDados::executar(function () use ($gol, $dados) {
            $alteracoes = ['minuto' => $dados['minuto'] ?? $gol->minuto];

            // só recalcula o clube se o jogador ou o contra mudarem
            if (array_key_exists('jogador_id', $dados) || array_key_exists('contra', $dados)) {
                $jogadorId = (int) ($dados['jogador_id'] ?? $gol->jogador_id);
                $contra = filter_var($dados['contra'] ?? false, FILTER_VALIDATE_BOOLEAN);

                $alteracoes['jogador_id'] = $jogadorId;
                $alteracoes['clube_id'] = $contra ? $this->adversarioDoJogador($gol->partida, $jogadorId) : null;
            }

            $gol->update($alteracoes);

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
        BancoDeDados::executar(fn () => $cartao->delete());
    }

    private function adversarioDoJogador(Partida $partida, int $jogadorId): int
    {
        $clubeDoJogador = Jogador::findOrFail($jogadorId)->clube_id;

        return match ($clubeDoJogador) {
            $partida->mandante_id => $partida->visitante_id,
            $partida->visitante_id => $partida->mandante_id,
            default => throw new RegraNegocioException('Gol contra: o jogador precisa ser de um dos clubes da partida.'),
        };
    }
}