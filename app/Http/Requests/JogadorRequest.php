<?php

namespace App\Http\Requests;

use App\Jogador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JogadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $obrigatorio = $this->isMethod('patch') ? 'sometimes' : 'required';

        return [
            'nome' => [$obrigatorio, 'string', 'max:100'],
            'posicao' => [$obrigatorio, Rule::in(Jogador::POSICOES)],
            'data_nascimento' => [$obrigatorio, 'date', 'before_or_equal:'.now()->subYears(15)->toDateString(), 'after:1950-01-01'],
            'numero_camisa' => ['nullable', 'integer', 'between:1,99'],
            'clube_id' => ['bail', $obrigatorio, 'integer', 'between:1,2147483647', 'exists:clube,id_clube'],
        ];
    }

    public function attributes(): array
    {
        return [
            'posicao' => 'posição',
            'data_nascimento' => 'data de nascimento',
            'numero_camisa' => 'número da camisa',
            'clube_id' => 'clube',
        ];
    }

    public function messages(): array
    {
        return ['data_nascimento.before_or_equal' => 'O jogador precisa ter pelo menos 15 anos.'];
    }
}
