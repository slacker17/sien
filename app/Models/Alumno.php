<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;


class Alumno extends Model
{
    use CrudTrait;

    public $sufix = '/public';
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'alumnos';
    // protected $primaryKey = 'id';
    protected $primaryKey = 'id_Alumno';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['nombre', 'app', 'apm', 'curp', 'domicilio', 'telefono', 'localidad', 'municipio', 'estado', 'correo', 'capacidadDiferente', 
    'idescuelanormal', 'status', 'matricula', 'celular', 'estado_civil', 'sexo', 'servicio_medico', 'tel_accidente', 'direccion_accidente', 'contacto_accidente', 'cp', 'idgrupo', 'egresado',
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    // Link para añadir alumno al un grupo (utilizado por el administrativo)
    public function addGrupo($crud = false)
    {
        return '<a class="btn btn-xs btn-default" target="_self" href="'.$this->sufix.'/admin/asignaalumnogrupo/'.$this->id_Alumno.'/edit" data-toggle="tooltip">
                <i class="glyphicon glyphicon-plus"></i> Añadir a Grupo</a>';
    }

    // Link para asignar calificaciones y asistencia al alumno
    public function addCalificacion($crud = false)
    {
        // href="/admin/asignaalumnogrupo/'.$this->id_Alumno.'/edit"
        return '<a class="btn btn-xs btn-default" target="_self" data-toggle="tooltip">
                <i class="glyphicon glyphicon-plus"></i> Calificaciones y Asistencia</a>';
    }

    // Link para imprimir formato de inscripcion / re inscripcion
    public function formatoInscripcion($crud = false)
    {
        return '<a class="btn btn-xs btn-default" target="_self" href="'.$this->sufix.'/inscripcionpdf/'.$this->id_Alumno.'" data-toggle="tooltip">
                <i class="glyphicon glyphicon-list-alt"></i> Formato Inscripción</a>';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
     /**
     *  Pertenece a una escuela normal 
     */
    public function escuelasnormales()
    {
      return $this->belongsTo('App\Models\Escuelanormal', 'idescuelanormal', 'id');
    }

     /**
     *  Pertenece a un grupo 
     */
    public function grupos()
    {
      return $this->belongsTo('App\Models\Grupo', 'idgrupo', 'id_Grupos');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
