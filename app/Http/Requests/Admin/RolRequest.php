<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RolRequest extends FormRequest
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
        $id = $this->route()->parameter('role');

        return [
            'name' => 'required|max:50|'. Rule::unique('roles', 'name')->ignore($id, 'id'),
            'permissions' => 'required',
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
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre supera el maximo de caracteres permitido',
            'name.unique' => 'Ya existe el rol digitado',
            'permissions.required' => 'La lista de permisos es requerida',
        ];
    }
}
