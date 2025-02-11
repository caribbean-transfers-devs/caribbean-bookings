<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnterpriseRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'names' => 'required|string|max:255',
            'is_external' => 'required|integer|in:1,0',
            'status' => 'required|integer|in:1,0',
            'type_enterprise' => 'required|string|in:PROVIDER,CUSTOMER',
        ];
    }

    public function messages()
    {
        return [
            'names.required' => 'Por favor, ingresa el nombre de la empresa',
            'names.string' => 'El campo nombre de la empresa, debe ser una cadena de texto',
        ];
    }
}
