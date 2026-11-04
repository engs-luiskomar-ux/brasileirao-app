@extends('layouts.app')

@section('titulo', 'Cartões')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Ranking de cartões {{ $ano ?? 'geral' }}</h1>
            <p>Jogadores mais punidos.</p>
        </div>
        @include('estatisticas._filtro_ano')
    </div>

    @include('estatisticas._abas')

    <div class="card tabela-wrap">
        <table>
            <thead>
                <tr>
                    <th class="num">#</th><th>Jogador</th><th>Clube atual</th>
                    <th class="num"><span class="cartao cartao-amarelo"></span>Amarelos</th>
                    <th class="num"><span class="cartao cartao-vermelho"></span>Vermelhos</th>
                    <th class="num">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ranking as $linha)
                    <tr>
                        <td class="num">{{ $loop->iteration }}º</td>
                        <td><a href="{{ route('jogadores.show', $linha->id_jogador) }}">{{ $linha->jogador }}</a></td>
                        <td class="texto-suave">{{ $linha->clube_atual }}</td>
                        <td class="num">{{ $linha->amarelos }}</td>
                        <td class="num">{{ $linha->vermelhos }}</td>
                        <td class="num"><strong>{{ $linha->total }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="vazio">Nenhum cartão registrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection