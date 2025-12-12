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
            <table id="cotizacionesTable" class="table table-bordered table-striped table-hover">
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
                                @can('admin.cotizaciones.edit')
                                    <select class="form-control form-control-sm cambiar-estado"
                                            data-cotizacion-id="{{ $cotizacion->id }}"
                                            data-estado-actual="{{ $cotizacion->estado }}">
                                        <option value="pendiente" {{ $cotizacion->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="aprobada" {{ $cotizacion->estado == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                        <option value="rechazada" {{ $cotizacion->estado == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                        <option value="vencida" {{ $cotizacion->estado == 'vencida' ? 'selected' : '' }}>Vencida</option>
                                    </select>
                                @else
                                    @if($cotizacion->estado == 'aprobada')
                                        <span class="badge badge-success">Aprobada</span>
                                    @elseif($cotizacion->estado == 'rechazada')
                                        <span class="badge badge-danger">Rechazada</span>
                                    @elseif($cotizacion->estado == 'vencida')
                                        <span class="badge badge-warning">Vencida</span>
                                    @else
                                        <span class="badge badge-info">Pendiente</span>
                                    @endif
                                @endcan
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
                    { "orderable": false, "targets": 6 } // Estado no ordenable
                ]
            });

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
    </script>
@stop

