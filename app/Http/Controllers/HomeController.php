<?php

namespace App\Http\Controllers;

use App\Models\Comando;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->tipo_usuario == 'ADMIN'){
            //Retornar ahora con variables para usar
            $usuarios = User::all();
            $comandos = Comando::all();
            $total_usuarios = count($usuarios);
            $total_comandos = count($comandos);
            //Usuarios generales y admins
            /*$usuarios_general = Array();
            $usuarios_admin = Array();
            foreach($usuarios as $usuario){
                if($usuario->tipo_usuario == 'ADMIN'){
                    array_push($usuarios_admin,$usuario);
                }else{
                    array_push($usuarios_general,$usuario);
                }
            }*/
            $usuarios_general = User::all()->where('tipo_usuario','=',NULL);
            $usuarios_admin = User::all()->where('tipo_usuario','=','ADMIN');
            return view('inicio',["total_usuarios" => $total_usuarios, "total_comandos" => $total_comandos, "comandos" => $comandos,
            "usuarios_general" => $usuarios_general, "usuarios_admin" => $usuarios_admin]);
        }else{
            return redirect('chatify');
        } 
        
        //return redirect()->route('chatify');
    }
    public function index2(){
        return view('home');
    }
    public function invitado_login(){
        //retorna vista igual xd 
        //Auth::login(User::where('google_id','=',env('BOT_UNPRG_SECRET_ID') . "invitado_%%" ));
        //echo  "aaaa"; die();
        //return redirect('/chatify');
    }
}
