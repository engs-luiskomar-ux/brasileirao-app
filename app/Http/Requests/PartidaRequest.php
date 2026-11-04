<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $obrigatorio = $this->isMethod('patch') ? 'sometimes' : 'required';

        return [
            'temporada_id' => ['bail', $obrigatorio, 'integer', 'between:1,2147483647', 'exists:temporada,id_temporada'],
            'rodada' => [$obrigatorio, 'integer', 'between:1,38'],
            'data_partida' => [$obrigatorio, 'date'],
            'mandante_id' => ['bail', $obrigatorio, 'integer', 'between:1,2147483647', 'exists:clube,id_clube'],
            'visitante_id' => ['bail', $obrigatorio, 'integer', 'between:1,2147483647', 'exists:clube,id_clube', 'different:mandante_id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'temporada_id' => 'temporada',
            'data_partida' => 'data da partida',
            'mandante_id' => 'mandante',
            'visitante_id' => 'visitante',
        ];
    }
}
