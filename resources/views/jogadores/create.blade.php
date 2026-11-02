@extends('layouts.app')

@section('titulo', 'Novo jogador')

@section('conteudo')
    <div class="cabecalho"><h1>Novo jogador</h1></div>

    <form method="POST" action="{{ route('jogadores.store') }}" class="card form">
        @csrf
        @include('jogadores._form')
        <div class="acoes">
            <button class="btn">Salvar</button>
            <a href="{{ route('jogadores.index') }}" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
@endsection
