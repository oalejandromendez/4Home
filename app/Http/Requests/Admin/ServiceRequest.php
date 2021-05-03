<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'name'              => 'required|string|max:50',
            'price'             => 'required',
            'working_day'       => 'required',
            'quantity'          => 'required',
            'type'              => 'required'
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
            'name.required'             => 'El nombre del servicio es requerido.',
            'name.string'               => 'El nombre del servicio debe ser un nombre valido.',
            'name.max'                  => 'El nombre del servicio debe ser de máximo 50 caracteres.',
            'price.required'            => 'El precio del servicio es requerido',
            'working_day.required'      => 'La jornada es requerida',
            'quantity.required'         => 'La cantidad es requerida',
            'type.required'             => 'El tipo es requerido',
        ];
    }
}
