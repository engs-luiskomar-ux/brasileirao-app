<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $obrigatorio = $this->isMethod('patch') ? 'sometimes' : 'required';
        $clube = $this->route('clube');

        return [
            'nome' => [
                $obrigatorio, 'string', 'max:100',
                Rule::unique('clube', 'nome')->ignore($clube?->id_clube, 'id_clube'),
            ],
            'cidade' => [$obrigatorio, 'string', 'max:50'],
            'estado' => [$obrigatorio, 'string', 'size:2', 'alpha'],
            'ano_fundacao' => ['nullable', 'integer', 'between:1850,'.now()->year],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome' => 'nome do clube',
            'estado' => 'UF',
            'ano_fundacao' => 'ano de fundação',
        ];
    }
}
