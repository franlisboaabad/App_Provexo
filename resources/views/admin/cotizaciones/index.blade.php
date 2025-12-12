@extends('adminlte::page')

@section('title', 'Lista de Cotizaciones')

@section('content_header')
    <h1>Lista de Cotizaciones</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            @can('admin.cotizaciones.create')
                <a href="{{ route('admin.cotizaciones.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nueva Cotización
                </a>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Vencimiento</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cotizaciones as $cotizacion)
                        <tr>
                            <td>{{ $cotizacion->id }}</td>
                            <td><strong>{{ $cotizacion->numero_cotizacion }}</strong></td>
                            <td>{{ $cotizacion->cliente->user->name ?? 'N/A' }}</td>
                            <td>{{ $cotizacion->fecha_emision->format('d/m/Y') }}</td>
                            <td>{{ $cotizacion->fecha_vencimiento ? $cotizacion->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</td>
                            <td><strong>S/ {{ number_format($cotizacion->total, 2) }}</strong></td>
                            <td>
                                @if($cotizacion->estado == 'aprobada')
                                    <span class="badge badge-success">Aprobada</span>
                                @elseif($cotizacion->estado == 'rechazada')
                                    <span class="badge badge-danger">Rechazada</span>
                                @elseif($cotizacion->estado == 'vencida')
                                    <span class="badge badge-warning">Vencida</span>
                                @else
                                    <span class="badge badge-info">Pendiente</span>
                                @endif
                            </td>
                            <td>
                                @can('admin.cotizaciones.show')
                                    <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}"
                                       class="btn btn-info btn-sm"
                                       title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan

                                @can('admin.cotizaciones.edit')
                                    <a href="{{ route('admin.cotizaciones.edit', $cotizacion) }}"
                                       class="btn btn-warning btn-sm"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                @can('admin.cotizaciones.destroy')
                                    <form action="{{ route('admin.cotizaciones.destroy', $cotizacion) }}"
                                          method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta cotización? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay cotizaciones registradas</td>
                        </tr>
                    @endforelse
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
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@stop

