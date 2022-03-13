<?php

namespace App\Http\Controllers;

use App\Models\ChFavorite;
use App\Models\ChMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }
    public function callback(){
        $usuario = Socialite::driver('google')->user();

        $busca_usuario = User::where('google_id', $usuario->getId())->first();

        if(!is_null($busca_usuario)){
            Auth::login($busca_usuario);
            return redirect('/home');
        }
        $busca_usuario = User::create([
            'name' => $usuario->getName(),
            'email' => $usuario->getEmail(),
            'google_id' => $usuario->getId(),
            'link_img' => $usuario->getAvatar(),
            'avatar' => $usuario->getAvatar(),
        ]);
        Auth::login($busca_usuario);
        //Cuando se crea el usuario debo agregar un registro a la tabla ch_messages y ch_favorites
        $usuario_nuevo = User::where('google_id',$usuario->getId())->first();
        $count = ChFavorite::count() + 1;
        $favorito = new ChFavorite();
        //Preguntar si ya existe el id del chfavorite

        /*do{
            $existe = ChFavorite::where('id',$count)->first();
            $count++;
        }while($existe != null);*/

        $favorito->id = $count; //? 
        $favorito->user_id = $usuario_nuevo->id;
        $favorito->favorite_id = User::where('google_id',env('BOT_UNPRG_SECRET_ID'))->first()->id;
        $favorito->save();
        //Para que aparezca el bot en favoritos
        //Ahora sigue hacer que el bot nos envíe un mensaje
        //$bot = User::where('google_id','PEDRIBOT96361___')->first();
        $count_messages = ChMessage::count() + 1;
        $existe_msj_id = ChMessage::where('id',($count_messages))->first(); 
        /*while(!is_null($existe_msj_id)){
            $count_messages++;
            $existe_msj_id = ChFavorite::where('id',($count_messages+1))->first(); 
        }*/
        $msj_bienvenida = new ChMessage();
        $msj_bienvenida->id = $count_messages;
        $msj_bienvenida->type = 'user';
        $msj_bienvenida->from_id = User::where('google_id',env('BOT_UNPRG_SECRET_ID'))->first()->id;
        $msj_bienvenida->to_id = $usuario_nuevo->id;

        $msj_bienvenida->body = "🤖BIENVENIDO a Pedrito BOT - EL BOT de EPICI🤖\n
        ¡Bep bep! ¡Bop! ¡Estoy vivo! \n
        Hola ".$usuario_nuevo->name ."
        Te ayudaré con las consultas que pueda, si crees que me faltan más preguntas por responder, no dudes en comunicarte con mis desarrrolladores, si crees que puedes hacer crecer el proyecto de igual manera no dudes en comunicarte con mis desarrolladores.😀\n
        Por favor escribe <b>/cmd</b> para darte mi lista de comandos. 
        ";
        $msj_bienvenida->save();
        
        return redirect('/home');
        
    }
}
