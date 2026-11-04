<?php

namespace App\Http\Requests;

use App\Models\Cartao;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Thiago - validação de cartão, antes de chamar a procedure sp_registrar_cartao.
class CartaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jogador_id' => ['bail', 'required', 'integer', 'between:1,2147483647', 'exists:jogador,id_jogador'],
            'minuto' => ['required', 'integer', 'between:0,120'],
            'tipo' => ['required', Rule::in(Cartao::TIPOS)],
        ];
    }

    public function attributes(): array
    {
        return ['jogador_id' => 'jogador', 'tipo' => 'tipo de cartão'];
    }
}
