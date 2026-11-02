<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JogadorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_jogador,
            'nome' => $this->nome,
            'posicao' => $this->posicao,
            'data_nascimento' => $this->data_nascimento?->format('Y-m-d'),
            'numero_camisa' => $this->numero_camisa,
            'clube' => $this->whenLoaded('clube', fn () => [
                'id' => $this->clube->id_clube,
                'nome' => $this->clube->nome,
            ]),
        ];
    }
}
