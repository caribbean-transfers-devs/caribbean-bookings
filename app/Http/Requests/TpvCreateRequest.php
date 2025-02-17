<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TpvCreateRequest extends FormRequest
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
            'origin_sale_id' => 'required|integer',
            'service_token' => 'required',
            'first_name' => 'required|max:75',
            'last_name' => 'required|max:75',
            'email_address' => 'required|email|max:85',
            'phone' => 'required',
            'site_id' => 'required|integer',
            'call_center_agent' => 'required|integer',
            'is_quotation' => 'required|integer',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'origin_sale_id.required' => 'Debe seleccionar un origen de venta',
            'service_token.required' => 'Debe seleccionar un servicio',
            'first_name.required' => 'El nombre es requerido',
            'last_name.required' => 'El apellido es requerido',
            'email_address.required' => 'El e-mail es requerido',
            'phone.required' => 'El teléfono es requerido',
            'site_id.required' => 'El sitio es requerido',
            'call_center_agent.required' => 'Debe seleccionar un agente',
            'is_quotation.required' => 'Este parametro es requerido',
        ];
    }

}
