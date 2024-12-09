<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExchangeRequest extends FormRequest
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
            'exchange' => 'required|numeric|min:0.01',
            'date_init' => 'required|date',
            'date_end' => 'required|date'
        ];
    }

    public function messages()
    {
        return [
            'exchange.required' => 'Por favor, ingresa un monto valido',
            'date_init.required' => 'Por favor, ingresa una fecha valida',
            'date_end.required' => 'Por favor, ingresa una fecha valida',
        ];
    }
}
