<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WorkingDayRequest extends FormRequest
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
            'name'          => 'required|string|max:250',
            'init_hour'     => 'required',
            'end_hour'      => 'required'

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
            'name.required'             => 'El nombre de la jornada es requerido.',
            'name.string'               => 'El nombre de la jornada debe ser un nombre valido.',
            'name.max'                  => 'El nombre de la jornada debe ser de máximo 50 caracteres.',
            'init_hour.required'        => 'La hora inicial de la jornada es requerida',
            'end_hour.required'         => 'La hora final de la jornada es requerida'
        ];
    }
}
