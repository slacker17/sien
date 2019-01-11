<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalificacionFinal extends Model
{
    protected $table = 'calificaciones_finales';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'alumno_id',
        'curso_id',
        'calificacion_final',
        'ciclo'
    ];

    public function alumno()
    {
        return $this->belongsTo('App\Models\Alumno', 'alumno_id', 'id_Alumno');
    }

    /**
     *  Pertenece a un curso
     */
    public function curso()
    {
      return $this->belongsTo('App\Models\Curso', 'curso_id', 'id_curso');
    }
}
