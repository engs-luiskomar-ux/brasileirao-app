
@extends('layouts.app')

@section('titulo', 'Partidas')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Partidas</h1>
            <p>Placar calculado em cada linha por <code>fn_placar_partida</code>.</p>
        </div>
        <a href="{{ route('partidas.create') }}" class="btn">+ Nova partida</a>
    </div>

    <form method="GET" action="{{ route('partidas.index') }}" class="card filtros">
        <div class="campo">
            <label for="temporada_id">Temporada</label>
            <select id="temporada_id" name="temporada_id">
                <option value="">Todas</option>
                @foreach ($temporadas as $temporada)
                    <option value="{{ $temporada->id_temporada }}" @selected(($filtros['temporada_id'] ?? null) == $temporada->id_temporada)>{{ $temporada->ano }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="rodada">Rodada</label>
            <select id="rodada" name="rodada">
                <option value="">Todas</option>
                @foreach (range(1, 38) as $rodada)
                    <option value="{{ $rodada }}" @selected(($filtros['rodada'] ?? null) == $rodada)>{{ $rodada }}ª</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="clube_id">Clube</label>
            <select id="clube_id" name="clube_id">
                <option value="">Todos</option>
                @foreach ($clubes as $clube)
                    <option value="{{ $clube->id_clube }}" @selected(($filtros['clube_id'] ?? null) == $clube->id_clube)>{{ $clube->nome }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn">Filtrar</button>
    </form>

    <div class="card tabela-wrap">
        <table>
            <thead>
                <tr>
                    <th>Temp.</th><th class="centro">Rod.</th><th>Data</th>
                    <th class="num">Mandante</th><th class="centro">Placar</th><th>Visitante</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($partidas as $partida)
                    <tr>
                        <td>{{ $partida->temporada->ano }}</td>
                        <td class="centro">{{ $partida->rodada }}ª</td>
                        <td>{{ $partida->data_partida->format('d/m/Y H:i') }}</td>
                        <td class="num"><strong>{{ $partida->mandante->nome }}</strong></td>
                        <td class="centro">
                            @if ($partida->realizada())
                                <span class="placar"><b>{{ $partida->gols_mandante }}</b>×<b>{{ $partida->gols_visitante }}</b></span>
                            @else
                                <span class="tag tag-ouro">a jogar</span>
                            @endif
                        </td>
                        <td><strong>{{ $partida->visitante->nome }}</strong></td>
                        <td class="num"><a href="{{ route('partidas.show', $partida) }}" class="btn btn-secundario btn-pequeno">Súmula</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="vazio">Nenhuma partida encontrada com esses filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $partidas->links() }}
    </div>
@endsection