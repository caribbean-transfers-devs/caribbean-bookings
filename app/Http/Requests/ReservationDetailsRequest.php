<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationDetailsRequest extends FormRequest
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
            'client_first_name' => 'required|string|max:255',
            'client_last_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:255',
            'currency' => 'required|in:1,2',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'client_first_name.required' => 'El nombre del cliente es requerido',
            'client_first_name.max' => 'El nombre del cliente no puede ser mayor a 255 caracteres',
            'client_last_name.required' => 'El apellido del cliente es requerido',
            'client_last_name.max' => 'El apellido del cliente no puede ser mayor a 255 caracteres',
            'client_email.required' => 'El correo del cliente es requerido',
            'client_email.email' => 'El correo del cliente debe ser un correo válido',
            'client_email.max' => 'El correo del cliente no puede ser mayor a 255 caracteres',
            'client_phone.required' => 'El teléfono del cliente es requerido',
            'client_phone.max' => 'El teléfono del cliente no puede ser mayor a 255 caracteres',
            'currency.required' => 'La moneda es requerida',
            'currency.in' => 'La moneda no es válida',
        ];
    }
}
