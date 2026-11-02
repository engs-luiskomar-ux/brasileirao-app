@extends('layouts.app')

@section('titulo', 'Editar partida')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Editar partida</h1>
            <p>{{ $partida->mandante->nome }} × {{ $partida->visitante->nome }} — {{ $partida->rodada }}ª rodada</p>
        </div>
    </div>

    <form method="POST" action="{{ route('partidas.update', $partida) }}" class="card form">
        @csrf
        @method('PUT')
        @include('partidas._form')
        <div class="acoes">
            <button class="btn">Salvar alterações</button>
            <a href="{{ route('partidas.show', $partida) }}" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>

    <form method="POST" action="{{ route('partidas.destroy', $partida) }}" class="card"
          onsubmit="return confirm('Excluir esta partida? Os gols e cartões dela também serão apagados.')">
        @csrf
        @method('DELETE')
        <button class="btn btn-perigo">Excluir partida</button>
    </form>
@endsection
