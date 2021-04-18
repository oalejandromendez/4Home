<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfessionalRequest extends FormRequest
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
        $id = $this->route()->parameter('professional');
        return [
            'identification'    => 'required|' . Rule::unique('professional', 'identification')->ignore($id),
            'name'              => 'required|string|max:50',
            'lastname'          => 'required|string|max:50',
            'phone'             => 'required',
            'phone_contact'     => 'required',
            'salary'            => 'required',
            'email'             => 'required|email|' . Rule::unique('professional', 'email')->ignore($id),
            'photo'             => 'required'
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
            'identification.unique'     => 'El numero de identificación ya ha sido registrado',
            'identification.required'   => 'El numero de identificación es requerido',
            'name.required'             => 'El nombre es requerido.',
            'name.string'               => 'El nombre debe ser un nombre valido.',
            'name.max'                  => 'El nombre debe ser de máximo 50 caracteres.',
            'lastname.required'         => 'El apellido es requerido.',
            'lastname.string'           => 'El apellido debe ser un apellido valido.',
            'lastname.max'              => 'El apellido debe ser de máximo 50 caracteres.',
            'email.required'            => 'El correo es requerido',
            'email.email'               => 'El correo no es valido',
            'email.unique'              => 'El correo ya ha sido registrado',
            'phone.required'            => 'El numero de telefono es requerido',
            'phone_contact.required'    => 'El numero de telefono de contacto es requerido',
            'salary.required'           => 'El salario es requerido',
            'photo.required'            => 'La foto es requerida'
        ];
    }
}
