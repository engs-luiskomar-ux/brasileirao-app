<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_clube,
            'nome' => $this->nome,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'fundacao' => $this->ano_fundacao,
            'total_jogadores' => $this->whenCounted('jogadores'),
            'links' => [
                'self' => route('api.clubes.show', $this->id_clube),
            ],
        ];
    }
}
