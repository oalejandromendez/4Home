<?php

namespace App\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

class ReserveRequest extends FormRequest
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
        $id = $this->route()->parameter('reserve');
        return [
            'user'              => 'required',
            'customer_address'  => 'required',
            'service'           => 'required',
            'type'              => 'required',
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
            'user.required'                 => 'El cliente es requerido',
            'customer_address.required'     => 'La dirección del cliente es requerida',
            'service.required'              => 'El servicio es requerido',
            'type.required'                 => 'El tipo de servicio es requerido',
            'days.required'                 => 'Los datos de la reserva son requeridos'
        ];
    }
}
