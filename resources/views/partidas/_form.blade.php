
<div class="form-grade">
    <div class="campo">
        <label for="temporada_id">Temporada *</label>
        <select id="temporada_id" name="temporada_id" required>
            <option value="">Selecione…</option>
            @foreach ($temporadas as $temporada)
                <option value="{{ $temporada->id_temporada }}" @selected(old('temporada_id', $partida->temporada_id) == $temporada->id_temporada)>{{ $temporada->nome }}</option>
            @endforeach
        </select>
        @error('temporada_id') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="rodada">Rodada *</label>
        <input type="number" id="rodada" name="rodada" min="1" max="38" required value="{{ old('rodada', $partida->rodada) }}">
        @error('rodada') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="data_partida">Data e hora *</label>
        <input type="datetime-local" id="data_partida" name="data_partida" required
               value="{{ old('data_partida', $partida->data_partida?->format('Y-m-d\TH:i')) }}">
        <div class="ajuda">Precisa estar no ano da temporada (trigger trg_partida_valida).</div>
        @error('data_partida') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="mandante_id">Mandante *</label>
        <select id="mandante_id" name="mandante_id" required>
            <option value="">Selecione…</option>
            @foreach ($clubes as $clube)
                <option value="{{ $clube->id_clube }}" @selected(old('mandante_id', $partida->mandante_id) == $clube->id_clube)>{{ $clube->nome }}</option>
            @endforeach
        </select>
        @error('mandante_id') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="visitante_id">Visitante *</label>
        <select id="visitante_id" name="visitante_id" required>
            <option value="">Selecione…</option>
            @foreach ($clubes as $clube)
                <option value="{{ $clube->id_clube }}" @selected(old('visitante_id', $partida->visitante_id) == $clube->id_clube)>{{ $clube->nome }}</option>
            @endforeach
        </select>
        @error('visitante_id') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
</div>