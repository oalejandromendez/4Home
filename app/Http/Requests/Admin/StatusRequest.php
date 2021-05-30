<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
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
            'colour'        => 'required|string|max:50',
            'openSchedule'  => 'required'
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
            'name.required'             => 'El nombre del estado es requerido.',
            'name.string'               => 'El nombre del estado debe ser un nombre valido.',
            'name.max'                  => 'El nombre del estado debe ser de máximo 250 caracteres.',
            'colour.required'           => 'El color del estado es requerido.',
            'colour.string'             => 'El color del estado debe ser un nombre valido.',
            'colour.max'                => 'El color del estado debe ser de máximo 50 caracteres.',
            'openSchedule.required'     => 'El indicativo de la agenda es requerido.',
        ];
    }
}
