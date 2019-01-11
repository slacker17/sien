<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Auth;

class Curso extends Model
{
    use CrudTrait;

    public $sufix = '/public';
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'cursos';
    // protected $primaryKey = 'id';
    protected $primaryKey = 'id_curso';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'descripcionCurso',
        'duracionHras',
        'numeroUnidades',
        'semestre',
        'plan_id',
        'licenciatura'
    ];


    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function asignarDocentes($crud = false)
    {
        return '<a class="btn btn-xs btn-default" target="_blank" href="http://google.com?q='.
                urlencode($this->descripcionCurso).
                '" data-toggle="tooltip" title="Asignar docentes."><i class="glyphicon glyphicon-user"></i> Asignar docentes</a>';
    }

    // Link para mostrar llos grupos pertenecientes al docente que da clases
    // usado por el rol docente
    public function verGruposCurso($crud = false)
    {
        return '<a class="btn btn-xs btn-default" target="_self" href="'.$this->sufix.'/admin/grupo?curso_id='.$this->id_curso.'" data-toggle="tooltip" title="Asignar cursos."><i class="glyphicon glyphicon-list-alt"></i> Ver grupos</a>';
        /*return '<a class="btn btn-xs btn-default" target="_blank" href="http://google.com?q='.
                //urlencode($this->descripcion).
                .'" data-toggle="tooltip" title="Asignar cursos."><i class="glyphicon glyphicon-list-alt"></i> Ver grupos</a>';*/
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Tiene muchos Grupos    
    public function grupo()
    {
        return $this->belongsToMany('App\Models\Grupo', 'curso_grupos', 'curso_id', 'grupo_id');
    }

    // Tiene muchos profesores, docentes (users)
    public function profesores(){
        return $this->belongsToMany('App\Models\Profesor', 'curso_users', 'curso_id', 'user_id');
    }

    // Pertenece a un plan de estudios
    public function plan(){
        return $this->belongsTo('App\Models\Planestudio', 'plan_id', 'id');
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

    /* Get full descripcion */
    public function getFullDescripcionCursoAttribute(){
	if(Auth::user()->escuelasnormales->id == 1) // Si es cam                                                                                                        
            return "{$this->descripcionCurso} ( {$this->semestre} semestre, Licenciatura: {$this->licenciatura} )";
        else
            return "{$this->descripcionCurso} ( {$this->semestre} semestre, Plan de estudios: {$this->plan->name} )";
        //return "{$this->descripcionCurso} ( {$this->semestre} semestre, Plan de estudios: {$this->plan->name} )";
    }
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
