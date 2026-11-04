@extends('layouts.app')

@section('titulo', 'Artilharia')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Artilharia {{ $ano ?? 'geral' }}</h1>
            <p>Calculada por <code>fn_artilharia({{ $ano ?? 'NULL' }}, 30)</code>. Empatados dividem a posição (rank).</p>
        </div>
        @include('estatisticas._filtro_ano')
    </div>

    @include('estatisticas._abas')

    <div class="card tabela-wrap">
        <table>
            <thead><tr><th class="num">#</th><th>Jogador</th><th>Gols pelo(s) clube(s)</th><th class="num">Gols</th></tr></thead>
            <tbody>
                @forelse ($artilheiros as $linha)
                    <tr @class(['destaque' => $linha->posicao == 1])>
                        <td class="num"><strong>{{ $linha->posicao }}º</strong></td>
                        <td><a href="{{ route('jogadores.show', $linha->jogador_id) }}">{{ $linha->jogador }}</a></td>
                        <td class="texto-suave">{{ $linha->clubes }}</td>
                        <td class="num"><strong>{{ $linha->gols }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="vazio">Nenhum gol registrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection