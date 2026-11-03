<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemporadaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $temporada = $this->route('temporada');

        return [
            'ano' => [
                'required', 'integer', 'between:1971,'.(now()->year + 1),
                Rule::unique('temporada', 'ano')->ignore($temporada?->id_temporada, 'id_temporada'),
            ],
            'nome' => ['nullable', 'string', 'max:50'],
        ];
    }
}
