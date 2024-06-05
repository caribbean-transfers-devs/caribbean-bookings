<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
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
            'enterprise_id' => 'required|integer|exists:enterprises,id',
            'names' => 'required|string',
            'surnames' => 'required|string',
            'phone' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'enterprise_id.required' => 'Por favor, seleccione una empresa',
            'names.required' => 'Por favor, ingresa el nombre completo del conductor',
            'names.string' => 'El campo nombres debe ser una cadena de texto',
            'surnames.required' => 'Por favor, ingresa los apellidos del conductor',
            'surnames.string' => 'El campo apellidos debe ser una cadena de texto',
            'phone.required' => 'Por favor, ingresa el numero de teléfono',
            'phone.string' => 'El campo de teléfono debe ser una cadena de texto',
        ];
    }
}
