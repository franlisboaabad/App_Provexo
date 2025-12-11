@extends('adminlte::page')

@section('title', 'Actividades')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Lista de actividades</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <a href="{{ route('actividades.create') }}" class="btn btn-primary btn-xs">Nueva Actividad</a>
        <hr>

        <table class="table" id="table-actividades">
            <thead>
                <th>#</th>
                <th>Proyecto</th>
                <th>Actividad</th>
                <th>Fecha de facturación</th>
                <th>Config</th>
            </thead>
            <tbody>
                @foreach ($actividades as $actividad)
                    <tr>
                        <td>{{ $actividad->id }}</td>
                        <td>{{ $actividad->proyecto->nombre_proyecto }}</td>
                        <td>{{ $actividad->actividad }}</td>
                        <td>{{ $actividad->fecha_facturacion }}</td>
                        <td>
                            <form action="" method="POST">

                                @can('admin.actividad.edit')
                                    <a href="{{ route('actividades.edit', $actividad) }}" class="btn btn-xs btn-info">Editar</a>
                                @endcan

                                @can('admin.actividad.destroy')
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" id="btn_Delete" class="btn btn-danger btn-xs" data-url="{{ route('actividades.destroy', $actividad) }}">Eliminar</button>
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
        $('#table-actividades').DataTable();
    });
</script>
<script src="{{ asset('js/actividad.js') }}"></script>



@stop
