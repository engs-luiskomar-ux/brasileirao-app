@extends('layouts.app')

@section('titulo', 'Classificação'.($ano ? ' '.$ano : ''))

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Classificação {{ $ano }}</h1>
            <p>Critérios: pontos, vitórias, saldo de gols, gols marcados e nome do clube.</p>
        </div>
        <form method="GET" action="{{ route('classificacao.index') }}" class="filtros">
            <div class="campo">
                <label for="ano">Temporada</label>
                <select id="ano" name="ano" onchange="this.form.submit()" @disabled($anos->isEmpty())>
                    @forelse ($anos as $opcao)
                        <option value="{{ $opcao }}" @selected($opcao == $ano)>{{ $opcao }}</option>
                    @empty
                        <option value="">Nenhuma temporada cadastrada</option>
                    @endforelse
                </select>
            </div>
            <button type="submit" class="btn" @disabled($anos->isEmpty())>Ver classificação</button>
        </form>
    </div>

    <div class="card tabela-wrap">
        <table>
            <thead>
                <tr>
                    <th class="num">#</th><th>Clube</th>
                    <th class="num" title="Pontos">P</th><th class="num" title="Jogos">J</th>
                    <th class="num" title="Vitórias">V</th><th class="num" title="Empates">E</th>
                    <th class="num" title="Derrotas">D</th><th class="num" title="Gols pró">GP</th>
                    <th class="num" title="Gols contra">GC</th><th class="num" title="Saldo de gols">SG</th>
                    <th class="num" title="Aproveitamento">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tabela as $linha)
                    <tr @class([
                        'z-libertadores' => $linha->posicao <= 4,
                        'z-rebaixamento' => $linha->posicao > $tabela->count() - 4,
                    ])>
                        <td class="num"><strong>{{ $linha->posicao }}</strong></td>
                        <td><a href="{{ route('clubes.show', $linha->clube_id) }}">{{ $linha->clube }}</a></td>
                        <td class="num"><strong>{{ $linha->pontos }}</strong></td>
                        <td class="num">{{ $linha->jogos }}</td>
                        <td class="num">{{ $linha->vitorias }}</td>
                        <td class="num">{{ $linha->empates }}</td>
                        <td class="num">{{ $linha->derrotas }}</td>
                        <td class="num">{{ $linha->gols_pro }}</td>
                        <td class="num">{{ $linha->gols_contra }}</td>
                        <td class="num">{{ $linha->saldo_gols }}</td>
                        <td class="num">{{ $linha->aproveitamento }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="vazio">
                            {{ $anos->isEmpty() ? 'Nenhuma temporada cadastrada.' : 'Nenhuma partida realizada nesta temporada.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <p class="texto-suave">P: pontos · J: jogos · V: vitórias · E: empates · D: derrotas · GP: gols pró · GC: gols contra · SG: saldo de gols · %: aproveitamento.</p>
        <p class="texto-suave">Faixa verde: G4 (Libertadores) · Faixa vermelha: Z4 (rebaixamento).</p>
    </div>
@endsection
