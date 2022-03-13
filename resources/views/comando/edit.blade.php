@extends('layouts.main', ['activePage' => 'gestionar', 'titlePage' => __('Gestionar Comandos')])

@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-info">
                    <h4 class="card-title">Agregue un Comando</h4>
                    <p class="card-category">A la Base de Datos</p>
                </div>
                <div class="card-body">
                    <form action={{route('comando.update', $comando)}} method="POST" autocomplete="off" class="form_horizontal">
                        @csrf
                        @method('put')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nombre_comando">Nombre</label>
                            <div class="col-sm-10">
                                <div class="form-group bmd-form-group">
                                    {{-- <span class="input-group-text" id="basic-addon1">/</span> --}}
                                    <input value="{{substr($comando->nombre, 1)}}" type="text" value="{{ old('nombre_comando') }}" class="form-control @error('nombre_comando') is-invalid  @enderror " id="nombre_comando" name="nombre_comando" aria-describedby="basic-addon1" placeholder="Ejemplo: /bot" >
                                    {{--<input type="text" value="{{ old('nombre_comando') }}" name="nombre_comando" class="form-control @error('nombre_comando')
                                    is-invalid
                                    @enderror" id="nombre_comando">--}}
                                    @error('nombre_comando')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                            </div>
                           
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="descripcion">Descripción</label>
                            <div class="col-sm-10">
                                <div class="form-group bmd-form-group">
                                    <input value="{{$comando->descripcion}}" placeholder="Breve descripción del comando" type="text" value="{{ old('descripcion') }}" name="descripcion" class="form-control @error('descripcion')
                                            is-invalid
                                        @enderror" id="descripcion">
                                    @error('descripcion')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                            </div>
                            
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="tipo_comando">Tipo</label>
                            <div class="col-sm-10">
                                <div class="form-group bmd-form-group">
                                    <select value="{{$comando->tipo_comando}}" class="form-control @error('tipo_comando')
                                            is-invalid
                                        @enderror" name="tipo_comando" id="tipo_comando">
                                        <option value="NORMAL" @if(old('tipo_comando')=='NORMAL' ) selected @endif>Normal</option>
                                        <option value="GRUPAL" @if(old('tipo_comando')=='GRUPAL' ) selected @endif>Grupal</option>
                                        <option value="SUBCOMANDO" @if(old('tipo_comando')=='SUBCOMANDO' ) selected @endif>Subcomando</option>
                                    </select>
                                    @error('tipo_comando')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="respuesta">Respuesta</label>
                            <div class="col-sm-10">
                                <div class="form-group bmd-form-group">
                                    <textarea class="form-control" style="" name="respuesta" id="respuesta" placeholder="Instrucciones o respuesta que mostrará PedritoBot...">{{$comando->respuesta}}
                                    </textarea>
                                    {{--<input type="text" value="{{ old('respuesta') }}" name="respuesta" class="form-control @error('respuesta')
                                    is-invalid
                                    @enderror" id="respuesta">--}}
                                    @error('respuesta')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-info">Actualizar<div class="ripple-container"></div></button>
                        </div>
                    </form>
                </div>
            </div>
            <a class="btn btn-danger float-right" href={{route('comando.index')}} type="button"><span class="material-icons">
                arrow_back
            </span> Regresar</a>
        </div>
    </div>
</div>
@endsection
