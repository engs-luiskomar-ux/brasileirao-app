<div class="acoes" style="margin-bottom:16px">
    <a href="{{ route('estatisticas.artilharia', ['ano' => $ano]) }}" @class(['btn', 'btn-secundario' => ! request()->routeIs('estatisticas.artilharia')])>Artilharia</a>
    <a href="{{ route('estatisticas.cartoes', ['ano' => $ano]) }}" @class(['btn', 'btn-secundario' => ! request()->routeIs('estatisticas.cartoes')])>Cartões</a>
</div>