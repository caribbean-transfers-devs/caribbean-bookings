<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChgPassRequest extends FormRequest
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
            'password' => 'required|string|min:6',
            'confirm_pass' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'El campo contraseña debe tener mínimo 6 caracteres',
            'confirm_pass.required' => 'La confirmación de contraseña es requerida',
            'confirm_pass.same' => 'La confirmación de contraseña debe ser igual a la contraseña, verifique',
        ];
    }
}
