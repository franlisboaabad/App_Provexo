@extends('adminlte::page')

@section('title', 'Nuevo Equipo')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('content_header')
    <h1>Registro de Equipos (Laptops | Pc´s | Smartphone | Tablets) </h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">

                    <a href="{{ route('equipos.index') }}" class="btn btn-xs btn-warning">Lista de equipos</a>
                    <hr>


                    <form action="{{ route('equipos.store') }}" method="POST" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="">Seleccionar cliente</label>
                            <select class="form-control select2" style="width: 100%" name="cliente_id">
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"> {{ $cliente->nombre }}
                                        {{ $cliente->apellidos }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Tipo de equipo</label>
                            <select name="tipo_id" id="" class="form-control">
                                @foreach ($tipo_equipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <label for="">Marca</label>
                                    <input type="text" name="marca" class="form-control"
                                        placeholder="Marca de equipo" required>
                                </div>
                                <div class="col">
                                    <label for="">Modelo</label>
                                    <input type="text" name="modelo" class="form-control"
                                        placeholder="Modelo de equipo" required>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="">Numero de serie</label>
                            <input type="text" name="numero_de_serie" class="form-control"
                                placeholder="Numero de serie de equipo" required>
                        </div>

                        <div class="form-group">
                            <label for="">Caracteristicas del equipo </label>
                            <textarea name="caracteristicas" class="form-control" cols="30" rows="3"></textarea>
                        </div>


                        <div class="form-group">
                            <label for="">Accesorios adicionales </label>
                            <textarea name="accesorios_adicionales" class="form-control" cols="30" rows="3"></textarea>
                        </div>




                        <div class="form-group">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-success btn-xs"> Registrar equipo</button>
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
            $('#table-equipos').DataTable();
            $('.select2').select2()
        });
    </script>
@stop
