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
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                @can('admin.cotizaciones.create')
                    <a href="{{ route('admin.cotizaciones.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Cotización
                    </a>
                @endcan
            </div>
            <div class="d-flex align-items-center">
                <label for="filtroEstado" class="mb-0 mr-2">
                    <strong>Filtrar por Estado:</strong>
                </label>
                <select class="form-control form-control-sm" id="filtroEstado" style="width: auto; min-width: 150px;">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                    <option value="vencida" {{ request('estado') == 'vencida' ? 'selected' : '' }}>Vencida</option>
                    <option value="ganado" {{ request('estado') == 'ganado' ? 'selected' : '' }}>Ganado</option>
                    <option value="perdido" {{ request('estado') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <table id="cotizacionesTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Fecha Emisión</th>
                        <th>Monto Cotizado</th>
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
                            <td><strong>S/ {{ number_format($cotizacion->total, 2) }}</strong></td>
                            <td>
                                @can('admin.cotizaciones.edit')
                                    <select class="form-control form-control-sm cambiar-estado"
                                            data-cotizacion-id="{{ $cotizacion->id }}"
                                            data-estado-actual="{{ $cotizacion->estado }}">
                                        <option value="pendiente" {{ $cotizacion->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="aprobada" {{ $cotizacion->estado == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                        <option value="rechazada" {{ $cotizacion->estado == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                        <option value="vencida" {{ $cotizacion->estado == 'vencida' ? 'selected' : '' }}>Vencida</option>
                                        <option value="ganado" {{ $cotizacion->estado == 'ganado' ? 'selected' : '' }}>Ganado</option>
                                        <option value="perdido" {{ $cotizacion->estado == 'perdido' ? 'selected' : '' }}>Perdido</option>
                                    </select>
                                @else
                                    @if($cotizacion->estado == 'aprobada')
                                        <span class="badge badge-success">Aprobada</span>
                                    @elseif($cotizacion->estado == 'rechazada')
                                        <span class="badge badge-danger">Rechazada</span>
                                    @elseif($cotizacion->estado == 'vencida')
                                        <span class="badge badge-warning">Vencida</span>
                                    @elseif($cotizacion->estado == 'ganado')
                                        <span class="badge badge-success">Ganado</span>
                                    @elseif($cotizacion->estado == 'perdido')
                                        <span class="badge badge-danger">Perdido</span>
                                    @else
                                        <span class="badge badge-info">Pendiente</span>
                                    @endif
                                @endcan
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog"></i> Acciones
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('admin.cotizaciones.show')
                                            <a class="dropdown-item" href="{{ route('admin.cotizaciones.show', $cotizacion) }}">
                                                <i class="fas fa-eye text-info"></i> Ver Detalle
                                            </a>
                                        @endcan
                                        <a class="dropdown-item" href="#" onclick="abrirModalPreview({{ $cotizacion->id }}, '{{ $cotizacion->numero_cotizacion }}'); return false;">
                                            <i class="fas fa-file-pdf text-danger"></i> Preview PDF
                                        </a>
                                        @can('admin.cotizaciones.edit')
                                            <a class="dropdown-item" href="{{ route('admin.cotizaciones.edit', $cotizacion) }}">
                                                <i class="fas fa-edit text-warning"></i> Editar
                                            </a>
                                        @endcan
                                        @can('admin.ventas.create')
                                            @if(!$cotizacion->venta && in_array($cotizacion->estado, ['aprobada', 'pendiente']))
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('admin.ventas.create', ['cotizacion_id' => $cotizacion->id]) }}">
                                                    <i class="fas fa-hand-holding-usd text-success"></i> Convertir en Venta
                                                </a>
                                            @elseif($cotizacion->venta)
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="{{ route('admin.ventas.show', $cotizacion->venta) }}">
                                                    <i class="fas fa-eye text-primary"></i> Ver Venta
                                                </a>
                                                @can('admin.ventas.edit')
                                                    <a class="dropdown-item" href="{{ route('admin.ventas.edit', $cotizacion->venta) }}">
                                                        <i class="fas fa-edit text-warning"></i> Editar Venta
                                                    </a>
                                                @endcan
                                            @endif
                                        @endcan
                                        @can('admin.cotizaciones.destroy')
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.cotizaciones.destroy', $cotizacion) }}"
                                                  method="POST"
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar esta cotización? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay cotizaciones registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Preview PDF con Acciones -->
    <div class="modal fade" id="modalPreviewCotizacion" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-file-pdf text-white"></i> Preview de Cotización: <span id="modal-numero-cotizacion"></span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna Izquierda: Preview -->
                        <div class="col-md-7">
                            <h6 class="font-weight-bold mb-3">
                                <i class="fas fa-file-pdf text-danger"></i> Vista Previa
                            </h6>
                            <div class="border p-3" style="background-color: #f8f9fa; min-height: 500px;">
                                <iframe id="previewPdfModal" src=""
                                        style="width: 100%; height: 500px; border: none;"></iframe>
                            </div>
                        </div>

                        <!-- Columna Derecha: Acciones Rápidas -->
                        <div class="col-md-5">
                            <h6 class="font-weight-bold mb-3">
                                <i class="fas fa-bolt"></i> Acciones Rápidas
                            </h6>

                            <!-- Descargar PDF -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-download text-primary"></i> Descargar PDF
                                    </h6>
                                    <p class="card-text text-muted small">Descarga la cotización en formato PDF</p>
                                    <a href="#" id="btn-descargar-pdf" class="btn btn-primary btn-block" target="_blank" download>
                                        <i class="fas fa-file-pdf"></i> Descargar Cotización
                                    </a>
                                </div>
                            </div>

                            @if(!auth()->user()->hasRole('Cliente'))
                            <!-- Enviar por Email -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-envelope text-success"></i> Enviar por Email
                                    </h6>
                                    <p class="card-text text-muted small">Envía la cotización por correo electrónico</p>
                                    <form id="formEnviarEmailModal" onsubmit="enviarEmailModal(event)">
                                        @csrf
                                        <div class="form-group">
                                            <input type="email"
                                                   class="form-control"
                                                   id="emailClienteModal"
                                                   placeholder="correo@ejemplo.com"
                                                   required>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-paper-plane"></i> Enviar Email
                                        </button>
                                    </form>
                                    <div id="alertEmailModal" class="mt-2"></div>
                                </div>
                            </div>

                            <!-- Enviar por WhatsApp -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fab fa-whatsapp text-success"></i> Enviar por WhatsApp
                                    </h6>
                                    <p class="card-text text-muted small">Envía la cotización directamente por WhatsApp</p>

                                    <!-- Radio buttons para seleccionar tipo de WhatsApp -->
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="whatsappTipoModal" id="whatsappWebModal" value="web" checked>
                                            <label class="form-check-label" for="whatsappWebModal">
                                                WhatsApp Web
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="whatsappTipoModal" id="whatsappDesktopModal" value="desktop">
                                            <label class="form-check-label" for="whatsappDesktopModal">
                                                WhatsApp Desktop (Windows)
                                            </label>
                                        </div>
                                    </div>

                                    <form id="formEnviarWhatsAppModal" onsubmit="enviarWhatsAppModal(event)">
                                        @csrf
                                        <div class="form-group">
                                            <input type="tel"
                                                   class="form-control"
                                                   id="telefonoWhatsAppModal"
                                                   placeholder="+51 987 654 321"
                                                   required>
                                            <small class="form-text text-muted">Incluye código de país (ej: +51)</small>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fab fa-whatsapp"></i> Enviar por WhatsApp
                                        </button>
                                    </form>
                                    <div id="alertWhatsAppModal" class="mt-2"></div>
                                </div>
                            </div>
                            @endif
                        </div>
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
        .cambiar-estado {
            min-width: 120px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .cambiar-estado:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .cambiar-estado.border-success {
            border-color: #28a745 !important;
        }
        .cambiar-estado.border-danger {
            border-color: #dc3545 !important;
        }
        .cambiar-estado.border-warning {
            border-color: #ffc107 !important;
        }
        .cambiar-estado.border-info {
            border-color: #17a2b8 !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Inicializar DataTable
        $(document).ready(function() {
            var table = $('#cotizacionesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "pageLength": 25,
                "order": [[0, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [5, 6] } // Estado, Acciones no ordenables
                ]
            });

            // Filtro por estado
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var estadoFiltro = $('#filtroEstado').val();

                    // Si no hay filtro, mostrar todas las filas
                    if (!estadoFiltro) {
                        return true;
                    }

                    // Obtener el valor del select en la columna de estado (índice 5)
                    var estadoFila = $(table.row(dataIndex).node()).find('.cambiar-estado').val() ||
                                    $(table.row(dataIndex).node()).find('td:eq(5)').text().toLowerCase();

                    // Comparar estados
                    return estadoFila === estadoFiltro || estadoFila.toLowerCase().includes(estadoFiltro.toLowerCase());
                }
            );

            $('#filtroEstado').on('change', function() {
                table.draw();
            });

            // Aplicar filtro inicial si hay un estado en la URL
            @if(request('estado'))
                $('#filtroEstado').val('{{ request('estado') }}').trigger('change');
            @endif

            // Cambiar estado de cotización
            $(document).on('change', '.cambiar-estado', function() {
                var select = $(this);
                var cotizacionId = select.data('cotizacion-id');
                var estadoAnterior = select.data('estado-actual');
                var nuevoEstado = select.val();

                // Si el estado no cambió, no hacer nada
                if (nuevoEstado === estadoAnterior) {
                    return;
                }

                // Confirmar cambio
                if (!confirm('¿Está seguro de cambiar el estado a "' + nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1) + '"?')) {
                    select.val(estadoAnterior);
                    return;
                }

                // Deshabilitar select mientras se procesa
                select.prop('disabled', true);

                // Enviar petición AJAX
                $.ajax({
                    url: '/cotizaciones/' + cotizacionId + '/cambiar-estado',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        estado: nuevoEstado
                    },
                    success: function(response) {
                        if (response.success) {
                            // Actualizar estado actual en el data attribute
                            select.data('estado-actual', nuevoEstado);

                            // Actualizar clase del select según el estado
                            select.removeClass('border-info border-success border-danger border-warning');

                            if (nuevoEstado === 'aprobada') {
                                select.addClass('border-success');
                            } else if (nuevoEstado === 'rechazada') {
                                select.addClass('border-danger');
                            } else if (nuevoEstado === 'vencida') {
                                select.addClass('border-warning');
                            } else {
                                select.addClass('border-info');
                            }

                            // Mostrar mensaje de éxito
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Error al cambiar el estado';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Revertir al estado anterior
                        select.val(estadoAnterior);

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        // Rehabilitar select
                        select.prop('disabled', false);
                    }
                });
            });

            // Aplicar estilos según el estado inicial
            $('.cambiar-estado').each(function() {
                var estado = $(this).val();
                $(this).removeClass('border-info border-success border-danger border-warning');

                if (estado === 'aprobada') {
                    $(this).addClass('border-success');
                } else if (estado === 'rechazada') {
                    $(this).addClass('border-danger');
                } else if (estado === 'vencida') {
                    $(this).addClass('border-warning');
                } else {
                    $(this).addClass('border-info');
                }
            });
        });

        // Variable global para almacenar el ID de la cotización actual en el modal
        let cotizacionIdModal = null;

        // Función para abrir el modal de preview
        function abrirModalPreview(cotizacionId, numeroCotizacion) {
            cotizacionIdModal = cotizacionId;

            // Actualizar el título del modal
            document.getElementById('modal-numero-cotizacion').textContent = numeroCotizacion;

            // Actualizar el iframe con la URL del PDF
            const pdfUrl = `/cotizaciones/${cotizacionId}/pdf`;
            document.getElementById('previewPdfModal').src = pdfUrl;

            // Actualizar el enlace de descarga
            document.getElementById('btn-descargar-pdf').href = pdfUrl;

            // Limpiar formularios (solo si existen - para clientes no existen)
            const formEmail = document.getElementById('formEnviarEmailModal');
            const formWhatsApp = document.getElementById('formEnviarWhatsAppModal');
            const alertEmail = document.getElementById('alertEmailModal');
            const alertWhatsApp = document.getElementById('alertWhatsAppModal');

            if (formEmail) {
                formEmail.reset();
            }
            if (formWhatsApp) {
                formWhatsApp.reset();
            }
            if (alertEmail) {
                alertEmail.innerHTML = '';
            }
            if (alertWhatsApp) {
                alertWhatsApp.innerHTML = '';
            }

            // Abrir el modal
            $('#modalPreviewCotizacion').modal('show');
        }

        // Función para enviar email desde el modal
        function enviarEmailModal(event) {
            event.preventDefault();
            const email = document.getElementById('emailClienteModal').value;
            const alertDiv = document.getElementById('alertEmailModal');

            fetch(`/cotizaciones/${cotizacionIdModal}/enviar-email`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertDiv.innerHTML = '<div class="alert alert-success">¡Email enviado exitosamente!</div>';
                    document.getElementById('formEnviarEmailModal').reset();
                } else {
                    alertDiv.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error al enviar el email') + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alertDiv.innerHTML = '<div class="alert alert-danger">Error al enviar el email. Por favor, intente nuevamente.</div>';
            });
        }

        // Función para enviar WhatsApp desde el modal
        function enviarWhatsAppModal(event) {
            event.preventDefault();
            const telefono = document.getElementById('telefonoWhatsAppModal').value.replace(/\s+/g, '');
            const tipo = document.querySelector('input[name="whatsappTipoModal"]:checked').value;
            const alertDiv = document.getElementById('alertWhatsAppModal');

            // Obtener URL pública
            fetch(`/cotizaciones/${cotizacionIdModal}/publica`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const mensaje = encodeURIComponent('Hola, te comparto la cotización: ' + data.url);
                    let urlWhatsApp = '';

                    if (tipo === 'web') {
                        // WhatsApp Web
                        urlWhatsApp = `https://web.whatsapp.com/send?phone=${telefono}&text=${mensaje}`;
                    } else {
                        // WhatsApp Desktop (Windows) - usa wa.me que abre la app si está instalada
                        urlWhatsApp = `https://wa.me/${telefono}?text=${mensaje}`;
                    }

                    window.open(urlWhatsApp, '_blank');
                    alertDiv.innerHTML = '<div class="alert alert-success">Redirigiendo a WhatsApp...</div>';
                } else {
                    alertDiv.innerHTML = '<div class="alert alert-danger">Error al generar el enlace. Intente nuevamente.</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alertDiv.innerHTML = '<div class="alert alert-danger">Error al generar el enlace. Por favor, intente nuevamente.</div>';
            });
        }

    </script>
@stop

