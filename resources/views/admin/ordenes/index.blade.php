@extends('adminlte::page')

@section('title', 'Ordenes de servicio')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Lista Ordenes de Servicios</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    @include('validators.forms')

                    <a href="{{ route('ordenes-de-servicio.create') }}" class="btn btn-xs btn-primary">Nueva Orden</a>
                    <hr>

                    <table class="table" id="table-ordenes">
                        <thead>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Equipo</th>
                            <th>Tipo de Equipo</th>
                            <th>Fecha de entrega</th>
                            <th>Problema</th>
                            <th>Estado del servicio</th>
                            <th>Costo</th>
                            <th>Acciones</th>
                        </thead>
                        <tbody>
                            @foreach ($ordenes as $orden)
                                <tr>
                                    <td>{{ $orden->id }}</td>
                                    <td>{{ $orden->cliente->nombre }} {{ $orden->cliente->apellidos }} </td>
                                    <td>{{ $orden->equipo->numero_de_serie }}</td>
                                    <td>{{ $orden->equipo->tipo->tipo }}</td>
                                    <td>{{ $orden->fecha_aproximada_entrega }}</td>
                                    <td>{{ $orden->descripcion_del_problema }}</td>
                                    <td>{{ $orden->estado_del_servicio }}</td>
                                    <td>{{ $orden->costo_estimado }}</td>
                                    <td>
                                        <form action="{{ route('ordenes-de-servicio.destroy', $orden ) }}" method="POST">

                                            {{-- @can('admin.ordenes.show')
                                                <a href="{{ route('ordenes-de-servicio.show', $orden ) }}" class="btn btn-xs btn-warning">Ver</a>
                                            @endcan --}}

                                            @can('admin.ordenes.edit')
                                                <a href="{{ route('ordenes-de-servicio.edit', $orden ) }}" class="btn btn-xs btn-info">Editar</a>
                                            @endcan

                                            @can('admin.ordenes.destroy')
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('¿Esta seguro de eliminar registro?')">Eliminar</button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#table-ordenes').DataTable();
    });
</script>
@stop
