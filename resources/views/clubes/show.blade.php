@extends('layouts.app')

@section('titulo', $clube->nome)

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>{{ $clube->nome }}</h1>
            <p>{{ $clube->cidade }} / {{ $clube->estado }} @if ($clube->ano_fundacao) · fundado em {{ $clube->ano_fundacao }} @endif</p>
        </div>
        <div class="acoes">
            <a href="{{ route('clubes.edit', $clube) }}" class="btn btn-secundario">Editar</a>
            <a href="{{ route('clubes.index') }}" class="btn btn-secundario">Voltar</a>
        </div>
    </div>

    <div class="grade grade-4" style="margin-bottom:20px">
        <div class="indicador"><small>Jogadores no elenco</small><strong>{{ $clube->jogadores_count }}</strong></div>
        @if ($campanha)
            <div class="indicador"><small>Posição em {{ $ano }}</small><strong>{{ $campanha->posicao }}º</strong></div>
            <div class="indicador"><small>Pontos em {{ $ano }}</small><strong>{{ $campanha->pontos }}</strong></div>
            <div class="indicador"><small>Aproveitamento</small><strong>{{ $campanha->aproveitamento }}%</strong></div>
        @elseif ($ano)
            <div class="indicador"><small>Temporada {{ $ano }}</small><strong>—</strong><span class="texto-suave">Sem classificação nesta temporada.</span></div>
        @else
            <div class="indicador"><small>Temporadas</small><strong>—</strong><span class="texto-suave">Nenhuma temporada cadastrada.</span></div>
        @endif
    </div>

    <div class="card tabela-wrap">
        <h2>Elenco atual</h2>
        <table>
            <thead><tr><th class="num">Nº</th><th>Jogador</th><th>Posição</th><th>Nascimento</th></tr></thead>
            <tbody>
                @forelse ($elenco as $jogador)
                    <tr>
                        <td class="num">{{ $jogador->numero_camisa ?? '—' }}</td>
                        <td><a href="{{ route('jogadores.show', $jogador) }}">{{ $jogador->nome }}</a></td>
                        <td><span class="tag tag-cinza">{{ $jogador->posicao }}</span></td>
                        <td>{{ $jogador->data_nascimento?->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="vazio">Nenhum jogador cadastrado neste clube.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
