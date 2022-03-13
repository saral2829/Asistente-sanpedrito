@extends('layouts.main', ['activePage' => 'gestionar', 'titlePage' => __('Gestionar Comandos')])

@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-info">
                    <h4 class="card-title">Listado de comandos</h4>
                    <p class="card-category">Todos los comandos creados</p>
                    
                </div>
                <div class="card-body">
                    @if(session('mensaje'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{session('mensaje')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                          </button>
                    </div>
                    @endif

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Comando</th>
                                <th>Descripción</th>
                                <th class="text-center">Tipo de comando</th>
                                <th colspan="2">Gestión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comandos as $comando)
                                <tr>
                                    <td class="text-center">{{$comando->id}}</td>
                                    <td><code>{{$comando->nombre}}</code></td>
                                    <td>{{$comando->descripcion}}</td>
                                    <td class="text-center">{{$comando->tipo_comando}}</td>
                                    <td class="td-actions">
                                        @if($comando->id != 1)
                                        <form  style="display: inline-block;" action="{{route('comando.edit_v2', $comando)}}" method="GET">
                                            <button type="submit" rel="tooltip" class="btn btn-info">
                                                <i class="material-icons">edit</i>
                                            </button>
                                        </form>
                                        
                                        <form style="display: inline-block;" action="{{route('comando.destroy', $comando)}}" method="POST">
                                            @csrf
                                            @method('delete')                                           
                                            <button type="submit" rel="tooltip" class="btn btn-danger">
                                                <i class="material-icons">close</i>
                                            </button> 
                                        </form>
                                        @else
                                        <p class="">Sin acción.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            {{--@foreach($personas as $persona)
                                <tr>
                                    <td>{{$persona->apellido_paterno .' '.  $persona->apellido_materno }}</td>
                            <td>{{$persona->nombres}}</td>
                            <td>{{$persona->dni}}</td>
                            <td><a type="button" class="btn btn-warning" href="{{route('persona.edit',$persona->id)}}">Editar</a></td>
                            <form method="POST" action="{{route('persona.destroy',$persona->id)}}">
                                @csrf
                                @method('delete')
                                <td><button type="submit" onclick=" return confirm('¿Estás seguro de eleiminar este registro?');" class="btn btn-danger" href={{route('persona.destroy',$persona->id)}}>Eliminar</button></td>
                            </form>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                    {{--$personas->links()--}}
                </div>
            </div>
            <a class="btn btn-info float-right" href={{route('comando.create')}} type="button"><span class="material-icons">
                            add
                        </span> Nuevo Comando</a>
        </div>
    </div>
</div>
@endsection
