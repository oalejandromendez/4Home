<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $password = 'required|min:8';
        $id = $this->route()->parameter('user');

        if ($this->method() == 'PUT') {
            $password = '';
        }
        return [
            'email' => 'required|email|' . Rule::unique('users')->ignore($id),
            'password' => $password,
            'name' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'phone' => 'nullable|numeric',
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
            'name.required'     => 'El nombre es requerido.',
            'name.string'       => 'El nombre debe ser un nombre valido.',
            'name.max'          => 'El nombre debe ser de máximo 50 caracteres.',
            'lastname.required' => 'El apellido es requerido.',
            'lastname.string'   => 'El apellido debe ser un apellido valido.',
            'lastname.max'      => 'El apellido debe ser de máximo 50 caracteres.',
            'password.required' => 'La contraseña es requerida',
            'password.min'      => 'La contraseña debe ser mínimo de 8 caracteres',
            'email.required'    => 'El correo es requerido',
            'email.email'       => 'El correo no es valido',
            'email.unique'      => 'El correo ya ha sido registrado'
        ];
    }
}
