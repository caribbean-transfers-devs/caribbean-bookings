<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationConfirmationRequest extends FormRequest
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
            'item_id' => 'required|numeric',
            'terminal_id' => 'required|numeric',
            'lang' => 'required|in:en,es',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'item_id.required' => 'El identificador del item es requerido',
            'terminal_id.required' => 'El identificador de terminal es requerido',
            'lang.required' => 'El idioma es requerido',
            'lang.in' => 'El idioma no es válido',
        ];
    }
}
