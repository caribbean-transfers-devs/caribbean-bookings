<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'enterprise_id'     => 'required|integer|exists:enterprises,id',
            'destination_id'    => 'required|integer|exists:destinations,id',
            'name'              => 'required|string|max:255',
            'iata_code'         => 'nullable|string|max:3|uppercase',
            'cut_off'           => 'nullable|numeric|min:0|max:100|regex:/^\d+(\.\d{1,2})?$/',
            'distance'          => 'nullable|string|regex:/^\d+\sKM$/i',
            'time'              => 'nullable|string|regex:/^(\d+\sH)?\s*(\d+\sMin)?$/i',
            'is_primary'        => 'required|boolean',
            'status'            => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            // Mensajes genéricos
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'max' => [
                'string' => 'El campo :attribute no debe exceder :max caracteres.',
                'numeric' => 'El campo :attribute no debe ser mayor que :max.',
            ],
            'min' => [
                'numeric' => 'El campo :attribute debe ser al menos :min.',
            ],
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'regex' => 'El formato del campo :attribute no es válido.',
            'exists' => 'El :attribute seleccionado no existe.',
            'uppercase' => 'El campo :attribute debe estar en mayúsculas.',

            // Mensajes específicos
            'enterprise_id.required' => 'Por favor, selecciona una empresa.',
            'enterprise_id.exists' => 'La empresa seleccionada no existe.',
            
            'destination_id.required' => 'Por favor, selecciona un destino.',
            'destination_id.exists' => 'El destino seleccionado no existe.',
            
            'name.required' => 'Por favor, ingresa el nombre de la zona.',
            'name.max' => 'El nombre de la zona no debe exceder los 255 caracteres.',
            
            'iata_code.required' => 'Por favor, ingresa el código IATA.',
            'iata_code.max' => 'El código IATA no debe exceder 3 caracteres.',
            'iata_code.uppercase' => 'El código IATA debe estar en mayúsculas.',
            
            'cut_off.required' => 'Por favor, ingresa el tiempo de corte.',
            'cut_off.numeric' => 'El tiempo de corte debe ser un número.',
            'cut_off.min' => 'El tiempo de corte no puede ser negativo.',
            'cut_off.max' => 'El tiempo de corte no puede exceder 100.',
            'cut_off.regex' => 'El tiempo de corte debe tener máximo 2 decimales.',
            
            'distance.regex' => 'La distancia debe tener el formato "XX KM" (ej. "38 KM").',
            
            'time.regex' => 'El tiempo debe tener el formato "X H" o "X Min" o "X H Y Min" (ej. "5 H", "35 Min", "3 H 20 Min").',
            
            'is_primary.required' => 'Por favor, indica si es la zona principal.',
            
            'status.required' => 'Por favor, indica el estado de la zona.',
        ];
    }

    public function attributes()
    {
        return [
            'enterprise_id' => 'empresa',
            'destination_id' => 'destino',
            'name' => 'nombre de la zona',
            'iata_code' => 'código IATA',
            'cut_off' => 'tiempo de corte',
            'distance' => 'distancia',
            'time' => 'tiempo',
            'is_primary' => 'zona principal',
            'status' => 'estado',
        ];
    }
}