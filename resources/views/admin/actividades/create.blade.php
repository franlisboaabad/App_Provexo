@extends('adminlte::page')

@section('title', 'Nueva Actividad')
@section('plugins.Select2', true)
@section('content_header')
    <h1>Registro de Actividad</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">

                <div class="card-body">

                    <form action="" method="POST" id="form_actividad">

                        <div class="form-group">
                            <label for="">Seleccionar proyecto </label>
                            <select class="form-control select2" style="width: 100%" name="proyecto_id">
                                @foreach ($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id }}"> {{ $proyecto->nombre_proyecto }}
                                @endforeach
                            </select>
                        </div>

                        <div class="form group pb-3">
                            <label for="">Actividad</label>
                            <input type="text" placeholder="Ingrese actividad" name="actividad" id="actividad"
                                class="form-control" required value="{{ old('actividad') }}">
                        </div>

                        <div class="form-group">
                            <label for="">Fecha de facturación</label>
                            <input type="date" name="fecha_facturacion" id="fecha_facturacion"
                                class="form-control" required value="{{ old('fecha_facturacion') }}">
                        </div>
                        <div class="form-group">
                            @csrf
                            <button type="button" id="btn_Register" class="btn btn-success btn-xs" data-url="{{ route('actividades.store') }}">Registrar actividad</button>
                            <a href="{{ route('actividades.index') }}" class="btn btn-warning btn-xs">Lista de actividades</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .select2-container--default .select2-selection--single {
        height: 40px !important;
    }
</style>
@stop

@section('js')
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/actividad.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".select2").select2();
        });
    </script>


@stop
