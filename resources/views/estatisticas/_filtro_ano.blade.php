{{-- Seletor de temporada. --}}
<form method="GET" class="filtros">
    <div class="campo">
        <label for="ano">Temporada</label>
        <select id="ano" name="ano" onchange="this.form.submit()">
            <option value="">Todas</option>
            @foreach ($anos as $opcao)
                <option value="{{ $opcao }}" @selected($opcao == $ano)>{{ $opcao }}</option>
            @endforeach
        </select>
    </div>
</form>
