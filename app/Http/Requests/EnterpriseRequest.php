<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnterpriseRequest extends FormRequest
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
            'is_external' => 'required|integer|in:1,0',
            'names' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|integer',
            'email' => 'required|email|max:255', // Validación de correo electrónico
            'company_contact' => 'nullable|string',
            'credit_days' => 'required|integer|min:1',

            'company_name_invoice' => 'nullable|string',
            'company_rfc_invoice' => 'nullable|string',
            'company_address_invoice' => 'nullable|string',
            'company_email_invoice' => 'nullable|email',            

            'is_invoice_iva' => 'required|integer|in:1,0',
            'is_rates_iva' => 'required|integer|in:1,0',
            'is_foreign' => 'required|integer|in:1,0',
            'currency' => 'required|string|in:MXN,USD',
            'status' => 'required|integer|in:1,0',
            'type_enterprise' => 'required|string|in:PROVIDER,CUSTOMER',
        ];
    }

    public function messages()
    {
        return [
            'names.required' => 'Por favor, ingresa el nombre de la empresa',
            'names.string' => 'El campo nombre de la empresa, debe ser una cadena de texto',
        ];
    }
}
