@extends('layouts.app')

@section('titulo', 'Nova partida')

@section('conteudo')
    <div class="cabecalho">
        <div>
            <h1>Nova partida</h1>
            <p>O cadastro chama a procedure <code>sp_registrar_partida</code>, que impede um clube de jogar duas vezes na mesma rodada.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('partidas.store') }}" class="card form">
        @csrf
        @include('partidas._form')
        <div class="acoes">
            <button class="btn">Salvar</button>
            <a href="{{ route('partidas.index') }}" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
@endsection