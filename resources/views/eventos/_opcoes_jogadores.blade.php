-- Thiago - lista de jogadores dos dois clubes da partida 
<option value="">Selecione…</option>
<optgroup label="{{ $partida->mandante->nome }} (mandante)">
    @foreach ($sumula['elencoMandante'] as $jogador)
        <option value="{{ $jogador->id_jogador }}">{{ $jogador->numero_camisa ? $jogador->numero_camisa.' · ' : '' }}{{ $jogador->nome }}</option>
    @endforeach
</optgroup>
<optgroup label="{{ $partida->visitante->nome }} (visitante)">
    @foreach ($sumula['elencoVisitante'] as $jogador)
        <option value="{{ $jogador->id_jogador }}">{{ $jogador->numero_camisa ? $jogador->numero_camisa.' · ' : '' }}{{ $jogador->nome }}</option>
    @endforeach
</optgroup>