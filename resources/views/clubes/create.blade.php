@extends('layouts.app')

@section('titulo', 'Novo clube')

@section('conteudo')
    <div class="cabecalho"><h1>Novo clube</h1></div>

    <form method="POST" action="{{ route('clubes.store') }}" class="card form">
        @csrf
        @include('clubes._form')
        <div class="acoes">
            <button type="submit" class="btn">Salvar</button>
            <a href="{{ route('clubes.index') }}" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
@endsection
