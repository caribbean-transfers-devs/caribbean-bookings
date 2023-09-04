<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'required|array',
            'restricted' => 'nullable|integer'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es requerido',
            'name.string' => 'El campo nombre debe ser una cadena de texto',
            'name.max' => 'El campo nombre debe tener máximo 255 caracteres',
            'email.required' => 'El campo correo electrónico es requerido',
            'email.email' => 'El campo correo electrónico debe ser un correo electrónico válido',
            'password.string' => 'El campo contraseña debe ser una cadena de texto',
            'password.min' => 'El campo contraseña debe tener mínimo 6 caracteres',
            'password.confirmed' => 'El campo contraseña no coincide con el campo confirmar contraseña',
            'roles.required' => 'Al menos un rol es requerido',
            'roles.array' => 'Los roles deben venir como un arreglo',
            'restricted.integer' => 'El campo restringido debe estar dentro de las opciones'
        ];
    }
}
