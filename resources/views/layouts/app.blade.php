{{-- Layout compartilhado pelas telas que usam @extends('layouts.app'). --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Início') · {{ config('app.name', 'Brasileirão DB') }}</title>
    @if (is_file(public_path('css/app.css')))
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif
</head>
<body>
    @php
        // Cada link aparece quando a rota do módulo estiver cadastrada.
        $menu = [
            'dashboard' => ['Início', 'dashboard'],
            'clubes.index' => ['Clubes', 'clubes.*'],
            'temporadas.index' => ['Temporadas', 'temporadas.*'],
            'classificacao.index' => ['Classificação', 'classificacao.*'],
            'partidas.index' => ['Partidas', 'partidas.*'],
            'jogadores.index' => ['Jogadores', 'jogadores.*'],
            'estatisticas.artilharia' => ['Estatísticas', 'estatisticas.*'],
        ];
    @endphp

    <header class="topo">
        <div class="container topo__conteudo">
            <a href="{{ url('/') }}" class="marca">⚽ Brasileirão<span>DB</span></a>
            <nav class="menu" aria-label="Menu principal">
                @foreach ($menu as $rota => $item)
                    @if (\Illuminate\Support\Facades\Route::has($rota))
                        <a href="{{ route($rota) }}" @class(['ativo' => request()->routeIs($item[1])])>{{ $item[0] }}</a>
                    @endif
                @endforeach
            </nav>
        </div>
    </header>

    <main class="container">
        @includeIf('partials.alertas')
        @yield('conteudo')
    </main>

    <footer class="rodape">
        Engenharia de Software · Centro Universitário Campo Real · Banco de Dados II + Back-end ·
        Luis Antonio, João Vitor, Nelson e Thiago
    </footer>
</body>
</html>
