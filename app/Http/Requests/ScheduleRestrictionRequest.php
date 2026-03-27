<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRestrictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:100',
            'is_active' => 'required|integer|in:1,0',
            'start_at'  => 'required|date',
            'end_at'    => 'required|date|after:start_at',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre de la restricción es obligatorio.',
            'name.max'           => 'El nombre no puede superar los 100 caracteres.',
            'is_active.required' => 'El estatus es obligatorio.',
            'is_active.in'       => 'El estatus debe ser Activo o Inactivo.',
            'start_at.required'  => 'La fecha y hora de inicio es obligatoria.',
            'start_at.date'      => 'La fecha de inicio no tiene un formato válido.',
            'end_at.required'    => 'La fecha y hora de fin es obligatoria.',
            'end_at.date'        => 'La fecha de fin no tiene un formato válido.',
            'end_at.after'       => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }
}
