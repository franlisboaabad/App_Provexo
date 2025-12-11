@extends('adminlte::page')

@section('title', 'Dashboard')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Lista de Tareas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <a href="{{ route('tareas.create') }}" class="btn btn-primary btn-xs">+ Nueva Tarea</a>
            <hr>

            <table class="table" id="table-tareas">
                <thead>
                    <th>#</th>
                    <th>Empresa</th>
                    <th>Proyecto</th>
                    <th>Actividad</th>
                    <th>Tarea</th>
                    <th>Estado de tarea</th>
                    <th>Fecha de entrega</th>
                    <th>Config</th>
                </thead>
                <tbody>
                    @foreach ($tareas as $tarea)
                        <tr>
                            <td>{{ $tarea->id }}</td>
                            <td>{{ $tarea->actividad->proyecto->cliente->nombre_empresa }}</td>
                            <td>{{ $tarea->actividad->proyecto->nombre_proyecto }}</td>
                            <td>{{ $tarea->actividad->actividad }}</td>
                            <td>{{ $tarea->nombre_tarea  }}</td>
                            {{-- <td>{{ $tarea->estado_de_tarea }}</td> --}}
                            <td>
                                <select name="" id="" class="form-control">
                                    <option value="">{{ $tarea->estado_de_tarea }}</option>
                                    <option value="">No iniciado</option>
                                    <option value="">En proceso</option>
                                    <option value="">Finalizado</option>
                                </select>
                            </td>
                            <td>{{ $tarea->fecha_presentacion }}</td>
                            <td>
                                <form action="" method="POST">
{{--
                                    @can('admin.proyectos.show')
                                        <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-xs btn-warning">Ver Proyecto</a>
                                    @endcan --}}

                                    @can('admin.tareas.edit')
                                        <a href="{{ route('tareas.edit', $tarea) }}" class="btn btn-xs btn-info">Editar</a>
                                    @endcan

                                    @can('admin.tareas.destroy')
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" id="btn_Delete" class="btn btn-danger btn-xs" data-url="{{ route('tareas.destroy', $tarea) }}">Eliminar</button>
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
        $('#table-tareas').DataTable();
    });
</script>
<script src="{{ asset('js/tarea.js') }}"></script>
@stop
