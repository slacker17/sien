<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Spatie\Permission\Traits\HasRoles;// <---------------------- and this one
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Backpack\CRUD\Tests\Unit\Models\Role;

class AsignaDocenteCurso extends Model
{
    use CrudTrait;
    use Notifiable;
    use HasRoles; // <------ and this
    //use HasRoles; // <------ and this

    public $sufix = '/public';

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'users';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $guarded = [];
    protected $fillable = [];
    //protected $fillable = ['id_curso'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
     // Link para agregar a un grupo
    public function addGrupo($crud = false)
    {
        return '<a class="btn btn-xs btn-default" target="_self" href="'.$this->sufix.'/admin/grupocursouser/create?user_id='.$this->id.'" data-toggle="tooltip">
                <i class="glyphicon glyphicon-plus"></i> Asignar a Grupos</a>';
    }

    public function verHorario($crud = false){
        return '<a class="btn btn-xs btn-default" target="_self" href="'.$this->sufix.'/admin/cargahoraria/'.$this->id.'/1" data-toggle="tooltip">                      
                <i class="glyphicon glyphicon-calendar"></i> Ver Horario</a>';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     *  Tiene un curso
     */
    /*public function cursos()
    {
      return $this->belongsTo('App\Models\Curso', 'id_curso', 'id_curso');
    }*/

    /**
     *  Puede tener muchos cursos asignados
     */
    public function cursos(){
        return $this->belongsToMany('App\Models\Curso', 'curso_users', 'user_id', 'curso_id');
    }

    /**
     * Get the user roles.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_users', 'user_id', 'role_id');
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

/*
 protected $table = 'users';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['id_curso'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     *  Tiene un curso
     
    public function cursos()
    {
      return $this->belongsTo('App\Models\Curso', 'id_curso', 'id_curso');
    }

    /**
     * Get the user roles.
     
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_users', 'user_id', 'role_id');
    }
*/