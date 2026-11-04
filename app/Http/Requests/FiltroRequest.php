<?php

namespace App\Http\Requests;

use App\Jogador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'busca' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'size:2', 'alpha'],
            'clube_id' => ['bail', 'nullable', 'integer', 'between:1,2147483647', 'exists:clube,id_clube'],
            'temporada_id' => ['bail', 'nullable', 'integer', 'between:1,2147483647', 'exists:temporada,id_temporada'],
            'partida_id' => ['bail', 'nullable', 'integer', 'between:1,2147483647', 'exists:partida,id_partida'],
            'rodada' => ['nullable', 'integer', 'between:1,38'],
            'posicao' => ['nullable', Rule::in(Jogador::POSICOES)],
            'ano' => ['nullable', 'integer', 'between:1971,'.(now()->year + 1)],
            'limite' => ['nullable', 'integer', 'between:1,100'],
            'page' => ['nullable', 'integer', 'between:1,2147483647'],
        ];
    }
}
