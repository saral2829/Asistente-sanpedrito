<div class="sidebar" data-color="azure" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
    <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
    <div class="logo">
        <a href="{{ route('home') }}" class="simple-text logo-normal">
            <img style="width:25px" src="{{ asset('images') }}/unprg.png" alt="Universidad Nacional Pedro Ruíz Gallo">
            {{ __('UNPRG') }}
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="material-icons">dashboard</i>
                    <p>{{ __('Inicio') }}</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'user-management' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('chatify') }}">
                    <i><img style="width:25px" src="{{ asset('images') }}/fast.svg"></i>
                    <p>{{ __('fast') }}</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'gestionar' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('comando.index') }}">
                    <i><img style="width:25px" src="{{ asset('images') }}/Wally-transparent.svg"></i>
                    <p>{{ __('Wally') }}</p>
                </a>
            </li>
            {{--
            @if(Auth::user()->tipo_usuario == 'ADMIN')
            <li class="nav-item {{ ($activePage == 'profile' || $activePage == 'user-management') ? ' active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#admin" aria-expanded="true">
                    <i><span class="material-icons">
                            supervisor_account
                        </span></i>
                    <p>{{ __('ADMINISTRADOR') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse" id="admin">
                    <ul class="nav">
                        <li class="nav-item{{ $activePage == 'gestionar' ? ' active' : '' }}">
                            <a class="nav-link" href="{{route('comando.index')}}">
                                <i><span class="material-icons">
                                        rule
                                    </span></i>
                                <span class="sidebar-normal"> {{ __('Gestionar comandos') }} </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif
            --}}
            {{--<li class="nav-item{{ $activePage == 'table' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('table') }}">
                <i class="material-icons">content_paste</i>
                <p>{{ __('Table List') }}</p>
            </a>
            </li>
            <li class="nav-item{{ $activePage == 'typography' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('typography') }}">
                    <i class="material-icons">library_books</i>
                    <p>{{ __('Typography') }}</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'icons' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('icons') }}">
                    <i class="material-icons">bubble_chart</i>
                    <p>{{ __('Icons') }}</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'map' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('map') }}">
                    <i class="material-icons">location_ons</i>
                    <p>{{ __('Maps') }}</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'notifications' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('notifications') }}">
                    <i class="material-icons">notifications</i>
                    <p>{{ __('Notifications') }}</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'language' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('language') }}">
                    <i class="material-icons">language</i>
                    <p>{{ __('RTL Support') }}</p>
                </a>
            </li>
            <li class="nav-item active-pro{{ $activePage == 'upgrade' ? ' active' : '' }}">
                <a class="nav-link text-white bg-danger" href="{{ route('upgrade') }}">
                    <i class="material-icons text-white">unarchive</i>
                    <p>{{ __('Upgrade to PRO') }}</p>
                </a>
            </li>--}}
        </ul>
    </div>
</div>
