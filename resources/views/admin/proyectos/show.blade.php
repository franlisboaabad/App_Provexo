@extends('adminlte::page')

@section('title', 'Detalle del proyecto')

@section('content_header')
    <h1>Detalle de proyecto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- <h1>{{ $proyecto->nombre_proyecto }}</h1> --}}

            <table class="table">
                <thead>
                    <th>#</th>
                    <th>Proyecto</th>
                    <th>Empresa</th>
                    <th>Estado</th>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $proyecto->id }}</td>
                        <td>{{ $proyecto->nombre_proyecto }}</td>
                        <td>{{ $proyecto->cliente->nombre_empresa }}</td>

                        <td>
                             @if($proyecto->estado == 1)
                                <span class="badge badge-success"> Activo </span>
                             @else
                                <span class="badge badge-success"> Inactivo </span>
                             @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            <hr>
            <p>Usuarios autorizados del proyecto:</p>
            <br><br>

            <table class="table">
                <thead>
                    <th>#</th>
                    <th>Usuario asignado</th>
                    <th>Config</th>

                </thead>
                <tbody>
                    @foreach ($usuariosAsignados as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>
                            <form action="">
                                @csrf
                                <button class="btn btn-danger btn-xs">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>

            <br><br>
            <a href="{{ route('proyectos.index') }}" class="btn btn-warning btn-xs">Lista de proyectos</a>
        </div>
    </div>
@stop

@section('css')

@stop

@section('js')

@stop
