<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatesEnterpriseRequest extends FormRequest
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
            'destination_id' => 'required',
            'from_id' => 'required',
            'to_id' => 'required',
            'service_id' => 'integer',
            'enterprise_id' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'destination_id.required' => 'El destino requerido',
            'from_id.required' => 'El lugar de origen es requerido',
            'to_id.required' => 'El lugar de destino es requerido',
            'enterprise_id.required' => 'La empresa es requerida',
        ];
    }

}
