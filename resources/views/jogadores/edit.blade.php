@extends('layouts.app')

@section('titulo', 'Editar '.$jogador->nome)

@section('conteudo')
    <div class="cabecalho"><h1>Editar {{ $jogador->nome }}</h1></div>

    <form method="POST" action="{{ route('jogadores.update', $jogador) }}" class="card form">
        @csrf
        @method('PUT')
        @include('jogadores._form')
        <div class="acoes">
            <button class="btn">Salvar alterações</button>
            <a href="{{ route('jogadores.show', $jogador) }}" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>

    <form method="POST" action="{{ route('jogadores.destroy', $jogador) }}" class="card"
          onsubmit="return confirm('Excluir este jogador? Os gols e cartões dele também serão apagados (ON DELETE CASCADE).')">
        @csrf
        @method('DELETE')
        <button class="btn btn-perigo">Excluir jogador</button>
    </form>
@endsection
