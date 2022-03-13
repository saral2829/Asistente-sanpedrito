<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use App\Models\ChMessage as Message;
use App\Models\ChFavorite as Favorite;
use App\Models\Comando;
use App\Models\Subcomando;
use Chatify\Facades\ChatifyMessenger as Chatify;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Str;

class MessagesController extends Controller
{
    /**
     * Authinticate the connection for pusher
     *
     * @param Request $request
     * @return void
     */
    public function pusherAuth(Request $request)
    {
        // Auth data
        $authData = json_encode([
            'user_id' => Auth::user()->id,
            'user_info' => [
                'name' => Auth::user()->name
            ]
        ]);
        // check if user authorized
        if (Auth::check()) {
            return Chatify::pusherAuth(
                $request['channel_name'],
                $request['socket_id'],
                $authData
            );
        }
        // if not authorized
        return new Response('Unauthorized', 401);
    }

    /**
     * Returning the view of the app with the required data.
     *
     * @param int $id
     * @return void
     */
    public function index( $id = null)
    {
        $routeName= FacadesRequest::route()->getName();
        $route = (in_array($routeName, ['user', config('chatify.routes.prefix')]))
            ? 'user'
            : $routeName;

        // prepare id
        return view('Chatify::pages.app', [
            'id' => ($id == null) ? 0 : $route . '_' . $id,
            'route' => $route,
            'messengerColor' => Auth::user()->messenger_color,
            'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
        ]);
    }


    /**
     * Fetch data by id for (user/group)
     *
     * @param Request $request
     * @return collection
     */
    public function idFetchData(Request $request)
    {
        // Favorite
        $favorite = Chatify::inFavorite($request['id']);

        // User data
        if ($request['type'] == 'user') {
            $fetch = User::where('id', $request['id'])->first();
        }

        // send the response
        return Response::json([
            'favorite' => $favorite,
            'fetch' => $fetch,
            'user_avatar' => asset('/storage/' . config('chatify.user_avatar.folder') . '/' . $fetch->avatar),
        ]);
    }

    /**
     * This method to make a links for the attachments
     * to be downloadable.
     *
     * @param string $fileName
     * @return void
     */
    public function download($fileName)
    {
        $path = storage_path() . '/app/public/' . config('chatify.attachments.folder') . '/' . $fileName;
        if (file_exists($path)) {
            return Response::download($path, $fileName);
        } else {
            return abort(404, "Lo siento, es posible que el archivo no se encuentre en el servidor.");
        }
    }

    /**
     * Send a message to database
     *
     * @param Request $request
     * @return JSON response
     */
    //Reconfigurar los mensajes ahora 
    public function send(Request $request)
    {
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();
            $allowed_files  = Chatify::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // if size less than 150MB
            if ($file->getSize() < 150000000) {
                if (in_array($file->getClientOriginalExtension(), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment and store the new name
                    $attachment = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $file->storeAs("public/" . config('chatify.attachments.folder'), $attachment);
                } else {
                    $error->status = 1;
                    $error->message = "File extension not allowed!";
                }
            } else {
                $error->status = 1;
                $error->message = "File extension not allowed!";
            }
        }

        if (!$error->status) {
            // send to database
            $messageID = mt_rand(9, 999999999) + time();
            
            Chatify::newMessage([
                'id' => $messageID,
                'type' => $request['type'],
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => ($attachment) ? json_encode((object)[
                    'new_name' => $attachment,
                    'old_name' => $attachment_title,
                ]) : null,
            ]);

            // fetch message to send it with the response
            $messageData = Chatify::fetchMessage($messageID);

            // send to user using pusher
            Chatify::push('private-chatify', 'messaging', [
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'message' => Chatify::messageCard($messageData, 'default')
            ]);
        }
        $id_bot = User::where('google_id',env('BOT_UNPRG_SECRET_ID'))->first()->id;
        $msje_bot = false; 
        $msj = htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8');
        if($id_bot == $request['id']){
            $msje_bot = true;
        }
        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => Chatify::messageCard(@$messageData),
            'tempID' => $request['temporaryMsgId'],
            'msje_bot' => $msje_bot,
            'msj' => $msj,
        ]);
    }
    public function mensajeBot(Request $request){
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        /*AQUI SI LE LLEGÓ AL BOT EL MSJE*/
        $attachment = null;
        $attachment_title = null;
        $msje = htmlentities(trim($request['msj']), ENT_QUOTES, 'UTF-8');
        $id_bot = User::where('google_id',env('BOT_UNPRG_SECRET_ID'))->first()->id;
        //$new_msj-> es la respuesta que dará 
        $existe_archivo = false;
        $no_es_grupal = false;
        $no_cmd = true;
        if($msje == '/cmd'){
            $new_msj = $this->comandos_bot();
            $no_es_grupal = true;
            $no_cmd = false;
        }else{
            $comando = Comando::where('nombre',$msje)->first();
            if(is_null($comando)){
                $new_msj = "¡Opss..!, no reconozco ese comando \n Si necesitas ayuda, el comando '/cmd', podría serte útil.";
            }
            else{
                switch($comando->tipo_comando){
                    case 'SUBCOMANDO':
                        $new_msj = $comando->respuesta;
                        $no_es_grupal = true;
                        break;
                    case 'NORMAL':
                        $new_msj = $comando->respuesta;
                        $new_msj = preg_replace("/((http|https|www)[^\s]+)/", '<a target=”_blank” href="$1">$0</a>', $new_msj);
                        
                        
                        $new_msj= preg_replace("/href=\"www/", 'href="https://www', $new_msj);
                        
                        $c='a-zA-Z-_0-9'; // allowed characters in domainpart
                        $la=preg_quote('!#$%&\'*+-/=?^_`{|}~', "/"); // additional allowed in first localpart
                        $email="[$c$la][$c$la\.]*[^.]@[$c]+\.[$c]+\.[$c]+";
                        //$email = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
                        // or with a link:
                        $new_msj = preg_replace("/\b($email)\b/", '<a href="mailto:\1">\1</a>', $new_msj);
                        //También puede ser que tenga data
                        $no_es_grupal = true;
                        break;
                    case 'GRUPAL':
                        $no_es_grupal = false;
                        $new_msj = $comando->respuesta;
                        break;
                }
            }    
        }
        /*$respuesta_con_links= preg_replace("/((http|https|www)[^\s]+)/", '<a target=”_blank” href="$1">$0</a>', $comando->respuesta);
        //miro si hay enlaces con solamente www, si es así le añado el https://
        $respuesta_con_links= preg_replace("/href=\"www/", 'href="https://www', $respuesta_con_links);
        $comando->respuesta = $respuesta_con_links;*/
        if($no_es_grupal && $no_cmd){
            //Preguntaremos si tiene un archivo de tipo_respuesta
            if($comando->tipo_respuesta != null){
                if($comando->tipo_respuesta == 'ARCHIVO'){
                    //La ruta archivo será el attachment o adjunto
                    $attachment = $comando->ruta_archivo;
                    $attachment_title = $comando->ruta_archivo;
                }
            }
        }else{
            if($no_cmd && !$no_es_grupal){
                //Respuesta de comando grupal, tendría que responder con sus comandos hijos
                $subcomandos = Comando::all()->where('comando_padre','=',$comando->id);
                //echo json_encode($subcomandos);die();
                $respuesta_subcomandos = "🤖Hola, has elegido un comando grupal, con los siguientes subcomandos: \n";
                foreach($subcomandos as $subcom){
                    $respuesta_subcomandos = $respuesta_subcomandos . "<code class='comando_bot' style='cursor:pointer;' >" . $subcom->nombre . "</code>" . " → " . $subcom->descripcion . ".\n";
                }
                $new_msj = $respuesta_subcomandos;
            }
        }
        $rpta_bot_id = mt_rand(9, 999999999) + time();
        Chatify::newMessage([
            'id' => $rpta_bot_id,
            'type' => $request['type'],
            'from_id' => $id_bot,
            'to_id' => Auth::user()->id,
            'type' => 'user',
            'body' => trim($new_msj),
            'attachment' => ($attachment) ? json_encode((object)[
                'new_name' => $attachment,
                'old_name' => $attachment_title,
            ]) : null,
        ]);
        $data_msj_bot = Chatify::fetchMessage($rpta_bot_id);
        Chatify::push('private-chatify', 'messaging', [
            'from_id' => $id_bot,
            'to_id' => Auth::user()->id,
            'message' => Chatify::messageCard($data_msj_bot, 'default')
        ]);
        $msje_bot = false; 
        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => Chatify::messageCard(@$data_msj_bot),
            'tempID' => $request,
            'msje_bot' => $msje_bot,
            'msj' => $request['msj'],
        ]);
    }
    public function comandos_bot(){
        /*return "***🤖 Hola, esta es mi lista de comandos 🤖 ***\n
        /ciclo -> Para saber en que ciclo acádemico nos encontramos\n
        /director -> Para saber el nombre del director de la EPICI\n
        /cocurricular -> Para saber todo acerca los cursos cocurriculares\n
        /devs -> Mira quiénes me desarrollaron\n
        /conocimiento -> ¿Qué tecnologías usaron en mi?\n
        /ingles -> Para saber respecto a los cursos de inglés\n
        /guias -> Consulta las guías actuales\n
        /unprg -> ¿Dónde estudio?\n
        ";*/
        $comandos_normal = Comando::all()->where('tipo_comando', '=', 'NORMAL')->where('comando_padre', '=', '1');
        $comandos_grupal = Comando::all()->where('tipo_comando', '=', 'GRUPAL')->where('comando_padre', '=', '1');;
        
        //$comandos = array_merge($comandos_grupal,$comandos_normal);
        $respuesta = "🤖Hola, mi lista de comandos es :\n ";
        foreach($comandos_normal as $comando){
            $respuesta = $respuesta . "<code class='comando_bot' style='cursor:pointer;' >" . $comando->nombre . "</code>" . " → " . $comando->descripcion . ".\n";
        }
        foreach($comandos_grupal as $comando){
            $respuesta = $respuesta . "<code class='comando_bot' style='cursor:pointer;' >" . $comando->nombre . "</code>" . " → " . $comando->descripcion . ".\n";
        }
        return $respuesta;
    }
    /**
     * fetch [user/group] messages from database
     *
     * @param Request $request
     * @return JSON response
     */
    public function fetch(Request $request)
    {
        // messages variable
        $allMessages = null;

        // fetch messages
        $query = Chatify::fetchMessagesQuery($request['id'])->orderBy('created_at', 'asc');
        $messages = $query->get();

        // if there is a messages
        if ($query->count() > 0) {
            foreach ($messages as $message) {
                $allMessages .= Chatify::messageCard(
                    Chatify::fetchMessage($message->id)
                );
            }
            // send the response
            return Response::json([
                'count' => $query->count(),
                'messages' => $allMessages,
            ]);
        }
        // send the response
        return Response::json([
            'count' => $query->count(),
            'messages' => '<p class="message-hint center-el"><span>Escribe algo para iniciar la conversación</span></p>',
        ]);
    }

    /**
     * Make messages as seen
     *
     * @param Request $request
     * @return void
     */
    public function seen(Request $request)
    {
        // make as seen
        $seen = Chatify::makeSeen($request['id']);
        // send the response
        return Response::json([
            'status' => $seen,
        ], 200);
    }

    /**
     * Get contacts list
     *
     * @param Request $request
     * @return JSON response
     */
    public function getContacts(Request $request)
    {
        // get all users that received/sent message from/to [Auth user]
        $users = Message::join('users',  function ($join) {
            $join->on('ch_messages.from_id', '=', 'users.id')
                ->orOn('ch_messages.to_id', '=', 'users.id');
        })
        ->where(function ($q) {
            $q->where('ch_messages.from_id', Auth::user()->id)
              ->orWhere('ch_messages.to_id', Auth::user()->id);
        })
        ->orderBy('ch_messages.created_at', 'desc')
        ->get()
        ->unique('id');

        $contacts = '<p class="message-hint center-el"><span>Tu lista de contacto está vacía</span></p>';
        $users = $users->where('id','!=',Auth::user()->id);
        if ($users->count() > 0) {
            // fetch contacts
            $contacts = '';
            foreach ($users as $user) {
                if ($user->id != Auth::user()->id) {
                    // Get user data
                    $userCollection = User::where('id', $user->id)->first();
                    $contacts .= Chatify::getContactItem($request['messenger_id'], $userCollection);
                }
            }
        }

        // send the response
        return Response::json([
            'contacts' => $contacts,
        ], 200);
    }

    /**
     * Update user's list item data
     *
     * @param Request $request
     * @return JSON response
     */
    public function updateContactItem(Request $request)
    {
        // Get user data
        $userCollection = User::where('id', $request['user_id'])->first();
        $contactItem = Chatify::getContactItem($request['messenger_id'], $userCollection);

        // send the response
        return Response::json([
            'contactItem' => $contactItem,
        ], 200);
    }

    /**
     * Put a user in the favorites list
     *
     * @param Request $request
     * @return void
     */
    public function favorite(Request $request)
    {
        // check action [star/unstar]
        if (Chatify::inFavorite($request['user_id'])) {
            // UnStar
            Chatify::makeInFavorite($request['user_id'], 0);
            $status = 0;
        } else {
            // Star
            Chatify::makeInFavorite($request['user_id'], 1);
            $status = 1;
        }

        // send the response
        return Response::json([
            'status' => @$status,
        ], 200);
    }

    /**
     * Get favorites list
     *
     * @param Request $request
     * @return void
     */
    public function getFavorites(Request $request)
    {
        $favoritesList = null;
        $favorites = Favorite::where('user_id', Auth::user()->id);
        foreach ($favorites->get() as $favorite) {
            // get user data
            $user = User::where('id', $favorite->favorite_id)->first();
            $favoritesList .= view('Chatify::layouts.favorite', [
                'user' => $user,
            ]);
        }
        // send the response
        return Response::json([
            'count' => $favorites->count(),
            'favorites' => $favorites->count() > 0
                ? $favoritesList
                : 0,
        ], 200);
    }

    /**
     * Search in messenger
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        $getRecords = null;
        $input = trim(filter_var($request['input'], FILTER_SANITIZE_STRING));
        $records = User::where('name', 'LIKE', "%{$input}%");
        foreach ($records->get() as $record) {
            if($record->tipo_usuario != 'INVITADO'){
                $getRecords .= view('Chatify::layouts.listItem', [
                    'get' => 'search_item',
                    'type' => 'user',
                    'user' => $record,
                ])->render();
            }
        }
        // send the response
        return Response::json([
            'records' => $records->count() > 0
                ? $getRecords
                : '<p class="message-hint center-el"><span>Nada para mostrar.</span></p>',
            'addData' => 'html'
        ], 200);
    }

    /**
     * Get shared photos
     *
     * @param Request $request
     * @return void
     */
    public function sharedPhotos(Request $request)
    {
        $shared = Chatify::getSharedPhotos($request['user_id']);
        $sharedPhotos = null;

        // shared with its template
        for ($i = 0; $i < count($shared); $i++) {
            $sharedPhotos .= view('Chatify::layouts.listItem', [
                'get' => 'sharedPhoto',
                'image' => asset('storage/attachments/' . $shared[$i]),
            ])->render();
        }
        // send the response
        return Response::json([
            'shared' => count($shared) > 0 ? $sharedPhotos : '<p class="message-hint"><span>Nada compatido aún</span></p>',
        ], 200);
    }

    /**
     * Delete conversation
     *
     * @param Request $request
     * @return void
     */
    public function deleteConversation(Request $request)
    {
        // delete
        $delete = Chatify::deleteConversation($request['id']);

        // send the response
        return Response::json([
            'deleted' => $delete ? 1 : 0,
        ], 200);
    }

    public function updateSettings(Request $request)
    {
        $msg = null;
        $error = $success = 0;

        // dark mode
        if ($request['dark_mode']) {
            $request['dark_mode'] == "dark"
                ? User::where('id', Auth::user()->id)->update(['dark_mode' => 1])  // Make Dark
                : User::where('id', Auth::user()->id)->update(['dark_mode' => 0]); // Make Light
        }

        // If messenger color selected
        if ($request['messengerColor']) {

            $messenger_color = explode('-', trim(filter_var($request['messengerColor'], FILTER_SANITIZE_STRING)));
            $messenger_color = Chatify::getMessengerColors()[$messenger_color[1]];
            User::where('id', Auth::user()->id)
                ->update(['messenger_color' => $messenger_color]);
        }
        // if there is a [file]
        if ($request->hasFile('avatar')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();

            $file = $request->file('avatar');
            // if size less than 150MB
            if ($file->getSize() < 150000000) {
                if (in_array($file->getClientOriginalExtension(), $allowed_images)) {
                    // delete the older one
                    if (Auth::user()->avatar != config('chatify.user_avatar.default')) {
                        $path = storage_path('app/public/' . config('chatify.user_avatar.folder') . '/' . Auth::user()->avatar);
                        if (file_exists($path)) {
                            @unlink($path);
                        }
                    }
                    // upload
                    $avatar = Str::uuid() . "." . $file->getClientOriginalExtension();
                    $update = User::where('id', Auth::user()->id)->update(['avatar' => $avatar]);
                    $file->storeAs("public/" . config('chatify.user_avatar.folder'), $avatar);
                    $success = $update ? 1 : 0;
                } else {
                    $msg = "¡Archivo no soportado!";
                    $error = 1;
                }
            } else {
                $msg = "¡Tipo de archivo no soportado!";
                $error = 1;
            }
        }

        // send the response
        return Response::json([
            'status' => $success ? 1 : 0,
            'error' => $error ? 1 : 0,
            'message' => $error ? $msg : 0,
        ], 200);
    }

    /**
     * Set user's active status
     *
     * @param Request $request
     * @return void
     */
    public function setActiveStatus(Request $request)
    {
        $update = $request['status'] > 0
            ? User::where('id', $request['user_id'])->update(['active_status' => 1])
            : User::where('id', $request['user_id'])->update(['active_status' => 0]);
        // send the response
        return Response::json([
            'status' => $update,
        ], 200);
    }
}
