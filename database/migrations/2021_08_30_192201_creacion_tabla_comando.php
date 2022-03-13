<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreacionTablaComando extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comandos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); //Para guardar el comando como texto -> por ejemplo /cmd, /clear cosas así
            //$table->string('respuesta');
            $table->string('tipo_respuesta')->nullable();//El tipo de respuesta puede ser texto o archivo, esto lo veré más adelante
            $table->text('respuesta');//La respuesta que el bot piensa dar como mensaje
            $table->enum('tipo_comando',['NORMAL','GRUPAL','SUBCOMANDO']);
            /**
             * NORMAL -> es un comando que tiene una sola funcionalidad, aparece en la lista
             * GRUPAL -> es un comando que tiene subcomandos, este igual aparece en la lista
             * SUBCOMANDO -> este es un comando que forma parte de un grupal, no aparece en lista, se listará cuando alguien  tipee su comando padre
             */
        });
        Schema::create('subcomandos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); //Para guardar el comando como texto -> por ejemplo /cmd, /clear cosas así
            //$table->string('respuesta');
            $table->string('tipo_respuesta')->nullable();//El tipo de respuesta puede ser texto o archivo, esto lo veré más adelante
            $table->string('comando_padre');//El comando padre es al comando al que pertenece
            $table->text('respuesta');//La respuesta que el bot piensa dar como mensaje
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comandos');
        Schema::dropIfExists('subcomandos');
    }
}
