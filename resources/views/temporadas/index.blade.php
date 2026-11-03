@extends('layouts.app')

@section('titulo', 'Temporadas')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Temporadas</h1>
            <p>Consulte as temporadas cadastradas e a quantidade de partidas.</p>
        </div>
        <a href="{{ route('temporadas.create') }}" class="btn">+ Nova temporada</a>
    </div>

    <div class="card tabela-wrap">
        <table>
            <thead><tr><th>Ano</th><th>Nome</th><th class="num">Partidas</th><th>Ações</th></tr></thead>
            <tbody>
                @forelse ($temporadas as $temporada)
                    <tr>
                        <td><strong>{{ $temporada->ano }}</strong></td>
                        <td>{{ $temporada->nome }}</td>
                        <td class="num">{{ $temporada->partidas_count }}</td>
                        <td class="num">
                            <a href="{{ route('classificacao.index', ['ano' => $temporada->ano]) }}" class="btn btn-secundario btn-pequeno">Classificação</a>
                            <a href="{{ route('temporadas.edit', $temporada) }}" class="btn btn-secundario btn-pequeno">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="vazio">Nenhuma temporada cadastrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
