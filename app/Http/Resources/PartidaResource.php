<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartidaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_partida,
            'temporada' => $this->whenLoaded('temporada', fn () => $this->temporada->ano),
            'rodada' => $this->rodada,
            'data' => $this->data_partida->format('Y-m-d H:i'),
            'realizada' => $this->realizada,
            'mandante' => $this->whenLoaded('mandante', fn() => [
                'id' => $this->mandante->id_clube,
                'nome' => $this->mandante->nome,
            ]),
            'visitante' => $this->whenLoaded('visitante', fn() => [
                'id' => $this->visitante->id_clube,
                'nome' => $this->visitante->nome,
            ]),
            'placar' => [
                'mandante' => (int) $this->gols_mandante,
                'visitante' => (int) $this->gols_visitante
            ]
        ];
    }
}
