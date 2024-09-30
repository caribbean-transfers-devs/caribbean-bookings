<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationItemRequest extends FormRequest
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
            'destination_service_id' => 'required|integer',
            'passengers' => 'required|integer',
            'from_name' => 'required|string|max:255',
            'to_name' => 'required|string|max:255',
            'op_one_pickup' => 'required|date',
            'flight_number' => 'nullable|string|max:255',
        ];
        if( $this->input('serviceTypeForm') && $this->input('serviceTypeForm') == 1 ){
            $rules['op_two_pickup'] = 'required|date';
        }else{
            $rules['op_two_pickup'] = 'nullable|date';
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'destination_service_id.required' => 'El servicio es requerido',
            'destination_service_id.integer' => 'El servicio debe ser uno de la lista',
            'passengers.required' => 'El número de pasajeros es requerido',
            'passengers.integer' => 'El número de pasajeros debe ser un número entero',
            'from_name.required' => 'El nombre de origen es requerido',
            'from_name.string' => 'El nombre de origen debe ser una cadena de caracteres',
            'from_name.max' => 'El nombre de origen debe ser menor a 255 caracteres',
            'to_name.required' => 'El nombre de destino es requerido',
            'to_name.string' => 'El nombre de destino debe ser una cadena de caracteres',
            'to_name.max' => 'El nombre de destino debe ser menor a 255 caracteres',
            'op_one_pickup.required' => 'La fecha de recogida es requerida',
            'op_one_pickup.date' => 'La fecha de recogida debe ser una fecha válida',
            'op_two_pickup.date' => 'La fecha de vuelta debe ser una fecha válida',
            'flight_number.required' => 'El número de vuelo es requerido',
            'flight_number.string' => 'El número de vuelo debe ser una cadena de caracteres',
            'flight_number.max' => 'El número de vuelo debe ser menor a 255 caracteres',
        ];
    }
}
