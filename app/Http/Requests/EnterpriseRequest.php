<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnterpriseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'is_external' => 'required|boolean',
            'names' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|numeric|digits_between:6,15',
            'email' => 'required|email|max:255',
            'company_contact' => 'nullable|string|max:255',
            'credit_days' => 'required|integer|min:0|max:10000',

            // Datos fiscales
            'company_name_invoice' => 'nullable|string|max:255',
            'company_rfc_invoice' => 'nullable|string|max:13',
            'company_address_invoice' => 'nullable|string|max:255',
            'company_email_invoice' => 'nullable|email|max:255',
            
            // Configuración
            'is_invoice_iva' => 'nullable|boolean',
            'is_rates_iva' => 'nullable|boolean',
            'is_foreign' => 'nullable|boolean',
            'currency' => 'nullable|string|in:MXN,USD',
            'status' => 'nullable|boolean',
            'type_enterprise' => 'required|string|in:PROVIDER,CUSTOMER',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'email' => 'El campo :attribute debe ser una dirección de correo válida.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'phone.digits_between' => 'El teléfono debe tener entre 6 y 15 dígitos.',
            'credit_days.min' => 'Los días de crédito no pueden ser negativos.',
            'credit_days.max' => 'Los días de crédito no pueden exceder 10000.',
            'company_rfc_invoice.min' => 'El RFC debe tener al menos 12 caracteres.',
            'company_rfc_invoice.max' => 'El RFC no debe exceder 13 caracteres.',
            
            // Mensajes específicos
            'names.required' => 'Por favor, ingresa el nombre de la empresa',
            'names.string' => 'El campo nombre de la empresa debe ser una cadena de texto',
        ];
    }

    public function attributes()
    {
        return [
            'names' => 'nombre de la empresa',
            'address' => 'dirección',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'company_contact' => 'contacto de la empresa',
            'credit_days' => 'días de crédito',
            'company_name_invoice' => 'razón social',
            'company_rfc_invoice' => 'RFC',
            'company_address_invoice' => 'dirección fiscal',
            'company_email_invoice' => 'correo fiscal',
        ];
    }
}