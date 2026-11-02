<div class="form-grade">
    <div class="campo">
        <label for="nome">Nome *</label>
        <input type="text" id="nome" name="nome" maxlength="100" required value="{{ old('nome', $jogador->nome) }}">
        @error('nome') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="posicao">Posição *</label>
        <select id="posicao" name="posicao" required>
            <option value="">Selecione…</option>
            @foreach ($posicoes as $posicao)
                <option value="{{ $posicao }}" @selected(old('posicao', $jogador->posicao) === $posicao)>{{ $posicao }}</option>
            @endforeach
        </select>
        @error('posicao') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="data_nascimento">Data de nascimento *</label>
        <input type="date" id="data_nascimento" name="data_nascimento" required
               value="{{ old('data_nascimento', $jogador->data_nascimento?->format('Y-m-d')) }}">
        @error('data_nascimento') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="numero_camisa">Número da camisa</label>
        <input type="number" id="numero_camisa" name="numero_camisa" min="1" max="99" value="{{ old('numero_camisa', $jogador->numero_camisa) }}">
        @error('numero_camisa') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="clube_id">Clube *</label>
        <select id="clube_id" name="clube_id" required>
            <option value="">Selecione…</option>
            @foreach ($clubes as $clube)
                <option value="{{ $clube->id_clube }}" @selected(old('clube_id', $jogador->clube_id) == $clube->id_clube)>{{ $clube->nome }}</option>
            @endforeach
        </select>
        @if ($jogador->exists)
            <div class="ajuda">Trocar o clube aqui também gera registro no histórico (trigger).</div>
        @endif
        @error('clube_id') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
</div>
