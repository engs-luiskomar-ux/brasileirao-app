<div class="form-grade">
    <div class="campo">
        <label for="ano">Ano *</label>
        <input type="number" id="ano" name="ano" required min="1971" max="{{ now()->year + 1 }}" value="{{ old('ano', $temporada->ano) }}">
        @error('ano') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" maxlength="50" value="{{ old('nome', $temporada->nome) }}" placeholder="Brasileirão {{ now()->year }}">
        <div class="ajuda">Se ficar vazio, será usado "Brasileirão &lt;ano&gt;".</div>
        @error('nome') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
</div>
