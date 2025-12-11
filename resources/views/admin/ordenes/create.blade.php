@extends('adminlte::page')

@section('title', 'Nueva Orden de servicio')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('content_header')
    <h1>Registro Orden de servicio </h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <div class="card-body">

                    <a href="{{ route('ordenes-de-servicio.index') }}" class="btn btn-xs btn-warning">Lista de equipos</a>
                    <hr>


                    <form action="{{ route('ordenes-de-servicio.store') }}" method="POST" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="">Seleccionar Cliente</label>
                            <select class="form-control select2" style="width: 100%" name="cliente_id">
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"> {{ $cliente->nombre }}
                                        {{ $cliente->apellidos }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="">Seleccionar Equipo</label>
                            <select class="form-control select2" style="width: 100%" name="equipo_id">
                                @foreach ($equipos as $equipo)
                                    <option value="{{ $equipo->id }}">  {{ $equipo->numero_de_serie }} </option>
                                @endforeach
                            </select>
                        </div>



                        <div class="form-group">
                            <label for="">Fecha aproximada de entrega</label>
                            <input type="date" name="fecha_aproximada_entrega" class="form-control"
                                placeholder="Fecha aproximada de entrega" required>
                        </div>


                        <div class="form-group">
                            <label for="">Problemas del equipo </label>
                            <textarea name="descripcion_del_problema" class="form-control" cols="30" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="">Costo estimado</label>
                            <input type="text" name="costo_estimado" class="form-control"
                                placeholder="Costo estimado del servicio" required>
                        </div>



                        <div class="form-group">
                            <label for="">Estado de reparación</label>
                            <select name="estado_del_servicio" class="form-control">
                                <option value="En espera">En espera</option>
                                <option value="En proceso">En proceso</option>
                                <option value="Reparado">Reparado</option>
                            </select>
                        </div>

                        <div class="form-group">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-success btn-xs"> Registrar Orden</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>


@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .select2-container--default .select2-selection--single {
            height: 40px !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2()
        });
    </script>
@stop
