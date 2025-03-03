<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TpvCreateRequest extends FormRequest
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
            'service_token' => 'required',
            'first_name' => 'required|max:75',
            'last_name' => 'max:75',
            'email2' => 'required|email|max:85',
            'phone' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'service_token.required' => 'Debe seleccionar un servicio',
            'first_name.required' => 'El nombre es requerido',
            'last_name.required' => 'El apellido es requerido',
            'email2.required' => 'El e-mail es requerido',
            'phone.required' => 'El teléfono es requerido',
        ];
    }

}
