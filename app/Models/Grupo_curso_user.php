<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Grupo_curso_user extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'grupos_cursos_users';
    // protected $primaryKey = 'id';
    protected $primaryKey = 'idCargaHoraria';
    public $timestamps = true;
    // protected $guarded = ['id'];
    //protected $guarded = [];
    protected $fillable = [
                            'idCargaHoraria',
                            'grupo_id', 'curso_id', 
                            'user_id', 'hora_inicio',
                            'hora_fin', 'dia',
                        ];
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
     *  Pertenece a un grupo
     */
    public function grupo()
    {
      return $this->belongsTo('App\Models\Grupo', 'grupo_id', 'id_Grupos');
    }

    /**
     *  Pertenece a un curso
     */
    public function curso()
    {
      return $this->belongsTo('App\Models\Curso', 'curso_id', 'id_curso');
    }

     /**
     *  Pertenece a un user (docente)
     */
    public function user()
    {
      return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     *  Pertenece a un user (docente)
     
    public function user()
    {
      return $this->belongsTo('Backpack\CRUD\Tests\Unit\Models\User', 'user_id', 'id');
    }*/


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
