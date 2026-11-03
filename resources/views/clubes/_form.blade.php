{{-- Campos compartilhados pelo cadastro e pela edição. --}}
<div class="form-grade">
    <div class="campo">
        <label for="nome">Nome do clube *</label>
        <input type="text" id="nome" name="nome" maxlength="100" required value="{{ old('nome', $clube->nome) }}">
        @error('nome') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="cidade">Cidade *</label>
        <input type="text" id="cidade" name="cidade" maxlength="50" required value="{{ old('cidade', $clube->cidade) }}">
        @error('cidade') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="estado">UF *</label>
        <input type="text" id="estado" name="estado" maxlength="2" required value="{{ old('estado', $clube->estado) }}" placeholder="PR">
        <div class="ajuda">Informe a sigla do estado, por exemplo PR.</div>
        @error('estado') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
    <div class="campo">
        <label for="ano_fundacao">Ano de fundação</label>
        <input type="number" id="ano_fundacao" name="ano_fundacao" min="1850" max="{{ now()->year }}" value="{{ old('ano_fundacao', $clube->ano_fundacao) }}">
        @error('ano_fundacao') <div class="erro-campo">{{ $message }}</div> @enderror
    </div>
</div>
