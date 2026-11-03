-- Thiago - súmula da partida gols e cartões
@inject('eventos', 'App\Services\EventoPartidaService')
@php($sumula = $eventos->sumula($partida))

<div class="grade grade-2" id="sumula">
    {{-- gols --}}
    <div class="card">
        <h2>⚽ Gols</h2>
        <ul class="linha-tempo">
            @forelse ($sumula['gols'] as $gol)
                <li>
                    <span class="minuto">{{ $gol->minuto }}'</span>
                    <span>
                        <a href="{{ route('jogadores.show', $gol->jogador_id) }}"><strong>{{ $gol->jogador->nome }}</strong></a>
                        <span class="texto-suave">— gol do {{ $gol->clube->nome }}</span>
                    </span>
                    <form method="POST" action="{{ route('gols.destroy', $gol) }}" class="inline" style="margin-left:auto"
                          onsubmit="return confirm('Remover este gol?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn-link">remover</button>
                    </form>
                </li>
            @empty
                <li class="texto-suave">Nenhum gol registrado.</li>
            @endforelse
        </ul>

        <form method="POST" action="{{ route('partidas.gols.store', $partida) }}" class="form" style="margin-top:16px">
            @csrf
            <div class="form-grade">
                <div class="campo">
                    <label for="gol_jogador">Jogador</label>
                    <select id="gol_jogador" name="jogador_id" required>
                        @include('eventos._opcoes_jogadores')
                    </select>
                </div>
                <div class="campo">
                    <label for="gol_minuto">Minuto</label>
                    <input type="number" id="gol_minuto" name="minuto" min="0" max="120" required>
                </div>
            </div>
            <label class="campo-check"><input type="checkbox" name="contra" value="1"> Gol contra (conta para o adversário)</label>
            <div><button class="btn">Registrar gol</button></div>
            <p class="texto-suave" style="margin:0">A trigger <code>trg_gol_valida</code> define o clube do gol e bloqueia gol de jogador expulso.</p>
        </form>
    </div>

    {{-- cartões --}}
    <div class="card">
        <h2>🟨 Cartões</h2>
        <ul class="linha-tempo">
            @forelse ($sumula['cartoes'] as $cartao)
                <li>
                    <span class="minuto">{{ $cartao->minuto }}'</span>
                    <span class="cartao cartao-{{ $cartao->tipo }}"></span>
                    <span>
                        <strong>{{ $cartao->jogador->nome }}</strong>
                        <span class="texto-suave">({{ $cartao->jogador->clube->nome }})</span>
                    </span>
                    <form method="POST" action="{{ route('cartoes.destroy', $cartao) }}" class="inline" style="margin-left:auto"
                          onsubmit="return confirm('Remover este cartão?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn-link">remover</button>
                    </form>
                </li>
            @empty
                <li class="texto-suave">Nenhum cartão registrado.</li>
            @endforelse
        </ul>

        <form method="POST" action="{{ route('partidas.cartoes.store', $partida) }}" class="form" style="margin-top:16px">
            @csrf
            <div class="form-grade">
                <div class="campo">
                    <label for="cartao_jogador">Jogador</label>
                    <select id="cartao_jogador" name="jogador_id" required>
                        @include('eventos._opcoes_jogadores')
                    </select>
                </div>
                <div class="campo">
                    <label for="cartao_minuto">Minuto</label>
                    <input type="number" id="cartao_minuto" name="minuto" min="0" max="120" required>
                </div>
                <div class="campo">
                    <label for="cartao_tipo">Tipo</label>
                    <select id="cartao_tipo" name="tipo">
                        <option value="amarelo">Amarelo</option>
                        <option value="vermelho">Vermelho</option>
                    </select>
                </div>
            </div>
            <div><button class="btn">Registrar cartão</button></div>
            <p class="texto-suave" style="margin:0">Procedure <code>sp_registrar_cartao</code>: o 2º amarelo vira vermelho automaticamente.</p>
        </form>
    </div>
</div>