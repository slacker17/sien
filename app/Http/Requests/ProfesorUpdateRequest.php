<?php

namespace App\Http\Requests;
use Backpack\PermissionManager\app\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Backpack\CRUD\app\Http\Requests\CrudRequest;

class ProfesorUpdateRequest extends CrudRequest
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
            //'email'         => 'required|unique:'.config('laravel-permission.table_names.users', 'users').',email',
            'email'             => 'required|unique:users,email,'.$this->id,
            'name'              => 'required',
            'password'          => 'confirmed|size:8', // Aveces requerido
            'app'               => 'required|max:50',
            'apm'               => 'required|max:50',
            'curp'              => 'required|size:18|unique:users,curp,'.$this->id,
            'domicilio'         => 'required',
            'telefono'          => 'required|digits:10',
            'idescuelanormal'   => 'required',
            'username'          => 'required|unique:users,username,'.$this->id.'|min:6|max:15',
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
            // REQUIRED
            'name.required'             => 'El nombre es requerido.',
            'email.required'            => 'El correo electrónico es requerido.',
            'password.required'         => 'La contraseña es requerida.',
            'app.required'              => 'El apellido paterno es requerido.',
            'apm.required'              => 'El apellido materno es requerido.',
            'curp.required'             => 'La CURP es requerida.',
            'domicilio.required'        => 'El domicilio es requerido',
            'telefono.required'         => 'El teléfono es requerido.',
            'idescuelanormal.required'  => 'La referencia a escuela normal es requerida.',
            'username.required'         => 'El nombre de usuario es requerida.',
            //'correo.required'       => 'El correo es requerido.',

            // FORMAT
            'telefono.digits'      => 'El teléfono debe ser númerico y de 10 digitos, ejemplo: 2461207461.',
            'email.email'           => 'Correo electrónico inválido',

            // UNIQUE DB
            'email.unique'          => 'El correo electrónico proporcionado ya esta registrado.',
            'curp.unique'           => 'La CURP proporcionada ya esta registrada.',
            'username.unique'       => 'El nombre de usuario proporcionado ya existe, por favor elija otro.',

            // Lenght
	    'curp.size'             => 'La CURP debe ser de 18 caracteres.',
	    'password.size'	    => 'La contraseña debe ser de 8 caracteres.',

            // CONFIRMED
            'password.confirmed'    => 'Las contraseñas no coinciden.',
        ];
    }
}
