<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ChFavorite;
use App\Models\ChMessage;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function loggedOut(Request $request) {
        return redirect('/login');
    }
    public function invitado_login(){
        //Auth::login(User::where('google_id','=',env('BOT_UNPRG_SECRET_ID') . "invitado_%%" )->first());
        //echo  "aaaa"; die();
        //Crear un nuevo usuario y eliminar al resto de usuarios
        
        date_default_timezone_set('America/Lima');
        $date = date('Y-m-d H:i:s');
        $prev_date = date('Y-m-d H:i:s', strtotime($date .' -1 day'));
//        $usuarios = User::where('tipo_usuario','=','INVITADO')->and('created_at','<=',$prev_date);
        $usuarios = DB::select("SELECT * FROM users WHERE tipo_usuario = 'INVITADO' AND created_at <= '$prev_date'");
        
        foreach ($usuarios as $usuario){
            //Eliminar de favoritos
            //echo $usuario->id; die();
            ChFavorite::where('user_id','=',$usuario->id)->delete();
            ChFavorite::where('favorite_id','=',$usuario->id)->delete();
            ChMessage::where('from_id','=',$usuario->id)->delete();
            ChMessage::where('to_id','=',$usuario->id)->delete();
            //Eliminar de los mensajes
        }
        User::where('tipo_usuario','=','INVITADO')->where('created_at','<=',$prev_date)->delete();
        //AGREGANDO UN NUEVO USUARIO PARA ESA SESIÓN
        //Creando al usuario invitado
        $invitado = new User();
        $invitado->name = "Invitado de Wally";
        $invitado->email = "invitado_wally_soporte".round(microtime(true) * 1000)."@unprg.edu.pe";
        $invitado->avatar = "https://i.ibb.co/fS9Xpkw/user-invitado.png";
        $invitado->link_img = "https://i.ibb.co/fS9Xpkw/user-invitado.png";
        $invitado->google_id = env('BOT_UNPRG_SECRET_ID') .round(microtime(true) * 1000) . "invitado_%%"; //Para eliminar //Por defecto este debería ser el primero
        $invitado->active_status = 1;
        $invitado->tipo_usuario = 'INVITADO';
        $invitado->save();
        Auth::login($invitado);
        //Más configuraciones
        //Cuando se crea el usuario debo agregar un registro a la tabla ch_messages y ch_favorites
        $usuario_nuevo = User::where('google_id',$invitado->google_id)->first();
        $count = round(microtime(true) * 1000) + 1;
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
        $count_messages = round(microtime(true) * 1000) + 1;
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

        $msj_bienvenida->body = "🤖BIENVENIDO a Pedrito BOT - EL BOT de UNPRG🤖\n
        ¡Bep bep! ¡Bop! ¡Estoy vivo! \n
        Hola Visitante <3
        Te ayudaré con las consultas que pueda, si crees que me faltan más preguntas por responder, no dudes en comunicarte con mis desarrrolladores, si crees que puedes hacer crecer el proyecto de igual manera no dudes en comunicarte con mis desarrolladores.😀\n
        Por favor escribe <b>/cmd</b> para darte mi lista de comandos. 
        ";
        $msj_bienvenida->save();

        return redirect('/chatify');
    }
}
