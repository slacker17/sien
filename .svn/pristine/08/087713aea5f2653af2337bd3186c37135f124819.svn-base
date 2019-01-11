<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the basic tables 
        Schema::create('Cursos', function(Blueprint $table) {
            $table->increments('id_curso');//->nullable()->default(null);
            $table->string('descripcionCurso', 50)->nullable()->default(null);
            $table->integer('duracionHras')->nullable()->default(null);
            $table->integer('numeroUnidades')->nullable()->default(null);
        
            $table->timestamps();
        
        });

        Schema::create('Grupos', function(Blueprint $table) {
            $table->increments('id_Grupos');//->nullable()->default(null);
            $table->integer('id_profesor')->nullable()->default(null);
            $table->integer('id_curso')->nullable()->default(null);
        
            $table->timestamps();
        
        });

        Schema::create('matriculas', function(Blueprint $table) {
            $table->increments('id_Matriculas');//->nullable()->default(null);
            $table->integer('id_Curso')->nullable()->default(null);
            $table->integer('id_Alumno')->nullable()->default(null);
        
            $table->timestamps();
        
        });

        Schema::create('alumnos', function(Blueprint $table) {
            $table->increments('id_Alumno');
            $table->string('nombre', 60)->nullable()->default(null);
            $table->string('app', 60)->nullable()->default(null);
            $table->string('apm', 60)->nullable()->default(null);
            $table->string('curp', 60)->nullable()->default(null);
            $table->mediumText('domicilio')->nullable()->default(null);
            $table->string('localidad', 60)->nullable()->default(null);
            $table->string('municipio', 60)->nullable()->default(null);
            $table->string('estado', 60)->nullable()->default(null);
            $table->string('correo', 60)->nullable()->default(null);
            $table->string('telefono', 12)->nullable()->default(null);
            $table->string('capacidadDiferente', 5)->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('profesores', function(Blueprint $table) {
            $table->increments('id_profesores');//->nullable()->default(null);
            $table->string('nombre', 60)->nullable()->default(null);
            $table->string('app', 60)->nullable()->default(null);
            $table->string('apm', 60)->nullable()->default(null);
            $table->string('curp', 20)->nullable()->default(null);
            $table->mediumText('domicilio')->nullable()->default(null);
            $table->string('telefono', 12)->nullable()->default(null);
            $table->string('correo', 60)->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('matriculas');
        Schema::drop('Grupos');
        Schema::drop('Cursos');
        Schema::drop('alumnos');
        Schema::drop('profesores');
        
    }
}
