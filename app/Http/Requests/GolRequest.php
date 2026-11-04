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
        $gol = $this->route('gol');
        $precisaTipo = $gol !== null && $gol->contra === null
            && $this->filled('jogador_id') && $this->integer('jogador_id') !== $gol->jogador_id;

        return [
            'partida_id' => [
                'bail', Rule::requiredIf($partidaNoCorpo), 'integer', 'between:1,2147483647', 'exists:partida,id_partida',
                Rule::when($gol !== null, [Rule::in([$gol?->partida_id])]),
            ],
            'jogador_id' => ['bail', $obrigatorio, 'integer', 'between:1,2147483647', 'exists:jogador,id_jogador'],
            'minuto' => [$obrigatorio, 'integer', 'between:0,120'],
            'contra' => [Rule::requiredIf($precisaTipo), 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['jogador_id' => 'jogador', 'partida_id' => 'partida'];
    }

    public function messages(): array
    {
        return [
            'partida_id.in' => 'A partida de um gol já registrado não pode ser alterada.',
            'contra.required' => 'Informe se o gol foi contra ao trocar o jogador de um registro antigo.',
        ];
    }
}
