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
                                           class="btn btn-sm btn-primary"
                                           onclick="abrirModalSeguimiento({{ $cotizacion->id }}, {{ $cotizacion->venta->id }})">
                                       <i class="fas fa-shipping-fast"></i> Ver Seguimiento
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

        /* Timeline de Seguimiento */
        .timeline-seguimiento {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            position: relative;
        }

        .timeline-marker {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            z-index: 2;
            border: 3px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-item.completado .timeline-marker {
            background-color: #28a745;
            color: white;
        }

        .timeline-item.actual .timeline-marker {
            background-color: #28a745;
            color: white;
            animation: pulse 2s infinite;
        }

        .timeline-item:not(.completado) .timeline-marker {
            background-color: #e0e0e0;
            color: #999;
        }

        .timeline-marker i {
            font-size: 14px;
        }

        .timeline-content {
            margin-left: 20px;
            flex-grow: 1;
        }

        .timeline-estado {
            font-weight: 600;
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
        }

        .timeline-item.completado .timeline-estado,
        .timeline-item.actual .timeline-estado {
            color: #28a745;
        }

        .timeline-item:not(.completado) .timeline-estado {
            color: #999;
        }

        .timeline-fecha {
            font-size: 14px;
            color: #666;
        }

        .timeline-observaciones {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #e0e0e0;
        }

        .timeline-observaciones small {
            font-style: italic;
            color: #888;
        }

        .timeline-line {
            width: 3px;
            height: 40px;
            background-color: #e0e0e0;
            margin-left: 18.5px;
            margin-bottom: 10px;
        }

        .timeline-item.completado + .timeline-line {
            background-color: #28a745;
        }

        .info-producto {
            border-top: 2px solid #e0e0e0;
            padding-top: 20px;
        }

        .producto-icono {
            width: 60px;
            height: 60px;
            background-color: #f5f5f5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .producto-nombre {
            font-size: 16px;
            color: #333;
        }

        .producto-sku {
            font-size: 14px;
            color: #666;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }

        @media (max-width: 768px) {
            .timeline-seguimiento {
                padding: 15px 0;
            }

            .timeline-marker {
                width: 35px;
                height: 35px;
            }

            .timeline-estado {
                font-size: 14px;
            }

            .timeline-fecha {
                font-size: 12px;
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
            // Usar estados de entrega desde el backend (dinámico)
            const estadosEntrega = venta.estados_entrega_timeline || [];

            // Obtener el índice del estado actual
            const estadoActual = venta.estado_entrega || estadosEntrega[0]?.valor || '';
            const indiceActual = estadosEntrega.findIndex(e => e.valor === estadoActual);

            // Formatear fecha desde string (formato: "dd mes - HH:mm hrs.")
            const formatearFechaDesdeString = (fechaStr) => {
                if (!fechaStr) return 'Fecha no disponible';
                // fechaStr viene como "13/12/2025 17:28" o similar
                const partes = fechaStr.split(' ');
                if (partes.length >= 2) {
                    const fechaPartes = partes[0].split('/'); // ["13", "12", "2025"]
                    const horaPartes = partes[1].split(':'); // ["17", "28"]
                    const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                                  'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                    const dia = parseInt(fechaPartes[0]);
                    const mes = meses[parseInt(fechaPartes[1]) - 1];
                    const hora = horaPartes[0].padStart(2, '0');
                    const minutos = horaPartes[1] ? horaPartes[1].padStart(2, '0') : '00';
                    return `${dia} ${mes} - ${hora}:${minutos} hrs.`;
                }
                return fechaStr;
            };

            // Crear mapa de historial por estado
            const historialMap = {};
            if (venta.historial_estados_entrega && Array.isArray(venta.historial_estados_entrega)) {
                venta.historial_estados_entrega.forEach(h => {
                    historialMap[h.estado_entrega] = h;
                });
            }

            // Generar timeline
            let timelineHtml = '<div class="timeline-seguimiento">';

            estadosEntrega.forEach((estado, index) => {
                const estaCompletado = index <= indiceActual;
                const esActual = index === indiceActual;

                // Buscar fecha en el historial
                const historial = historialMap[estado.valor];
                let fechaTexto = 'Fecha no disponible';

                if (historial && historial.fecha_completa) {
                    fechaTexto = formatearFechaDesdeString(historial.fecha_completa);
                } else if (estaCompletado && index === 0) {
                    // Si es el primer estado y está completado, usar fecha de creación de la venta
                    fechaTexto = formatearFechaDesdeString(venta.fecha_creacion);
                }

                // Agregar observaciones si existen
                let observacionesHtml = '';
                if (historial && historial.observaciones) {
                    observacionesHtml = `
                        <div class="timeline-observaciones mt-1">
                            <small class="text-muted">
                                <i class="fas fa-comment"></i> ${historial.observaciones}
                            </small>
                        </div>
                    `;
                }

                timelineHtml += `
                    <div class="timeline-item ${estaCompletado ? 'completado' : ''} ${esActual ? 'actual' : ''}">
                        <div class="timeline-marker">
                            ${estaCompletado ? `<i class="fas fa-check"></i>` : `<i class="fas fa-circle"></i>`}
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-estado">${estado.texto}</div>
                            <div class="timeline-fecha">${fechaTexto}</div>
                            ${observacionesHtml}
                        </div>
                    </div>
                    ${index < estadosEntrega.length - 1 ? '<div class="timeline-line"></div>' : ''}
                `;
            });

            timelineHtml += '</div>';

            // Información del producto (si está disponible)
            let productoHtml = '';
            if (venta.cotizacion && venta.cotizacion.numero) {
                productoHtml = `
                    <div class="info-producto mt-4">
                        <h5 class="mb-3"><strong>Información del producto</strong></h5>
                        <div class="d-flex align-items-center">
                            <div class="producto-icono mr-3">
                                <i class="fas fa-box fa-2x text-muted"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="producto-nombre mb-1">
                                    <strong>Cotización: ${venta.cotizacion.numero}</strong>
                                </div>
                                ${venta.codigo_seguimiento && venta.codigo_seguimiento !== 'N/A' ? `
                                    <div class="producto-sku">
                                        <strong>Código de Seguimiento:</strong> ${venta.codigo_seguimiento}
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }

            let html = `
                <div class="seguimiento-container">
                    ${timelineHtml}
                    ${productoHtml}
                </div>
            `;

            $('#contenidoSeguimiento').html(html);
        }


    </script>
@stop
