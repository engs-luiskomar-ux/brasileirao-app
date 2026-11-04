<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Thiago - Valida o gol. A partida vem pela URL ou no POST, e gol contra vai para o adversário.
class GolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $obrigatorio = $this->isMethod('patch') ? 'sometimes' : 'required';
        $partidaNoCorpo = $this->isMethod('post') && $this->route('partida') === null;

        return [
            'partida_id' => [Rule::requiredIf($partidaNoCorpo), 'integer', 'exists:partida,id_partida'],
            'jogador_id' => [$obrigatorio, 'integer', 'exists:jogador,id_jogador'],
            'minuto' => [$obrigatorio, 'integer', 'between:0,120'],
            'contra' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['jogador_id' => 'jogador', 'partida_id' => 'partida'];
    }
}