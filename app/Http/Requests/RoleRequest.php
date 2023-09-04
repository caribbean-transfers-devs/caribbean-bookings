<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'role' => 'required|string|max:80',
            'permits' => 'required|array'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'role.required' => 'El campo rol es requerido',
            'role.string' => 'El campo rol debe ser una cadena de caracteres',
            'role.max' => 'El campo rol debe tener máximo 80 caracteres',
            'permits.required' => 'Al menos un permiso es requerido',
            'permits.array' => 'Debe seleccionar al menos un permiso'
        ];
    }
}
