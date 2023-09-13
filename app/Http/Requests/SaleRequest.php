<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
            'reservation_id' => 'required|exists:reservations,id',
            'sale_type_id' => 'required|exists:sales_types,id',
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric',
            'total' => 'required|numeric',
            'call_center_agent_id' => 'required|exists:users,id'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'reservation_id.required' => 'La reservación es requerida',
            'reservation_id.exists' => 'La reservación no existe',
            'sale_type_id.required' => 'El tipo de venta es requerido',
            'sale_type_id.exists' => 'El tipo de venta no existe',
            'description.required' => 'La descripción es requerida',
            'description.max' => 'La descripción no puede ser mayor a 255 caracteres',
            'quantity.required' => 'La cantidad es requerida',
            'quantity.numeric' => 'La cantidad debe ser un número',
            'total.required' => 'El total es requerido',
            'total.numeric' => 'El total debe ser un número',
            'call_center_agent_id.required' => 'El agente es requerido',
            'call_center_agent_id.exists' => 'El agente no existe'
        ];
    }
}
