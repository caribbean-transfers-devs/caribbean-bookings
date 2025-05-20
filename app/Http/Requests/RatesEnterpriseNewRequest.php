<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatesEnterpriseNewRequest extends FormRequest
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
            'enterprise_id' => 'required|integer',
            'destination_service_id' => 'required|integer',
            'destination_service_type' => 'required|in:vehicle,passenger,shared',
            'destination_id' => 'required|integer',
            'zone_one' => 'required|integer',
            'zone_two' => 'required|integer',
        ];

        if ($this->input('destination_service_type') === 'vehicle' || $this->input('destination_service_type') === 'shared') {
            $rules['one_way'] = 'required|numeric|min:1';
            // $rules['round_trip'] = 'required|numeric|min:1';
        }

        if ($this->input('destination_service_type') === 'passenger') {
            $rules['ow_12'] = 'required|numeric|min:1';
            // $rules['rt_12'] = 'required|numeric|min:1';
            $rules['ow_37'] = 'required|numeric|min:1';
            // $rules['rt_37'] = 'required|numeric|min:1';
            $rules['up_8_ow'] = 'required|numeric|min:1';
            // $rules['up_8_rt'] = 'required|numeric|min:1';
            $rules['operating_cost'] = 'required|numeric|min:1';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'enterprise_id.required' => 'La empresa es requerida',
            'destination_service_id.required' => 'El tipo de servicio es requerido',
            'destination_service_type.required' => 'La forma de cobro del tipo de servicio es requerido',
            'destination_id.required' => 'El destino es requerido',
            'zone_one.required' => 'La zona de origen es requerido',
            'zone_two.required' => 'La zona de destino es requerido',
        ];
    }

}