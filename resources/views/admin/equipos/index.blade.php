@extends('adminlte::page')

@section('title', 'Lista de equipos')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Lista de equipos</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">

            @include('validators.forms')

            <a href="{{ route('equipos.create') }}" class="btn btn-xs btn-primary">Nuevo Equipo</a>
            <hr>


            <table class="table" id="table-equipos">
                <thead>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Tipo de equipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Numero de serie</th>
                    <th>Opc</th>
                </thead>
                <tbody>
                    @foreach ($equipos as $equipo)
                        <tr>
                            <td>{{ $equipo->id }}</td>
                            <td>{{ $equipo->cliente->nombre }} {{ $equipo->cliente->apellidos }}</td>
                            <td>{{ $equipo->tipo->tipo }}</td>
                            <td>{{ $equipo->marca }}</td>
                            <td>{{ $equipo->modelo }}</td>
                            <td>{{ $equipo->numero_de_serie }}</td>
                            <td>
                                <form action="{{ route('equipos.destroy', $equipo) }}">

                                    @can('admin.equipos.edit')
                                        <a href="{{ route('equipos.show', $equipo ) }}" class="btn btn-xs btn-warning">Ver</a>
                                    @endcan

                                    @can('admin.equipos.edit')
                                        <a href="{{ route('equipos.edit', $equipo) }}" class="btn btn-xs btn-info">Editar</a>
                                    @endcan

                                    @can('admin.equipos.destroy')
                                        <button type="submit" class="btn btn-danger btn-xs"
                                            onclick="return confirm('¿Esta seguro de eliminar registro?')">Eliminar</button>
                                    @endcan
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#table-equipos').DataTable();
        });
    </script>
@stop
