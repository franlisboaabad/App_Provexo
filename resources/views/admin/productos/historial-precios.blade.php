@extends('adminlte::page')

@section('title', 'Historial de Precios - ' . $producto->codigo_producto)

@section('content_header')
    <h1>Historial de Precios: {{ $producto->codigo_producto }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver a la lista
                    </a>
                    <a href="{{ route('admin.productos.show', $producto) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Ver Producto
                    </a>
                </div>
                <div class="card-body">
                    <!-- Información del Producto -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Información del Producto</h5>
                            <hr>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th width="40%">Código:</th>
                                    <td><strong>{{ $producto->codigo_producto }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Descripción:</th>
                                    <td>{{ $producto->descripcion }}</td>
                                </tr>
                                <tr>
                                    <th>Precio Base Actual:</th>
                                    <td><strong class="text-success">S/ {{ number_format($producto->precio_base, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Precio Venta:</th>
                                    <td>S/ {{ number_format($producto->precio_venta, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Resumen del Historial</h5>
                            <hr>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th width="40%">Total de Cambios:</th>
                                    <td><strong>{{ $historial->total() }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Último Cambio:</th>
                                    <td>
                                        @if($historial->count() > 0)
                                            {{ $historial->first()->created_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Sin cambios registrados</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Tabla de Historial -->
                    <h5><i class="fas fa-history"></i> Historial de Cambios de Precio Base</h5>
                    @if($historial->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha y Hora</th>
                                        <th>Usuario</th>
                                        <th class="text-right">Precio Anterior</th>
                                        <th class="text-right">Precio Nuevo</th>
                                        <th class="text-center">Diferencia</th>
                                        <th>Cotización</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historial as $index => $item)
                                        <tr>
                                            <td>{{ $historial->firstItem() + $index }}</td>
                                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td>{{ $item->usuario->name ?? 'N/A' }}</td>
                                            <td class="text-right">
                                                <span class="text-danger">S/ {{ number_format($item->precio_base_anterior, 2) }}</span>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-success"><strong>S/ {{ number_format($item->precio_base_nuevo, 2) }}</strong></span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $diferencia = $item->precio_base_nuevo - $item->precio_base_anterior;
                                                    $porcentaje = $item->precio_base_anterior > 0
                                                        ? (($diferencia / $item->precio_base_anterior) * 100)
                                                        : 0;
                                                @endphp
                                                <span class="badge badge-{{ $diferencia >= 0 ? 'success' : 'danger' }}">
                                                    {{ $diferencia >= 0 ? '+' : '' }}S/ {{ number_format($diferencia, 2) }}
                                                    ({{ $diferencia >= 0 ? '+' : '' }}{{ number_format($porcentaje, 2) }}%)
                                                </span>
                                            </td>
                                            <td>
                                                @if($item->cotizacion)
                                                    <a href="{{ route('admin.cotizaciones.show', $item->cotizacion) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       target="_blank">
                                                        <i class="fas fa-file-invoice"></i> {{ $item->cotizacion->numero_cotizacion }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $item->observaciones ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-3">
                            {{ $historial->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No se han registrado cambios en el precio base de este producto.
                        </div>
                    @endif
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
        console.log('Historial de precios cargado');
    </script>
@stop

