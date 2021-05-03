<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
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
            'name'      => 'required|string|max:250',
            'date'      => 'required'
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
            'name.required'             => 'El nombre del festivo es requerido.',
            'name.string'               => 'El nombre del festivo debe ser un nombre valido.',
            'name.max'                  => 'El nombre del festivo debe ser de máximo 50 caracteres.',
            'date.required'             => 'La fecha es requerida'
        ];
    }
}
