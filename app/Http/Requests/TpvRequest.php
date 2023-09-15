<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TpvRequest extends FormRequest
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
            'code' => 'required',
            'from_name' => 'required',
            'from_lat' => 'required',
            'from_lng' => 'required',
            'to_name' => 'required',
            'to_lat' => 'required',
            'to_lng' => 'required',
            'language' => 'required|in:en,es',
            'passengers' => 'required|integer|min:1|max:35',
            'currency' => 'required|in:USD,MXN',
            'rate_group' => 'required|max:10',
            'pickup' => 'required|date_format:Y-m-d H:i',
            'pickup_departure' => [
                'required_if:round_trip,true|date_format:Y-m-d H:i',
                'date_format:Y-m-d H:i',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'from_name.required' => 'El lugar de origen es requerido',
            'from_lat.required' => 'La latitud de origen es requerida, seleccione correctamente el lugar de origen',
            'from_lng.required' => 'La longitud de origen es requerida, seleccione correctamente el lugar de origen',
            'to_name.required' => 'El lugar de destino es requerido',
            'to_lat.required' => 'La latitud de destino es requerida, seleccione correctamente el lugar de destino',
            'to_lng.required' => 'La longitud de destino es requerida, seleccione correctamente el lugar de destino',
            'language.required' => 'El campo idioma es requerido',
        ];
    }

}
