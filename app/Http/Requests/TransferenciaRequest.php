<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferenciaRequest extends FormRequest
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
        return [
            'clube_destino_id' => ['bail', 'required', 'integer', 'between:1,2147483647', 'exists:clube,id_clube'],
            'numero_camisa' => ['nullable', 'integer', 'between:1,99'],
        ];
    }

    public function attributes(): array
    {
        return ['clube_destino_id' => 'clube de destino', 'numero_camisa' => 'número da camisa'];
    }
}
