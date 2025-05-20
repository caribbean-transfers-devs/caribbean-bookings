<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TypesSalesRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:public,private',
        ];
    }

    public function messages()
    {
        return [
            'names.required' => 'Por favor, ingresa el nombre de la venta',
            'names.string' => 'El campo nombre de la venta, debe ser una cadena de texto',
        ];
    }
}
