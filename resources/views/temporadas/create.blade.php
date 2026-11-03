@extends('layouts.app')

@section('titulo', 'Nova temporada')

@section('conteudo')
    <div class="cabecalho"><h1>Nova temporada</h1></div>

    <form method="POST" action="{{ route('temporadas.store') }}" class="card form">
        @csrf
        @include('temporadas._form')
        <div class="acoes">
            <button type="submit" class="btn">Salvar</button>
            <a href="{{ route('temporadas.index') }}" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
@endsection
