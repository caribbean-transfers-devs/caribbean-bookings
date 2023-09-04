<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidIPRequest extends FormRequest
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
            'ip' => 'required|ip',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'ip.required' => 'La dirección IP es requerida.',
            'ip.ip' => 'La dirección IP no es válida.',
        ];
    }
}
