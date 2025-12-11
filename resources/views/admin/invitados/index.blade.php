@extends('adminlte::page')

@section('title', 'Lista de invitados')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>

    <div class="card">
        <div class="card-body">
            <a href="">Nuevo Invitado</a>
            <hr>


            <table class="table" id="table-invitados">
                <thead>
                    <th>#</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Dni</th>
                    <th>Celular</th>
                    <th>E-mail</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Opc</th>
                </thead>
                <tbody>
                    @foreach ($invitados as $invitado)
                        <tr>
                            <td>{{ $invitado->id }}</td>
                            <td>{{ $invitado->nombre }}</td>
                            <td>{{ $invitado->apellidos }}</td>
                            <td>{{ $invitado->dni }}</td>
                            <td>{{ $invitado->celular }}</td>
                            <td>{{ $invitado->email }}</td>
                            <td>{{ $invitado->direccion }}</td>
                            <td>{{ $invitado->estado }}</td>
                            <td>
                                <form action="{{ route('invitados.destroy', $invitado) }}">
                                    <a href="{{ route('invitados.generarPDF', $invitado) }}"
                                        class="btn btn-warning btn-xs">Codigo Qr</a>
                                    
                                    @can('admin.invitados.edit')
                                        <a href="" class="btn btn-xs btn-info">Editar</a>
                                    @endcan

                                    @can('admin.invitados.destroy')
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
            $('#table-invitados').DataTable();
        });
    </script>
@stop
