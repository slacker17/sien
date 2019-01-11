<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable()->default(null);
            // Campos personalizados
            $table->string('app')->nullable()->default(null);
            $table->string('apm')->nullable()->default(null);
            $table->string('curp')->nullable()->default(null);
            $table->string('domicilio')->nullable()->default(null);
            $table->string('telefono')->nullable()->default(null);
            $table->string('correo')->nullable()->default(null);
            // Fin campos personalizados
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
