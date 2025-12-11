@extends('adminlte::page')

@section('title', 'Proyectos')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Lista de proyectos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <a href="{{ route('proyectos.create') }}" class="btn btn-primary btn-xs">+ Nuevo Proyecto</a>
            <hr>

            <table class="table" id="table-proyectos">
                <thead>
                    <th>#</th>
                    <th>Empresa</th>
                    <th>Proyecto</th>
                    <th>Usuarios</th>
                    <th>Config</th>
                </thead>
                <tbody>
                    @foreach ($proyectos as $proyecto)
                        <tr>
                            <td>{{ $proyecto->id }}</td>
                            <td>{{ $proyecto->cliente->nombre_empresa }}</td>
                            <td>{{ $proyecto->nombre_proyecto }}</td>
                            <td></td>
                            <td>
                                <form action="" method="POST">

                                    @can('admin.proyectos.show')
                                        <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-xs btn-warning">Ver Proyecto</a>
                                    @endcan

                                    @can('admin.proyectos.edit')
                                        <a href="{{ route('proyectos.edit', $proyecto) }}" class="btn btn-xs btn-info">Editar</a>
                                    @endcan

                                    @can('admin.proyectos.destroy')
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" id="btn_Delete" class="btn btn-danger btn-xs" data-url="{{ route('proyectos.destroy', $proyecto) }}">Eliminar</button>
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

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#table-proyectos').DataTable();
        });
    </script>
    <script src="{{ asset('js/proyecto.js') }}"></script>
@stop
