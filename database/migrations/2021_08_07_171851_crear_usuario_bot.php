<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearUsuarioBot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
        //Solo crear usuario bot
        $usuario = new User();
        $usuario->name = "Wally Bot";
        $usuario->email = "bot@soporte.unprg.edu.pe";
        $usuario->avatar = "https://i.ibb.co/Gp05rs3/Wally-01.png";
        $usuario->link_img = "https://i.ibb.co/Gp05rs3/Wally-01.png";
        $usuario->google_id = env('BOT_UNPRG_SECRET_ID'); //Para eliminar //Por defecto este debería ser el primero
        $usuario->active_status = 1;
        $usuario->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
        $usuario = User::where('google_id','=',env('BOT_UNPRG_SECRET_ID'));
        if(!is_null($usuario)){
            $usuario->delete();
        }        
    }
}
