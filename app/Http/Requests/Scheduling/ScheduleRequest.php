<?php

namespace App\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'                => 'required',
            'professional'      => 'required|',
            'supervisor'        => 'required',
            'days'              => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required'                   => 'La reserva es requerida.',
            'professional.required'         => 'El profesional es requerido.',
            'supervisor.required'           => 'El supervisor es requerido.',
            'days.required'                 => 'Los datos de la reserva son requeridos'
        ];
    }
}
