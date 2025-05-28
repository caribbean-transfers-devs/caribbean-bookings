<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'logo' => 'required|url|max:255|starts_with:https://',
            'payment_domain' => 'required|url|max:255|starts_with:https://',
            'color' => ['required', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'transactional_email' => 'required|email:rfc,dns|max:100',
            'transactional_email_send' => 'required|boolean',
            'transactional_phone' => 'required|string|max:20|regex:/^[\d\s\-\+\(\)]{10,20}$/',
            'is_commissionable' => 'required|boolean',
            'is_cxc' => 'required|boolean',
            'is_cxp' => 'required|boolean',
            'success_payment_url' => 'required|url|max:255|starts_with:https://',
            'cancel_payment_url' => 'required|url|max:255|starts_with:https://',
            'type_site' => 'required|string|in:PLATFORM,CALLCENTER,AGENCY,TICKETOFFICE',
            'enterprise_id' => 'required|integer|exists:enterprises,id',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'url' => 'El campo :attribute debe ser una URL válida.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'regex' => 'El formato del campo :attribute no es válido.',
            'in' => 'El valor seleccionado para :attribute no es válido.',
            'exists' => 'La empresa seleccionada no existe.',
            'starts_with' => 'El campo :attribute debe comenzar con https://',

            // Mensajes específicos
            'name.required' => 'Por favor, ingresa el nombre del sitio',
            'logo.required' => 'Por favor, ingresa la URL del logo',
            'logo.starts_with' => 'La URL del logo debe ser segura (https://)',
            'payment_domain.required' => 'Por favor, ingresa el dominio de pago',
            'payment_domain.starts_with' => 'El dominio de pago debe ser seguro (https://)',
            'color.required' => 'Por favor, selecciona un color en formato hexadecimal',
            'color.regex' => 'El color debe ser en formato hexadecimal (ej. #FFFFFF o #FFF)',
            'transactional_email.required' => 'Por favor, ingresa el correo transaccional',
            'transactional_email.email' => 'El correo transaccional debe ser una dirección válida',
            'transactional_email_send.required' => 'Por favor, indica si envía correos transaccionales',
            'transactional_phone.required' => 'Por favor, ingresa el teléfono transaccional',
            'transactional_phone.regex' => 'El teléfono debe contener solo números, espacios y caracteres +-()',
            'is_commissionable.required' => 'Por favor, indica si el sitio es comisionable',
            'is_cxc.required' => 'Por favor, indica si maneja cuentas por cobrar',
            'is_cxp.required' => 'Por favor, indica si maneja cuentas por pagar',
            'success_payment_url.required' => 'Por favor, ingresa la URL para pagos exitosos',
            'success_payment_url.starts_with' => 'La URL de pago exitoso debe ser segura (https://)',
            'cancel_payment_url.required' => 'Por favor, ingresa la URL para pagos cancelados',
            'cancel_payment_url.starts_with' => 'La URL de pago cancelado debe ser segura (https://)',
            'type_site.required' => 'Por favor, selecciona el tipo de sitio',
            'enterprise_id.required' => 'Por favor, selecciona una empresa asociada',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nombre del sitio',
            'logo' => 'logo',
            'payment_domain' => 'dominio de pago',
            'color' => 'color',
            'transactional_email' => 'correo transaccional',
            'transactional_email_send' => 'envío de correos',
            'transactional_phone' => 'teléfono transaccional',
            'is_commissionable' => 'comisionable',
            'is_cxc' => 'cuentas por cobrar',
            'is_cxp' => 'cuentas por pagar',
            'success_payment_url' => 'URL de pago exitoso',
            'cancel_payment_url' => 'URL de pago cancelado',
            'type_site' => 'tipo de sitio',
            'enterprise_id' => 'empresa',
        ];
    }
}