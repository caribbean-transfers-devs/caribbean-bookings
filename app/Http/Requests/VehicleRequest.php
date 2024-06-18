<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
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
            'enterprise_id' => 'required|integer|exists:enterprises,id',
            'destination_service_id' => 'required|integer|exists:destination_services,id',
            'name' => 'required|string',
            'unit_code' => 'required|string',
            'plate_number' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'enterprise_id.required' => 'Por favor, seleccione una empresa',
            'destination_service_id.required' => 'Por favor, selecciona un servicio',
            'name.required' => 'Por favor, ingresa el nombre de la unidad',
            'name.string' => 'El campo nombre de la unidad debe ser una cadena de texto',            
            'unit_code.required' => 'Por favor, ingresa el código de la unidad',
            'unit_code.string' => 'El campo código de la unidad debe ser una cadena de texto',
            'plate_number.required' => 'Por favor, ingresa el número de placa',
            'plate_number.string' => 'El campo número de placa debe ser una cadena de texto',
        ];
    }
}
