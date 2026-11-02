@extends('layouts.app')

@section('titulo', 'Jogadores')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Jogadores</h1>
            <p>{{ $jogadores->total() }} jogadores encontrados.</p>
        </div>
        <a href="{{ route('jogadores.create') }}" class="btn">+ Novo jogador</a>
    </div>

    <form method="GET" action="{{ route('jogadores.index') }}" class="card filtros">
        <div class="campo">
            <label for="busca">Nome contém</label>
            <input type="text" id="busca" name="busca" value="{{ request('busca') }}" placeholder="Ex.: Gabriel">
        </div>
        <div class="campo">
            <label for="clube_id">Clube</label>
            <select id="clube_id" name="clube_id">
                <option value="">Todos</option>
                @foreach ($clubes as $clube)
                    <option value="{{ $clube->id_clube }}" @selected(request('clube_id') == $clube->id_clube)>{{ $clube->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="posicao">Posição</label>
            <select id="posicao" name="posicao">
                <option value="">Todas</option>
                @foreach ($posicoes as $posicao)
                    <option value="{{ $posicao }}" @selected(request('posicao') === $posicao)>{{ $posicao }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn">Filtrar</button>
        <a href="{{ route('jogadores.index') }}" class="btn btn-secundario">Limpar</a>
    </form>

    <div class="card tabela-wrap">
        <table>
            <thead>
                <tr><th class="num">Nº</th><th>Jogador</th><th>Posição</th><th>Clube atual</th><th>Nascimento</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($jogadores as $jogador)
                    <tr>
                        <td class="num">{{ $jogador->numero_camisa ?? '—' }}</td>
                        <td><a href="{{ route('jogadores.show', $jogador) }}"><strong>{{ $jogador->nome }}</strong></a></td>
                        <td><span class="tag tag-cinza">{{ $jogador->posicao }}</span></td>
                        <td><a href="{{ route('clubes.show', $jogador->clube_id) }}">{{ $jogador->clube->nome }}</a></td>
                        <td>{{ $jogador->data_nascimento->format('d/m/Y') }}</td>
                        <td class="num"><a href="{{ route('jogadores.edit', $jogador) }}" class="btn btn-secundario btn-pequeno">Editar</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="vazio">Nenhum jogador encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $jogadores->links() }}
    </div>
@endsection
