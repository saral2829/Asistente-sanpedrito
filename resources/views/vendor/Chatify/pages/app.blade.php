@include('Chatify::layouts.headLinks')
<div class="messenger">
    {{-- ----------------------Users/Groups lists side---------------------- --}}
    <div class="messenger-listView">
        {{-- Header and search bar --}}
        <div class="m-header">
            <nav style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                <a href="#" style="display: flex; align-items: center;"><img src="{{ asset('images/icons/icon-fast.ico') }}" alt="" style="width: 25px; margin-right: 5px;"> <span class="messenger-headTitle" style="font-size: 1.5em;">Chats</span> </a>
                {{-- header buttons --}}
                <nav class="m-header-right">
                    <a href="#"><i class="fas fa-cog settings-btn"></i></a>
                    <a href="#" class="listView-x"><i class="fas fa-times"></i></a>
                </nav>
            </nav>
            {{-- Search input --}}
            <input type="text" class="messenger-search" placeholder="@if(Auth::user()->tipo_usuario == 'INVITADO') {{"Función deshabilitada"}} @else {{"Buscar"}} @endif" style="padding: 10px 15px; border-radius: 18px; margin" @if(Auth::user()->tipo_usuario == 'INVITADO')
                {{"disabled"}}
            @endif />
            {{-- Tabs --}}
            <div class="messenger-listView-tabs" style="margin-top:5px;">
                <a href="#" @if($route == 'user') class="active-tab" @endif data-view="users">
                    <span class="far fa-user"></span> Personas</a>
                <a href="#" @if($route == 'group') class="active-tab" @endif data-view="groups">
                    <span class="fas fa-users"></span> Grupos</a>
            </div>
        </div>
        {{-- tabs and lists --}}
        <div class="m-body">
           {{-- Lists [Users/Group] --}}
           {{-- ---------------- [ User Tab ] ---------------- --}}
           <div class="@if($route == 'user') show @endif messenger-tab app-scroll" data-view="users">

               {{-- Favorites --}}
               <div class="favorites-section" style="margin-top: 5px">
                <p class="messenger-title">Favoritos</p>
                <div class="messenger-favorites app-scroll-thin"></div>
               </div>

               {{-- Saved Messages --}}
               {!! view('Chatify::layouts.listItem', ['get' => 'saved','id' => $id])->render() !!}

               {{-- Contact --}}
               <div class="listOfContacts" style="width: 100%;height: calc(100% - 200px);position: relative;"></div>

           </div>

           {{-- ---------------- [ Group Tab ] ---------------- --}}
           <div class="@if($route == 'group') show @endif messenger-tab app-scroll" data-view="groups">
                {{-- items --}}
                <p style="text-align: center;color:grey; margin-top: 10px">Pronto disponible.</p>
             </div>

             {{-- ---------------- [ Search Tab ] ---------------- --}}
           <div class="messenger-tab app-scroll" data-view="search">
                {{-- items --}}
                <p class="messenger-title">Búsqueda</p>
                <div class="search-records">
                    <p class="message-hint center-el"><span>Resultados de búsqueda</span></p>
                </div>
             </div>
        </div>
    </div>

    {{-- ----------------------Messaging side---------------------- --}}
    <div class="messenger-messagingView">
        {{-- header title [conversation name] amd buttons --}}
        <div class="m-header m-header-messaging" style="width: 100%;">
            <nav style="display: flex; justify-content: space-between; align-items:center; width: 100%;">
                {{-- header back button, avatar and user name --}}
                <div style="display: inline-flex; width: 70%">
                    <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a>
                    <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                    </div>
                    <a href="#" class="user-name" style="width: 80%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ config('chatify.name') }}</a>
                </div>
                {{-- header buttons --}}
                <nav class="m-header-right">
                    <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a>
                    <a href="{{ route('home')}}"><i class="fas fa-home"></i></a>
                    <a href="#" class="show-infoSide"><i class="fas fa-info-circle"></i></a>
                </nav>
            </nav>
        </div>
        {{-- Internet connection --}}
        <div class="internet-connection">
            <span class="ic-connected">Conectado</span>
            <span class="ic-connecting">Conectando...</span>
            <span class="ic-noInternet">No tienes acceso a internet</span>
        </div>
        {{-- Messaging area --}}
        <div class="m-body app-scroll">
            <div class="messages">
                <p class="message-hint center-el"><span>Inicia un chat</span></p>
            </div>

            {{-- Typing indicator --}}
            <div class="typing-indicator">
                <div class="message-card typing">
                    <p>
                        <span class="typing-dots">
                            <span class="dot dot-1"></span>
                            <span class="dot dot-2"></span>
                            <span class="dot dot-3"></span>
                        </span>
                    </p>
                </div>
            </div>
            {{-- Send Message Form --}}
            @include('Chatify::layouts.sendForm')
        </div>
    </div>
    {{-- ---------------------- Info side ---------------------- --}}
    <div class="messenger-infoView app-scroll">
        {{-- nav actions --}}
        <nav>
            <a href="#"><i class="fas fa-times"></i></a>
        </nav>
        {!! view('Chatify::layouts.info')->render() !!}
    </div>
</div>
@include('Chatify::layouts.modals')
@include('Chatify::layouts.footerLinks')
