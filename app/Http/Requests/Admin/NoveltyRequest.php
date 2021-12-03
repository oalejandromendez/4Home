<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NoveltyRequest extends FormRequest
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
            'professional' => 'required',
            'type' => 'required',
            'initial_date' => 'required',
            'final_date' => 'required',
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
            'professional.required' => 'El profesional es requerido.',
            'type.required' => 'El tipo es requerido.',
            'initial_date.required' => 'La fecha inicial es requerida.',
            'final_date.required' => 'La fecha final es requerida.',
        ];
    }
}
