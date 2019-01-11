<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class CursoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|min:5|max:255'
            'descripcionCurso' => 'required|max:120',
            'duracionHras'     => 'integer|nullable',
            'numeroUnidades'   => 'required|integer',
            'semestre'         => 'required',
            'plan_id'          => 'required',
            'licenciatura'     => 'required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'descripcionCurso.required' => 'La descripción es requerida.',
            'duracionHras.required'     => 'La duración en horas es requerida.',
            'numeroUnidades.required'   => 'El número de unidades es requerido.',
            'semestre.required'         => 'El semestre es requerido.',
            'licenciatura.required'     => 'El tipo de licenciatura es requerido.',
            'duracionHras.integer'      => 'La duración en horas debe ser un número entero.',
            'numeroUnidades.integer'    => 'El número de unidades debe ser un número entero.',
            'plan_id.required'          => 'El plan de estudios es requerido.',
        ];
    }
}
