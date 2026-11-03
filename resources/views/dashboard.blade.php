-- Thiago - página inicial com números de todos os módulos 
@extends('layouts.app')

@section('titulo', 'Início')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Brasileirão DB</h1>
            <p>Aplicação Laravel + PostgreSQL (Neon) sobre o banco do Brasileirão 2014–2024.</p>
        </div>
        <div class="acoes">
            <a href="{{ route('partidas.create') }}" class="btn">+ Partida</a>
            <a href="{{ route('jogadores.create') }}" class="btn btn-secundario">+ Jogador</a>
        </div>
    </div>

    <div class="grade grade-4" style="margin-bottom:20px">
        <div class="indicador"><small>Clubes</small><strong>{{ number_format($totais['clubes'], 0, ',', '.') }}</strong></div>
        <div class="indicador"><small>Jogadores</small><strong>{{ number_format($totais['jogadores'], 0, ',', '.') }}</strong></div>
        <div class="indicador"><small>Partidas</small><strong>{{ number_format($totais['partidas'], 0, ',', '.') }}</strong></div>
        <div class="indicador"><small>Gols</small><strong>{{ number_format($totais['gols'], 0, ',', '.') }}</strong></div>
        <div class="indicador"><small>Cartões</small><strong>{{ number_format($totais['cartoes'], 0, ',', '.') }}</strong></div>
        <div class="indicador"><small>Gols por partida</small><strong>{{ number_format($totais['media_gols'], 2, ',', '.') }}</strong></div>
    </div>

    <div class="grade grade-2">
        <div class="card tabela-wrap">
            <h2>Classificação {{ $ano }} — topo</h2>
            <table>
                <thead><tr><th class="num">#</th><th>Clube</th><th class="num">P</th><th class="num">J</th><th class="num">SG</th></tr></thead>
                <tbody>
                    @forelse ($lideres as $linha)
                        <tr>
                            <td class="num">{{ $linha->posicao }}</td>
                            <td><a href="{{ route('clubes.show', $linha->clube_id) }}">{{ $linha->clube }}</a></td>
                            <td class="num"><strong>{{ $linha->pontos }}</strong></td>
                            <td class="num">{{ $linha->jogos }}</td>
                            <td class="num">{{ $linha->saldo_gols }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="vazio">Sem partidas realizadas.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($ano)
                <a href="{{ route('classificacao.index', ['ano' => $ano]) }}">Ver tabela completa →</a>
            @endif
        </div>

        <div class="card tabela-wrap">
            <h2>Artilheiros {{ $ano }}</h2>
            <table>
                <thead><tr><th class="num">#</th><th>Jogador</th><th>Clube(s)</th><th class="num">Gols</th></tr></thead>
                <tbody>
                    @forelse ($artilheiros as $linha)
                        <tr>
                            <td class="num">{{ $linha->posicao }}</td>
                            <td><a href="{{ route('jogadores.show', $linha->jogador_id) }}">{{ $linha->jogador }}</a></td>
                            <td class="texto-suave">{{ $linha->clubes }}</td>
                            <td class="num"><strong>{{ $linha->gols }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="vazio">Sem gols registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <a href="{{ route('estatisticas.artilharia', ['ano' => $ano]) }}">Ver artilharia completa →</a>
        </div>
    </div>
@endsection