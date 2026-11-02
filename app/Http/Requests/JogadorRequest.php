<?php

namespace App\Http\Requests;

use App\Jogador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Rule;


class JogadorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
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
}
