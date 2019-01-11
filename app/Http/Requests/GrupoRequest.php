<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Auth; 
class GrupoRequest extends FormRequest
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
	$validators = [
            'semestre'     => 'required',
            'grupo'        => 'required',
            'idescuelanormal' => 'required',
        ];

        if( !Auth::user()->idescuelanormal == 1 && Auth::user()->hasRole('SUBDIRECTOR ACADÉMICO') )
            $validators['plan_id'] = 'required';

	return $validators;
        /*
        return [
            // 'name' => 'required|min:5|max:255'
            //'descripcion'     => 'required|max:30|min:2',
            'semestre'     => 'required',
            'grupo'        => 'required',
            'idescuelanormal' => 'required',
            'plan_id' => 'required',
            //'curso' => 'required',
            //'idcicloescolar' => 'required',
        ];*/
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
            // Required
            //'descripcion.required'  => 'El nombre de grupo es requerido.',
            //'curso.required' => 'Por favor seleccione los cursos que tendrá el grupo.',
            'semestre.required' => 'Por favor ingresa el semestre.',
            'grupo.required' => 'Por favor ingresa un grupo.',
            'plan_id.required' => 'Por favor el plan de estudios es requerido.', 
            //'idcicloescolar.required' => 'Por favor seleccione un ciclo escolar.', 
            //'id_profesor.required'  => 'Es neceario seleccionar al docente que impartirá el curso.',
            //'id_curso.required'     => 'Es necesario seleccionar el curso.',
        ];
    }
}
