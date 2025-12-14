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
                                    <th>Estado de Entrega:</th>
                                    <td>
                                        <span class="badge badge-{{ $venta->estado_entrega_badge_class ?? 'secondary' }}">
                                            {{ $venta->estado_entrega_texto ?? 'Registro Creado' }}
                                        </span>
                                    </td>
                                </tr>
                                @if($venta->codigo_seguimiento)
                                <tr>
                                    <th>Código de Seguimiento:</th>
                                    <td>
                                        <strong class="text-primary">{{ $venta->codigo_seguimiento }}</strong>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Total de Gastos:</th>
                                    <td>
                                        <strong class="text-danger">S/ {{ number_format($venta->total_gastos, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Margen Bruto:</th>
                                    <td>
                                        <span class="text-{{ ($venta->margen_bruto_con_transporte ?? 0) >= 0 ? 'success' : 'danger' }}">
                                            <strong>S/ {{ number_format($venta->margen_bruto_con_transporte ?? 0, 2) }}</strong>
                                        </span>
                                        <br>
                                        <small class="text-muted">Monto Vendido - (Costo Productos + Total Gastos)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Margen Neto:</th>
                                    <td>
                                        <span class="text-{{ ($venta->margen_neto ?? 0) >= 0 ? 'success' : 'danger' }}">
                                            <strong>S/ {{ number_format($venta->margen_neto ?? 0, 2) }}</strong>
                                        </span>
                                        <br>
                                        <small class="text-muted">Margen Bruto - Total Gastos</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Información de Entrega -->
                <div class="col-md-6">
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-truck"></i> Información de Entrega</h5>
                        </div>
                        <div class="card-body">
                            @if($venta->direccion_entrega || $venta->distrito || $venta->provincia || $venta->ciudad)
                                <table class="table table-sm">
                                    @if($venta->direccion_entrega)
                                        <tr>
                                            <th width="40%">Dirección:</th>
                                            <td>{{ $venta->direccion_entrega }}</td>
                                        </tr>
                                    @endif
                                    @if($venta->distrito)
                                        <tr>
                                            <th>Distrito:</th>
                                            <td>{{ $venta->distrito }}</td>
                                        </tr>
                                    @endif
                                    @if($venta->provincia)
                                        <tr>
                                            <th>Provincia:</th>
                                            <td>{{ $venta->provincia }}</td>
                                        </tr>
                                    @endif
                                    @if($venta->ciudad)
                                        <tr>
                                            <th>Ciudad:</th>
                                            <td>{{ $venta->ciudad }}</td>
                                        </tr>
                                    @endif
                                    @if($venta->codigo_postal)
                                        <tr>
                                            <th>Código Postal:</th>
                                            <td>{{ $venta->codigo_postal }}</td>
                                        </tr>
                                    @endif
                                    @if($venta->referencia)
                                        <tr>
                                            <th>Referencia:</th>
                                            <td>{{ $venta->referencia }}</td>
                                        </tr>
                                    @endif
                                </table>
                            @else
                                <p class="text-muted mb-0">No hay información de entrega registrada</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gastos de la Venta -->
                <div class="col-md-6">
                    <div class="card border-warning mb-3">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0"><i class="fas fa-receipt"></i> Gastos de la Venta</h5>
                        </div>
                        <div class="card-body">
                            @if($venta->gastos->count() > 0)
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                                <th class="text-right">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($venta->gastos as $gasto)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $gasto->descripcion }}</strong>
                                                        @if($gasto->fecha)
                                                            <br><small class="text-muted">{{ $gasto->fecha->format('d/m/Y') }}</small>
                                                        @endif
                                                        @if($gasto->observaciones)
                                                            <br><small class="text-muted">{{ $gasto->observaciones }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        <strong>S/ {{ number_format($gasto->monto, 2) }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total:</th>
                                                <th class="text-right text-danger">S/ {{ number_format($venta->total_gastos, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No hay gastos registrados</p>
                            @endif
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
                                    <span class="badge badge-{{ $venta->cotizacion->estado == 'aprobada' ? 'success' : 'danger' }}">
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

