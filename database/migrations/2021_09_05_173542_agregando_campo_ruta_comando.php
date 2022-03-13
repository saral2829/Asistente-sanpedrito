<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AgregandoCampoRutaComando extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comandos', function (Blueprint $table) {
            $table->string('ruta_archivo')->nullable();
        });
        Schema::table('subcomandos', function (Blueprint $table) {
            $table->string('ruta_archivo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comandos', function (Blueprint $table) {
            $table->dropColumn('ruta_archivo');
        });
        Schema::table('subcomandos', function (Blueprint $table) {
            $table->dropColumn('ruta_archivo');
        });
    }
}
