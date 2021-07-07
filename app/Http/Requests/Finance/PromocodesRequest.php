<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class PromocodesRequest extends FormRequest
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
            'amount'        => 'required',
            'reward'        => 'required',
            'expires'       => 'required',
            'quantity'      => 'required',
            'disposable'    => 'required'
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
            'amount.required'           => 'El número de códigos promocionales es requerido.',
            'reward.required'           => 'El porcentaje de descuento es requerido.',
            'expires.required'          => 'La cantidad de días de validez es requerido.',
            'quantity.required'         => 'La cantidad de veces que se puede repetir el código es requerido.',
            'disposable.required'       => 'El código promocional es de un solo uso es requerido',
        ];
    }
}
