@extends('adminlte::page')

@section('title', 'Nuevo Proyecto')

@section('plugins.Select2', true)

@section('content_header')
    <h1>Registro de proyectos</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST" id="form_proyecto">
                        <div class="form-group">
                            <label for="">Proyecto</label>
                            <input type="text" name="nombre_proyecto" id="nombre_proyecto" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="">Seleccionar Empresa </label>
                            <select class="form-control select2" style="width: 100%" name="cliente_id" id="cliente_id">
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"> {{ $cliente->nombre_empresa }}
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Seleccionar usuarios</label>
                            <select class="form-control select2" multiple="multiple" style="width: 100%" name="usuarios[]" id="usuarios">
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            @csrf
                            <button type="button" id="btn_Register" class="btn btn-success btn-xs" data-url="{{ route('proyectos.store') }}">Registrar proyecto</button>
                            <a href="{{ route('proyectos.index') }}" class="btn btn-warning btn-xs">Lista de proyectos</a>
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
    <script src="{{ asset('js/proyecto.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $(".select2").select2();
        });
    </script>

@stop
