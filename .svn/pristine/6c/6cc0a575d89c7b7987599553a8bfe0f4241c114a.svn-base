<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class AltaCalificacion extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'calificaciones';
    protected $primaryKey = 'id_calificacion';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['id_alumno', 'id_curso', 'calificacion', 'unidad', 'asistencia'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

     /**
     *  Pertenece a un alumno
     */
    public function alumno()
    {
      return $this->belongsTo('App\Models\Alumno', 'id_alumno', 'id_Alumno');
    }

    /**
     *  Pertenece a un curso
     */
    public function curso()
    {
      return $this->belongsTo('App\Models\Curso', 'id_curso', 'id_curso');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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
