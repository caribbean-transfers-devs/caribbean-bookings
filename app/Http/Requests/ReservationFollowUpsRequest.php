<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationFollowUpsRequest extends FormRequest
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
            'reservation_id' => 'required|integer|exists:reservations,id',
            'text' => 'required|string|max:500',
            'type' => 'required|string|in:CLIENT,INTERN,OPERATION,HISTORY',
            'name' => 'nullable|string|max:150',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'reservation_id.required' => 'El número de reserva es requerido',
            'reservation_id.integer' => 'El número de reserva debe ser un número entero',
            'reservation_id.exists' => 'El número de reserva no existe',
            'text.required' => 'El texto es requerido',
            'text.string' => 'El texto debe ser una cadena de caracteres',
            'text.max' => 'El texto debe ser mayor a 500 caracteres',
            'type.required' => 'El tipo es requerido',
            'type.string' => 'El tipo debe ser una cadena de caracteres',
            'type.in' => 'El tipo debe ser uno de los siguientes valores: CLIENT,INTERN,OPERATION,HISTORY',
            'name.string' => 'El nombre debe ser una cadena de caracteres',
            'name.max' => 'El nombre debe ser mayor a 150 caracteres',
        ];
    }
}
