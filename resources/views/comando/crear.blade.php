@extends('layouts.main', ['activePage' => 'gestionar', 'titlePage' => __('Gestionar Comandos')])

@section('content')
<style type="text/css">
    /* Tooltip container */
    .tooltip {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black;
        /* If you want dots under the hoverable text */
    }

    /* Tooltip text */
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #555;
        color: #fff;
        text-align: center;
        padding: 5px 0;
        border-radius: 6px;

        /* Position the tooltip text */
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;

        /* Fade in tooltip */
        opacity: 0;
        transition: opacity 0.3s;
    }

    /* Tooltip arrow */
    .tooltip .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    /* Show the tooltip text when you mouse over the tooltip container */
    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

</style>
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-info">
                    <h4 class="card-title">Agregue un Comando</h4>
                    <p class="card-category">A la Base de Datos</p>
                </div>
                <div class="card-body">
                    <form action={{route('comando.store')}} method="POST" autocomplete="off" class="form_horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nombre_comando">Nombre</label>
                            <div class="col-sm-10">
                                <div class="form-group bmd-form-group">
                                    {{-- <span class="input-group-text" id="basic-addon1">/</span> --}}
                                    <input type="text" value="{{ old('nombre_comando') }}" class="form-control @error('nombre_comando') is-invalid  @enderror " id="nombre_comando" name="nombre_comando" aria-describedby="basic-addon1" placeholder="Ejemplo: /bot">
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
                                    <input placeholder="Breve descripción del comando" type="text" value="{{ old('descripcion') }}" name="descripcion" class="form-control @error('descripcion')
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
                                    <select class="form-control @error('tipo_comando')
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
                            <label class="col-sm-2 col-form-label" for="tipo_respuesta">Tipo de Respuesta</label>
                            <div class="col-sm-10">
                                <div class="form-group bmd-form-group">
                                    <select class="form-control @error('tipo_respuesta')
                                            is-invalid
                                        @enderror" name="tipo_respuesta" id="tipo_respuesta">
                                        <option value="NORMAL" @if(old('tipo_respuesta')=='NORMAL' ) selected @endif>Normal</option>
                                        <option value="ARCHIVO" @if(old('tipo_respuesta')=='ARCHIVO' ) selected @endif>Archivo</option>
                                    </select>
                                    @error('tipo_respuesta')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 d-none" id="div_archivo">
                            <label class="col-sm-2 col-form-label" for="archivo_respuesta">Elige el archivo
                            </label>

                            <div class="col-sm-10">
                                <div>
                                    <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Pedrito automáticamente genererá el link del archivo subido, agrega una respuesta para poder ser agregado en el mensaje de respuesta.">
                                        <span class="material-icons">
                                            info
                                        </span>
                                    </button>
                                </div>
                                <div>
                                    <span class="btn btn-raised btn-round btn-default btn-file">
                                        <span class="fileinput-new d-block" id="sel_fil">Selecciona un archivo</span>
                                        <span class="fileinput-exists d-none" id="change_file">Cambiar archivo</span>
                                        <input type="file" id="archivo_respuesta" name="archivo_respuesta" />
                                        {{-- El archivo se subirá al servidor, el bot generará el link y lo devolverá--}}
                                    </span>
                                    <button id="btn_quitar_archivo" class="btn btn-danger btn-round"><i class="fa fa-times"></i> Quitar archivo</a></button>
                                </div>
                            </div>
                        </div>
                        {{-- Elegir comando padre a los subcomandos --}}
                        <div class="row mb-3 d-none" id="div_comandos_padre">
                            <label class="col-sm-2 col-form-label" for="comando_padre">Comando Padre</label>
                            <div class="col-sm-10">
                                <div class="form-group bmd-form-group">
                                    <select class="form-control @error('comando_padre')
                                            is-invalid
                                        @enderror" name="comando_padre" id="comando_padre">
                                        <option value="no_select" @if(old('comando_padre')=='no_select' ) selected @endif>Sin selección</option>
                                        {{-- Esta vista regresa con el listado de comandos padre --}}
                                        @foreach($comandos_padre as $comando)
                                            <option value="{{$comando->id}}" @if(old('comando_padre')==$comando->id ) selected @endif>{{$comando->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('comando_padre')
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
                                    <textarea class="form-control" style="" value="{{ old('respuesta') }}" name="respuesta" id="respuesta" placeholder="Instrucciones o respuesta que mostrará PedritoBot...">{{ old('respuesta') }}</textarea>
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
                            <button type="submit" class="btn btn-info">Agregar<div class="ripple-container"></div></button>
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
@push('js')
<script type="text/javascript">
    //Script comandos padre 
    document.getElementById('tipo_comando').addEventListener('change', function(){
        if(document.getElementById('tipo_comando').value == 'GRUPAL'){
            //Si es grupal, el comando obviamente no puede responder con un archivo
            document.getElementById('tipo_respuesta').value = 'NORMAL';
            document.getElementById('tipo_respuesta').disabled = true;
            document.getElementById('tipo_respuesta').dispatchEvent(new Event("change"));
            document.getElementById('div_comandos_padre').classList.add('d-none');
            document.getElementById('div_comandos_padre').classList.remove('d-block');
        }else{
            if(document.getElementById('tipo_comando').value == 'SUBCOMANDO'){
                document.getElementById('div_comandos_padre').classList.remove('d-none');
                document.getElementById('div_comandos_padre').classList.add('d-block');
            }else{
                document.getElementById('div_comandos_padre').classList.add('d-none');
                document.getElementById('div_comandos_padre').classList.remove('d-block');
            }
            document.getElementById('tipo_respuesta').value = 'NORMAL';
            document.getElementById('tipo_respuesta').disabled = false;        
        }
    });
    document.getElementById('btn_quitar_archivo').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('archivo_respuesta').value = null;
        document.getElementById('sel_fil').classList.add('d-block');
        document.getElementById('sel_fil').classList.remove('d-none');
        document.getElementById('change_file').classList.add('d-none');
        document.getElementById('change_file').classList.remove('d-block');
    });
    document.getElementById('archivo_respuesta').addEventListener('change', function() {
        if (document.getElementById('archivo_respuesta').value != null) {
            document.getElementById('sel_fil').classList.remove('d-block');
            document.getElementById('sel_fil').classList.add('d-none');
            document.getElementById('change_file').classList.remove('d-none');
            document.getElementById('change_file').classList.add('d-block');

        } else {
            document.getElementById('sel_fil').classList.add('d-block');
            document.getElementById('sel_fil').classList.remove('d-none');
            document.getElementById('change_file').classList.add('d-none');
            document.getElementById('change_file').classList.remove('d-block');
        }
    });
    //Scripts de la page 
    document.getElementById('tipo_respuesta').addEventListener('change', function() {
        if (document.getElementById('tipo_respuesta').value == 'ARCHIVO') {
            //Se activa el div para archivos
            document.getElementById('div_archivo').classList.remove('d-none');
            document.getElementById('div_archivo').classList.add('d-block');
        } else {
            //Se oculta el div para archivos
            document.getElementById('div_archivo').classList.remove('d-block');
            document.getElementById('div_archivo').classList.add('d-none');
            document.getElementById('archivo_respuesta').value = null;
        }
    });
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('tipo_comando').dispatchEvent(new Event("change"));
        document.getElementById('tipo_respuesta').dispatchEvent(new Event("change"));
    }); 

</script>
@endpush
