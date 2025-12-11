@extends('adminlte::page')

@section('title', 'Lista de clientes')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Listado de clientes</h1>
@stop

@section('content')
    {{-- <p>Welcome to this beautiful admin panel.</p> --}}

    <div class="card">
        <div class="card-body">

            {{-- @include('validators.forms') --}}


            <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-xs">Nuevo Cliente</a>
            <hr>


            <table class="table" id="table-clientes">
                <thead>
                    <th>#</th>
                    <th>Tienda</th>
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
                    @foreach ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->id }}</td>
                            <td>{{ $cliente->nombre_tienda }}</td>
                            <td>{{ $cliente->nombre }}</td>
                            <td>{{ $cliente->apellidos }}</td>
                            <td>{{ $cliente->dni }}</td>
                            <td>{{ $cliente->celular }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>{{ $cliente->direccion }}</td>
                            <td>
                                @if ($cliente->estado)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <form action="" method="POST">

                                    @can('admin.clientes.show')
                                        <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-warning btn-xs">Ver</a>
                                    @endcan

                                    @can('admin.clientes.edit')
                                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-xs btn-info">Editar</a>
                                    @endcan

                                    @can('admin.clientes.destroy')
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" id="btn_Delete" class="btn btn-danger btn-xs"
                                            data-url="{{ route('clientes.destroy', $cliente) }}">Eliminar</button>
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
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/cliente.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#table-clientes').DataTable();
        });
    </script>
@stop
