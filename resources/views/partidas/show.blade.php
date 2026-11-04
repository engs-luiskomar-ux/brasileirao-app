
@extends('layouts.app')

@section('titulo', $partida->mandante->nome.' × '.$partida->visitante->nome)

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>{{ $partida->temporada->nome }} — {{ $partida->rodada }}ª rodada</h1>
            <p>{{ $partida->data_partida->translatedFormat('l, d/m/Y') }} às {{ $partida->data_partida->format('H:i') }}</p>
        </div>
        <div class="acoes">
            <a href="{{ route('partidas.edit', $partida) }}" class="btn btn-secundario">Editar</a>
            <a href="{{ route('partidas.index', ['temporada_id' => $partida->temporada_id, 'rodada' => $partida->rodada]) }}" class="btn btn-secundario">Rodada</a>
        </div>
    </div>

    <div class="card">
        <div class="placar-grande">
            <a class="time" href="{{ route('clubes.show', $partida->mandante_id) }}">{{ $partida->mandante->nome }}</a>
            @if ($partida->realizada())
                <span class="gols">{{ $partida->gols_mandante }} × {{ $partida->gols_visitante }}</span>
            @else
                <span class="tag tag-ouro">a jogar</span>
            @endif
            <a class="time" href="{{ route('clubes.show', $partida->visitante_id) }}">{{ $partida->visitante->nome }}</a>
        </div>
        <p class="texto-suave" style="text-align:center;margin:0">
            Placar calculado por <code>fn_placar_partida({{ $partida->id_partida }})</code> a partir da tabela de gols.
        </p>
    </div>


    @includeIf('eventos._sumula', ['partida' => $partida])
@endsection
