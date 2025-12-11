@extends('adminlte::page')

@section('title', 'Nueva tarea')

@section('content_header')
    <h1>Registro de tarea</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST" id="form_tarea">

                        <div class="form-group">
                            <label for="">Seleccionar actividad </label>
                            <select class="form-control select2" style="width: 100%" name="actividad_id" id="actividad_id">
                                @foreach ($actividades as $actividad)
                                    <option value="{{ $actividad->id }}"> {{ $actividad->actividad }}
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Nombre de tarea</label>
                            <input type="text" class="form-control" name="nombre_tarea" id="nombre_tarea">
                        </div>

                        <div class="form-group">
                            <label for="">Fecha de inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                        </div>
                        <div class="form-group">
                            <label for="">Fecha de presentacion</label>
                            <input type="date" class="form-control" name="fecha_presentacion" id="fecha_presentacion">
                        </div>
                        <div class="form-group">
                            <label for="">Responsable</label>
                            <input type="text" name="responsable" id="responsable" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Sustento de tarea</label>
                            <input type="text" name="sustento_de_trabajo" id="sustento_de_trabajo" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Comentario</label>
                            <textarea name="comentario" id="comentario" cols="30" rows="10" class="form-control"></textarea>
                        </div>


                        <div class="form-group">
                            <label for="estado_de_tarea">Estado de la Tarea:</label>
                                <select name="estado_de_tarea" id="estado_de_tarea">
                                    <option value="no iniciado">No iniciado</option>
                                    <option value="en proceso">En proceso</option>
                                    <option value="en revisión">En revisión</option>
                                    <option value="culminado">Culminado</option>
                                </select>
                            </div>

                        <div class="form-group">
                            @csrf
                            <button  type="button" class="btn btn-success btn-xs" id="btn_Register" data-url="{{ route('tareas.store') }}">Registrar tarea</button>
                            <a href="{{ route('tareas.index') }}" class="btn btn-warning btn-xs">Lista de tareas</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')

@stop

@section('js')
    <script src="{{ asset('js/tarea.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop
