<?php

namespace App;

use Backpack\CRUD\CrudTrait; // <------------------------------- this one
use Spatie\Permission\Traits\HasRoles;// <---------------------- and this one
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Backpack\Base\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;


class User extends Authenticatable
{
    use Notifiable;
    use CrudTrait; // <----- this
    use HasRoles; // <------ and this

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'app', 'apm',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

     /**
   * Send the password reset notification.
   *
   * @param  string  $token
   * @return void
   */
  	public function sendPasswordResetNotification($token)
  	{
    	$this->notify(new ResetPasswordNotification($token));
  	}

    public function escuelasnormales()
    {
      return $this->belongsTo('App\Models\Escuelanormal', 'idescuelanormal', 'id');
    }

    /*  */
    public function getEscuelaId($escuelaNormal)
    {
      return User::where('idescuelanormal', $escuelaNormal)->get();
    }

    /**
     *  Pertenece a una escuela normal 
     */
    public function cursos()
    {
      return $this->belongsTo('App\Models\Curso', 'id_curso', 'id_curso');
    }


    public function getFullNameAttribute(){
	return "{$this->name} {$this->app} {$this->apm}";
    }
    /*
     * Tiene muchos grupos (para el rol administrativo)
     *
    public function grupos(){
    	return $this->hasMany('App\Models\Grupo', 'idadministrativo', 'id');
    }*/
}
