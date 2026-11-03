@extends('layouts.app')

@section('titulo', 'Editar '.$clube->nome)

@section('conteudo')
    <div class="cabecalho"><h1>Editar {{ $clube->nome }}</h1></div>

    <form method="POST" action="{{ route('clubes.update', $clube) }}" class="card form">
        @csrf
        @method('PUT')
        @include('clubes._form')
        <div class="acoes">
            <button type="submit" class="btn">Salvar alterações</button>
            <a href="{{ route('clubes.show', $clube) }}" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>

    <form method="POST" action="{{ route('clubes.destroy', $clube) }}" class="card"
          onsubmit="return confirm('Excluir este clube? Só é possível se ele não tiver jogadores nem partidas.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-perigo">Excluir clube</button>
        <span class="texto-suave">Clubes com jogadores ou partidas vinculados não podem ser excluídos.</span>
    </form>
@endsection
