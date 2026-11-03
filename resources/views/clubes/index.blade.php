@extends('layouts.app')

@section('titulo', 'Clubes')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Clubes</h1>
            <p>Consulte os clubes cadastrados e seus elencos.</p>
        </div>
        <div class="acoes">
            <a href="{{ route('clubes.create') }}" class="btn">+ Novo clube</a>
        </div>
    </div>

    <form method="GET" action="{{ route('clubes.index') }}" class="card filtros">
        <div class="campo">
            <label for="busca">Nome começa com</label>
            <input type="text" id="busca" name="busca" value="{{ request('busca') }}" placeholder="Ex.: fla">
        </div>
        <div class="campo">
            <label for="estado">UF</label>
            <select id="estado" name="estado">
                <option value="">Todas</option>
                @foreach ($estados as $uf)
                    <option value="{{ $uf }}" @selected(request('estado') === $uf)>{{ $uf }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn">Filtrar</button>
        <a href="{{ route('clubes.index') }}" class="btn btn-secundario">Limpar</a>
    </form>

    <div class="card tabela-wrap">
        <table>
            <thead>
                <tr>
                    <th>Clube</th>
                    <th>Cidade</th>
                    <th class="centro">UF</th>
                    <th class="num">Fundação</th>
                    <th class="num">Jogadores</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clubes as $clube)
                    <tr>
                        <td><a href="{{ route('clubes.show', $clube) }}"><strong>{{ $clube->nome }}</strong></a></td>
                        <td>{{ $clube->cidade }}</td>
                        <td class="centro"><span class="tag tag-cinza">{{ $clube->estado }}</span></td>
                        <td class="num">{{ $clube->ano_fundacao ?? '—' }}</td>
                        <td class="num">{{ $clube->jogadores_count }}</td>
                        <td class="num">
                            <a href="{{ route('clubes.edit', $clube) }}" class="btn btn-secundario btn-pequeno">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="vazio">Nenhum clube encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $clubes->links() }}
    </div>
@endsection
