@extends('adminlte::page')

@section('title', 'Detalle de Venta')

@section('content_header')
    <h1>Detalle de Venta #{{ $venta->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('admin.ventas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            <div>
                @can('admin.ventas.edit')
                    <a href="{{ route('admin.ventas.edit', $venta) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endcan
                @can('admin.ventas.destroy')
                    <form action="{{ route('admin.ventas.destroy', $venta) }}"
                          method="POST"
                          style="display: inline-block;"
                          onsubmit="return confirm('¿Está seguro de eliminar esta venta? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Información de la Venta -->
                <div class="col-md-6">
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-hand-holding-usd"></i> Información de la Venta</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">ID Venta:</th>
                                    <td>{{ $venta->id }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Creación:</th>
                                    <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Monto Vendido:</th>
                                    <td><strong>S/ {{ number_format($venta->monto_vendido, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Adelanto:</th>
                                    <td>S/ {{ number_format($venta->adelanto, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Restante:</th>
                                    <td><strong>S/ {{ number_format($venta->restante, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Estado del Pedido:</th>
                                    <td>
                                        <span class="badge badge-{{ $venta->estado_pedido == 'entregado' ? 'success' : ($venta->estado_pedido == 'cancelado' ? 'danger' : ($venta->estado_pedido == 'en_proceso' ? 'warning' : 'info')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $venta->estado_pedido)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Margen Bruto c/Transporte:</th>
                                    <td>
                                        <span class="text-{{ $venta->margen_bruto_con_transporte >= 0 ? 'success' : 'danger' }}">
                                            <strong>S/ {{ number_format($venta->margen_bruto_con_transporte, 2) }}</strong>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Información de Transporte -->
                <div class="col-md-6">
                    <div class="card border-info mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-truck"></i> Información de Transporte</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Monto Transporte:</th>
                                    <td>S/ {{ number_format($venta->monto_transporte, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre Transporte:</th>
                                    <td>{{ $venta->nombre_transporte ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Información de Cotización -->
                <div class="col-md-12">
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Cotización Asociada</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Número:</strong><br>
                                    <a href="{{ route('admin.cotizaciones.show', $venta->cotizacion) }}" target="_blank">
                                        {{ $venta->cotizacion->numero_cotizacion }}
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <strong>Cliente:</strong><br>
                                    {{ $venta->cotizacion->cliente->user->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Fecha Emisión:</strong><br>
                                    {{ $venta->cotizacion->fecha_emision->format('d/m/Y') }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Estado:</strong><br>
                                    <span class="badge badge-{{ $venta->cotizacion->estado == 'ganado' ? 'success' : 'danger' }}">
                                        {{ ucfirst($venta->cotizacion->estado) }}
                                    </span>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <strong>Monto Cotizado:</strong><br>
                                    S/ {{ number_format($venta->cotizacion->total, 2) }}
                                </div>
                                <div class="col-md-9">
                                    <strong>Nota de la Venta:</strong><br>
                                    {{ $venta->nota ?? 'Sin observaciones' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos de la Cotización -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-boxes"></i> Productos de la Cotización</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($venta->cotizacion->productos as $index => $productoCotizacion)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $productoCotizacion->producto->codigo_producto ?? 'N/A' }}</td>
                                                <td>{{ $productoCotizacion->producto->descripcion ?? 'N/A' }}</td>
                                                <td>{{ $productoCotizacion->cantidad }}</td>
                                                <td>S/ {{ number_format($productoCotizacion->precio_unitario, 2) }}</td>
                                                <td>S/ {{ number_format($productoCotizacion->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-right">Total:</th>
                                            <th>S/ {{ number_format($venta->cotizacion->total, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

