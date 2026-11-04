<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Thiago - formato JSON de um gol na API
class GolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_gol,
            'partida_id' => $this->partida_id,
            'minuto' => $this->minuto,
            'contra' => $this->contra,
            'jogador' => $this->whenLoaded('jogador', fn () => [
                'id' => $this->jogador->id_jogador,
                'nome' => $this->jogador->nome,
            ]),
            'clube_creditado' => $this->whenLoaded('clube', fn () => [
                'id' => $this->clube->id_clube,
                'nome' => $this->clube->nome,
            ]),
        ];
    }
}
