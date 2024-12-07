<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'logo' => 'required|string|url',
            'payment_domain' => 'required|string|url',
            'color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/'],
            'transactional_email' => 'required|string|max:255',
            'transactional_email_send' => 'required|integer|in:1,0',
            'transactional_phone' => 'required|string',
            'is_commissionable' => 'required|integer|in:1,0',
            'is_cxc' => 'required|integer|in:1,0',
            'is_cxp' => 'required|integer|in:1,0',
            'success_payment_url' => 'required|string',
            'cancel_payment_url' => 'required|string',
            'type_site' => 'required|string|in:PLATFORM,CALLCENTER,AGENCY,TICKETOFFICE',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Por favor, ingresa el nombre del sitio',
            'logo.required' => 'Por favor, ingresa la url del logo',
            'payment_domain.required' => 'Por favor, ingresa el dominio',
            'color.required' => 'Por favor, ingresa un color',

            'transactional_email.required' => 'por favor, ingresa el correo',
            'transactional_email_send.required' => 'Por favor, indica si envia correo',
            'transactional_phone.required' => 'Por favor, ingresa el teléfono',
            'is_commissionable.required' => 'Por favor, indica si es comisionable',
            'is_cxc.required' => 'Por favor, indica si maneja cuentas por cobrar',
            'is_cxp.required' => 'Por favor, indica si maneja cuentas por pagar',

            'success_payment_url.required' => 'Por favor, ingresa la url de pago confirmado',
            'cancel_payment_url.required' => 'Por favor, ingresa la url de pago cancelado',
            'type_site.required' => 'Por favor, indica el tipo de sitio',
        ];
    }
}
