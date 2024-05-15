<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosCreateRequest extends FormRequest
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
            'from_zone_id' => 'required',
            'to_zone_id' => 'required',
            'destination_service_id' => 'required',
            'terminal' => 'required|in:T1,T2,T3,T4',
            'sold_in_currency' => 'required|in:MXN,USD',
            'client_first_name' => 'required',
            'client_last_name' => 'required',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string|min:5',
            'vendor_id' => 'required',
            'folio' => 'required',
            'from_name' => 'required',
            'to_name' => 'required',
            'is_round_trip' => 'required|boolean',
            'passengers' => 'required|integer',
            'total' => 'required|numeric',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'from_zone_id.required' => 'Zona origen requerida',
            'to_zone_id.required' => 'Zona destino requerida',
            'destination_service_id.required' => 'Camioneta requerida',
            'terminal.required' => 'Terminal requerida',
            'terminal.in' => 'Sólo se aceptan los siguientes valores: T1,T2,T3,T4',
            'sold_in_currency.required' => 'Moneda con la que se pagó es requerida',
            'sold_in_currency.in' => 'Sólo se acepta MXN,USD para la moneda con la que se pagó',
            'client_first_name.required' => 'Nombre del cliente es requerido',
            'client_last_name.required' => 'Apellido del cliente es requerido',
            'client_email.email' => 'Escribe un email correcto',
            'client_phone.min' => 'Escribe un teléfono correcto',
            'vendor_id.required' => 'El vendedor es requerido',
            'folio.required' => 'El folio es requerido',
            'from_name.required' => 'El destino origen es requerido',
            'to_name.required' => 'El destino es requerido',
            'is_round_trip.required' => 'El tipo de viaje es requerido (Sencillo o Redondo)',
            'is_round_trip.boolean' => 'El tipo de viaje debe ser booleano',
            'passengers.required' => 'El número de pasajeros es requerido',
            'passengers.integer' => 'Escribe un número correcto de pasajeros',
            'total.required' => 'El total es requerido',
            'total.numeric' => 'El total debe ser un número',
        ];
    }

}
