@extends('layouts.app')

@section('titulo', 'Editar '.$temporada->nome)

@section('conteudo')
    <div class="cabecalho"><h1>Editar {{ $temporada->nome }}</h1></div>

    <form method="POST" action="{{ route('temporadas.update', $temporada) }}" class="card form">
        @csrf
        @method('PUT')
        @include('temporadas._form')
        <div class="acoes">
            <button type="submit" class="btn">Salvar alterações</button>
            <a href="{{ route('temporadas.index') }}" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>

    <form method="POST" action="{{ route('temporadas.destroy', $temporada) }}" class="card"
          onsubmit="return confirm('Excluir a temporada {{ $temporada->ano }}?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-perigo">Excluir temporada</button>
        <span class="texto-suave">Temporadas com partidas não podem ser excluídas.</span>
    </form>
@endsection
