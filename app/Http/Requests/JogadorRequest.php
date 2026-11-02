<?php

namespace App\Http\Requests;

use App\Jogador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * [Nelson] Validação de jogador (tela e API).
 * Em PATCH (API) os campos viram "sometimes".
 */
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
            'data_nascimento' => [$obrigatorio, 'date', 'before:-15 years', 'after:1950-01-01'],
            'numero_camisa' => ['nullable', 'integer', 'between:1,99'],
            'clube_id' => [$obrigatorio, 'integer', 'exists:clube,id_clube'],
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
        return ['data_nascimento.before' => 'O jogador precisa ter pelo menos 15 anos.'];
    }
}
