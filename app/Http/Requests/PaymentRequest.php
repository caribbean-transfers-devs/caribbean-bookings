<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        $rules = [
            'reservation_id' => 'required|exists:reservations,id',
            'total' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
            'payment_method' => 'required|in:CASH,PAYPAL,CARD,TRANSFER,MIFEL,STRIPE,SANTANDER',
            'currency' => 'required|in:USD,MXN',
            'reference' => 'required|string|max:255',
        ];
        if( $this->input('is_conciliated') && $this->input('is_conciliated') == 1 ){
            $rules['conciliation_comment'] = 'required|string|max:255';
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $rules_messages = [
            'reservation_id.required' => 'La reservación es requerida',
            'reservation_id.exists' => 'La reservación no existe',
            'total.required' => 'El total es requerido',
            'total.numeric' => 'El total debe ser un número',
            'exchange_rate.required' => 'El tipo de cambio es requerido',
            'exchange_rate.numeric' => 'El tipo de cambio debe ser un número',
            'payment_method.required' => 'El método de pago es requerido',
            'payment_method.in' => 'El método de pago no es válido',
            'currency.required' => 'La moneda es requerida',
            'currency.in' => 'La moneda no es válida',
            'reference.required' => 'La referencia es requerida',
            'reference.max' => 'La referencia no puede ser mayor a 255 caracteres',
        ];
        if( $this->input('is_conciliated') && $this->input('is_conciliated') == 1 ){
            $rules_messages['conciliation_comment.required'] = 'Por favor, ingresa un comentario';
            $rules_messages['conciliation_comment.max'] = 'El comentario no puede ser mayor a 255 caracteres';
        }
        return $rules_messages;
    }
}
