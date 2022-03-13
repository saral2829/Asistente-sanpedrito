<?php

use App\Models\Comando;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ActualizacionComandos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Actualizacion de los comandos, ahora pasan a ser uno, con fe y se entrega en el proyecto
        Schema::table('comandos', function (Blueprint $table) {
            $table->string('comando_padre')->nullable(); //ID del comando padre
            $table->boolean('eliminacion'); //Está eliminado o no ? 

        });
        $comando = new Comando();
        $comando->nombre = "/cmd";
        $comando->tipo_comando = "GRUPAL";
        $comando->descripcion = "Listado de comandos en general.";
        $comando->ruta_archivo = NULL; 
        $comando->tipo_respuesta = "NORMAL";
        $comando->eliminacion = false;
        $comando->respuesta = 'DEVS';
        $comando->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('comandos', function (Blueprint $table) {
            $table->dropColumn('comando_padre');
            $table->dropColumn('eliminacion');
        });
    }
}
