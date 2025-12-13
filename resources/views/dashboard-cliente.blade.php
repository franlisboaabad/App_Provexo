@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-tachometer-alt text-primary"></i> Tablero Kanban
        </h1>
        <div>
            <span class="badge badge-info">Total: {{ $totalCotizaciones }} cotizaciones</span>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Columna 1: Solicitud de Cotización -->
        <div class="col-md-6 mb-3">
            <div class="card kanban-column" style="border-top: 4px solid #17a2b8;">
                <div class="card-header" style="background-color: #17a2b820;">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-file-invoice-dollar" style="color: #17a2b8;"></i>
                            Solicitud de Cotización
                        </span>
                        <span class="badge badge-info">
                            {{ $totalSolicitudes }}
                        </span>
                    </h5>
                </div>
                <div class="card-body p-2 kanban-body" style="min-height: 500px; max-height: 700px; overflow-y: auto;">
                    @if($solicitudesCotizacion->count() > 0)
                        @foreach($solicitudesCotizacion as $cotizacion)
                            <div class="card mb-2 kanban-card" style="border-left: 3px solid #17a2b8;">
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1">
                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}" class="text-dark">
                                            <strong>{{ $cotizacion->numero_cotizacion }}</strong>
                                        </a>
                                    </h6>
                                    <p class="card-text mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $cotizacion->fecha_emision->format('d/m/Y') }}
                                        </small>
                                    </p>
                                    <p class="card-text mb-1">
                                        <strong class="text-success">S/ {{ number_format($cotizacion->total, 2) }}</strong>
                                    </p>
                                    <p class="card-text mb-1">
                                        <span class="badge badge-{{ $cotizacion->estado == 'pendiente' ? 'info' : ($cotizacion->estado == 'aprobada' ? 'success' : ($cotizacion->estado == 'rechazada' ? 'danger' : ($cotizacion->estado == 'vencida' ? 'warning' : 'secondary'))) }}">
                                            {{ ucfirst($cotizacion->estado) }}
                                        </span>
                                    </p>
                                    @if($cotizacion->fecha_vencimiento)
                                        <p class="card-text mb-1">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-times"></i> Vence: {{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}
                                            </small>
                                        </p>
                                    @endif
                                    <div class="mt-2">
                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}"
                                           class="btn btn-sm btn-info btn-block">
                                            <i class="fas fa-eye"></i> Ver Detalle
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">Sin solicitudes de cotización</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna 2: Orden Cotizada Venta -->
        <div class="col-md-6 mb-3">
            <div class="card kanban-column" style="border-top: 4px solid #28a745;">
                <div class="card-header" style="background-color: #28a74520;">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-hand-holding-usd" style="color: #28a745;"></i>
                            Orden Cotizada Venta
                        </span>
                        <span class="badge badge-success">
                            {{ $totalOrdenes }}
                        </span>
                    </h5>
                </div>
                <div class="card-body p-2 kanban-body" style="min-height: 500px; max-height: 700px; overflow-y: auto;">
                    @if($ordenesCotizadasVenta->count() > 0)
                        @foreach($ordenesCotizadasVenta as $cotizacion)
                            <div class="card mb-2 kanban-card" style="border-left: 3px solid #28a745;">
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1">
                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}" class="text-dark">
                                            <strong>{{ $cotizacion->numero_cotizacion }}</strong>
                                        </a>
                                    </h6>
                                    <p class="card-text mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $cotizacion->fecha_emision->format('d/m/Y') }}
                                        </small>
                                    </p>
                                    <p class="card-text mb-1">
                                        <strong class="text-success">S/ {{ number_format($cotizacion->total, 2) }}</strong>
                                    </p>
                                    @if($cotizacion->venta)
                                        <p class="card-text mb-1">
                                            <strong class="text-primary">Monto Vendido: S/ {{ number_format($cotizacion->venta->monto_vendido, 2) }}</strong>
                                        </p>
                                        <p class="card-text mb-1">
                                            <span class="badge badge-{{ $cotizacion->venta->estado_pedido == 'entregado' ? 'success' : ($cotizacion->venta->estado_pedido == 'en_proceso' ? 'warning' : ($cotizacion->venta->estado_pedido == 'cancelado' ? 'danger' : 'info')) }}">
                                                <i class="fas fa-shipping-fast"></i> {{ ucfirst(str_replace('_', ' ', $cotizacion->venta->estado_pedido)) }}
                                            </span>
                                        </p>
                                        @if($cotizacion->venta->adelanto > 0)
                                            <p class="card-text mb-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-money-bill-wave"></i> Adelanto: S/ {{ number_format($cotizacion->venta->adelanto, 2) }}
                                                </small>
                                            </p>
                                        @endif
                                    @endif
                                   <button type="button"
                                           class="btn btn-sm btn-primary btn-block"
                                           onclick="abrirModalSeguimiento({{ $cotizacion->id }}, {{ $cotizacion->venta->id }})">
                                       <i class="fas fa-shipping-fast"></i> Seguimiento de Venta
                                   </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">Sin órdenes convertidas en venta</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Seguimiento de Venta -->
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-shipping-fast"></i> Seguimiento de Venta
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contenidoSeguimiento">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p class="mt-2">Cargando información...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .kanban-column {
            height: 100%;
        }

        .kanban-body {
            background-color: #f8f9fa;
        }

        .kanban-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .kanban-card .card-body {
            background-color: white;
        }

        /* Scrollbar personalizado */
        .kanban-body::-webkit-scrollbar {
            width: 6px;
        }

        .kanban-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .kanban-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .kanban-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .kanban-column {
                margin-bottom: 20px;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-refresh cada 30 segundos (opcional)
            // setInterval(function() {
            //     location.reload();
            // }, 30000);
        });

        function abrirModalSeguimiento(cotizacionId, ventaId) {
            // Mostrar modal con loading
            $('#modalSeguimiento').modal('show');
            $('#contenidoSeguimiento').html(`
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">Cargando información...</p>
                </div>
            `);

            // Obtener información de la venta en JSON
            fetch(`{{ url('ventas') }}/${ventaId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.venta) {
                        mostrarInformacionSeguimiento(data.venta);
                    } else {
                        throw new Error('Error al cargar la información');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#contenidoSeguimiento').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar la información del pedido.
                        </div>
                    `);
                });
        }

        function mostrarInformacionSeguimiento(venta) {
            // Determinar clase del badge según el estado
            let estadoClass = 'badge-info';
            let estadoIcon = 'fas fa-clock';
            if (venta.estado_pedido === 'entregado') {
                estadoClass = 'badge-success';
                estadoIcon = 'fas fa-check-circle';
            } else if (venta.estado_pedido === 'en_proceso') {
                estadoClass = 'badge-warning';
                estadoIcon = 'fas fa-cog fa-spin';
            } else if (venta.estado_pedido === 'cancelado') {
                estadoClass = 'badge-danger';
                estadoIcon = 'fas fa-times-circle';
            }

            let html = `
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white text-center">
                                <h4 class="mb-0">
                                    <i class="${estadoIcon}"></i> Estado del Pedido
                                </h4>
                            </div>
                            <div class="card-body text-center py-4">
                                <h2>
                                    <span class="badge ${estadoClass} badge-lg" style="font-size: 1.2rem; padding: 10px 20px;">
                                        ${venta.estado_pedido_texto}
                                    </span>
                                </h2>
                                <p class="text-muted mt-2 mb-0">Cotización: ${venta.cotizacion.numero}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Información de Pago</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <th width="50%">Monto Vendido:</th>
                                        <td><strong class="text-success">S/ ${venta.monto_vendido}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Adelanto:</th>
                                        <td>S/ ${venta.adelanto}</td>
                                    </tr>
                                    <tr>
                                        <th>Restante:</th>
                                        <td><strong class="text-primary">S/ ${venta.restante}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-truck"></i> Información de Transporte</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <th width="50%">Monto Transporte:</th>
                                        <td>S/ ${venta.monto_transporte}</td>
                                    </tr>
                                    <tr>
                                        <th>Nombre Transporte:</th>
                                        <td>${venta.nombre_transporte}</td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Creación:</th>
                                        <td>${venta.fecha_creacion}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            if (venta.nota) {
                html += `
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0"><i class="fas fa-sticky-note"></i> Nota</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">${venta.nota}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            $('#contenidoSeguimiento').html(html);
        }


    </script>
@stop
