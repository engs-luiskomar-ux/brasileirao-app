@extends('layouts.app')

@section('titulo', $jogador->nome)

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>{{ $jogador->nome }}</h1>
            <p>
                <span class="tag">{{ $jogador->posicao }}</span>
                @if ($jogador->numero_camisa) <span class="tag tag-ouro">camisa {{ $jogador->numero_camisa }}</span> @endif
                · <a href="{{ route('clubes.show', $jogador->clube_id) }}">{{ $jogador->clube->nome }}</a>
                · nascido em {{ $jogador->data_nascimento->format('d/m/Y') }}
            </p>
        </div>
        <div class="acoes">
            <a href="{{ route('jogadores.edit', $jogador) }}" class="btn btn-secundario">Editar</a>
            <a href="{{ route('jogadores.index') }}" class="btn btn-secundario">Voltar</a>
        </div>
    </div>

    <div class="grade grade-4" style="margin-bottom:20px">
        <div class="indicador"><small>Gols</small><strong>{{ $estatisticas->gols }}</strong></div>
        <div class="indicador"><small><span class="cartao cartao-amarelo"></span>Amarelos</small><strong>{{ $estatisticas->amarelos }}</strong></div>
        <div class="indicador"><small><span class="cartao cartao-vermelho"></span>Vermelhos</small><strong>{{ $estatisticas->vermelhos }}</strong></div>
        <div class="indicador"><small>Temporadas com gol</small><strong>{{ $estatisticas->temporadas_com_gol }}</strong></div>
        <div class="indicador"><small>Idade</small><strong>{{ $estatisticas->idade }}</strong></div>
    </div>
    <p class="texto-suave">Números calculados no banco por <code>fn_estatisticas_jogador({{ $jogador->id_jogador }})</code>.</p>

    <div class="grade grade-2">
        <form method="POST" action="{{ route('jogadores.transferir', $jogador) }}" class="card form">
            @csrf
            <h2>Transferir jogador</h2>
            <div class="campo">
                <label for="clube_destino_id">Clube de destino *</label>
                <select id="clube_destino_id" name="clube_destino_id" required>
                    <option value="">Selecione…</option>
                    @foreach ($clubes as $clube)
                        @continue($clube->id_clube === $jogador->clube_id)
                        <option value="{{ $clube->id_clube }}" @selected(old('clube_destino_id') == $clube->id_clube)>{{ $clube->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label for="numero_camisa_nova">Nova camisa</label>
                <input type="number" id="numero_camisa_nova" name="numero_camisa" min="1" max="99" value="{{ old('numero_camisa') }}">
                <div class="ajuda">Vazio = mantém o número atual se estiver livre no destino (regra da procedure).</div>
            </div>
            <div><button class="btn">Transferir (sp_transferir_jogador)</button></div>
        </form>

        <div class="card tabela-wrap">
            <h2>Histórico de transferências</h2>
            <table>
                <thead><tr><th>Data</th><th>De</th><th>Para</th></tr></thead>
                <tbody>
                    @forelse ($historico as $transferencia)
                        <tr>
                            <td>{{ $transferencia->data_transferencia->format('d/m/Y H:i') }}</td>
                            <td>{{ $transferencia->origem->nome }}</td>
                            <td><strong>{{ $transferencia->destino->nome }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="vazio">Nenhuma transferência registrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <p class="texto-suave">Gravado automaticamente pela trigger <code>trg_jogador_transferencia</code>.</p>
        </div>
    </div>
@endsection
