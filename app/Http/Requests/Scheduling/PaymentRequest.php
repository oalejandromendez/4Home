<?php

namespace App\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'type'              => 'required',
            'name'              => 'required',
            'type_document'     => 'required',
            'identification'    => 'required',
            'phone'             => 'required',
            'total'             => 'required'
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
            'type.required'             => 'El tipo de pago es requerido.',
            'name.requied'              => 'El nombre del titular es requerido',
            'type_document.required'    => 'El tipo de documento es requerido',
            'document.required'         => 'El número de documento es requerido',
            'phone.required'            => 'El número de celular es requerido',
            'total.required'            => 'El total a pagar es requerido'
        ];
    }
}
