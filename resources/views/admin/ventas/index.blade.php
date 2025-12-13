@extends('adminlte::page')

@section('title', 'Lista de Ventas')

@section('content_header')
    <h1>Lista de Ventas</h1>
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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                @can('admin.ventas.create')
                    <a href="{{ route('admin.ventas.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Venta
                    </a>
                @endcan
            </div>
            <div class="d-flex align-items-center gap-2">
                <label for="filtroEstadoPedido" class="mb-0">
                    <strong>Filtrar por Estado de Pedido:</strong>
                </label>
                <select class="form-control form-control-sm" id="filtroEstadoPedido" style="width: auto; min-width: 150px;">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado_pedido') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_proceso" {{ request('estado_pedido') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="entregado" {{ request('estado_pedido') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="cancelado" {{ request('estado_pedido') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
                <label for="filtroEstadoCotizacion" class="mb-0 ml-2">
                    <strong>Estado Cotización:</strong>
                </label>
                <select class="form-control form-control-sm" id="filtroEstadoCotizacion" style="width: auto; min-width: 150px;">
                    <option value="">Todos</option>
                    <option value="ganado" {{ request('estado_cotizacion') == 'ganado' ? 'selected' : '' }}>Ganado</option>
                    <option value="perdido" {{ request('estado_cotizacion') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ventasTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cotización</th>
                            <th>Cliente</th>
                            <th>Fecha Venta</th>
                            <th>Monto Vendido</th>
                            <th>Adelanto</th>
                            <th>Restante</th>
                            <th>Estado Pedido</th>
                            <th>Estado Cotización</th>
                            <th>Margen Bruto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $venta)
                            <tr>
                                <td>{{ $venta->id }}</td>
                                <td>
                                    <a href="{{ route('admin.cotizaciones.show', $venta->cotizacion) }}" target="_blank">
                                        {{ $venta->cotizacion->numero_cotizacion }}
                                    </a>
                                </td>
                                <td>{{ $venta->cotizacion->cliente->user->name ?? 'N/A' }}</td>
                                <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                                <td>S/ {{ number_format($venta->monto_vendido, 2) }}</td>
                                <td>S/ {{ number_format($venta->adelanto, 2) }}</td>
                                <td>S/ {{ number_format($venta->restante, 2) }}</td>
                                <td>
                                    <select class="form-control form-control-sm cambiar-estado-pedido"
                                            data-venta-id="{{ $venta->id }}"
                                            style="min-width: 120px;">
                                        <option value="pendiente" {{ $venta->estado_pedido == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_proceso" {{ $venta->estado_pedido == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="entregado" {{ $venta->estado_pedido == 'entregado' ? 'selected' : '' }}>Entregado</option>
                                        <option value="cancelado" {{ $venta->estado_pedido == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $venta->cotizacion->estado == 'ganado' ? 'success' : 'danger' }}">
                                        {{ ucfirst($venta->cotizacion->estado) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-{{ $venta->margen_bruto_con_transporte >= 0 ? 'success' : 'danger' }}">
                                        S/ {{ number_format($venta->margen_bruto_con_transporte, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cog"></i> Acciones
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @can('admin.ventas.show')
                                                <a class="dropdown-item" href="{{ route('admin.ventas.show', $venta) }}">
                                                    <i class="fas fa-eye text-info"></i> Ver Detalle
                                                </a>
                                            @endcan
                                            @can('admin.ventas.edit')
                                                <a class="dropdown-item" href="{{ route('admin.ventas.edit', $venta) }}">
                                                    <i class="fas fa-edit text-warning"></i> Editar
                                                </a>
                                            @endcan
                                            @can('admin.ventas.destroy')
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.ventas.destroy', $venta) }}"
                                                      method="POST"
                                                      style="display: inline-block;"
                                                      onsubmit="return confirm('¿Está seguro de eliminar esta venta? Esta acción no se puede deshacer.');">
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
                                <td colspan="11" class="text-center">No hay ventas registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#ventasTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "order": [[0, "desc"]],
                "pageLength": 25,
                "responsive": true,
                "columnDefs": [
                    { "orderable": false, "targets": [10] } // Acciones no ordenable
                ]
            });

            // Filtro por estado de pedido
            $('#filtroEstadoPedido').on('change', function() {
                var estado = $(this).val();
                var url = new URL(window.location.href);
                if (estado) {
                    url.searchParams.set('estado_pedido', estado);
                } else {
                    url.searchParams.delete('estado_pedido');
                }
                window.location.href = url.toString();
            });

            // Filtro por estado de cotización
            $('#filtroEstadoCotizacion').on('change', function() {
                var estado = $(this).val();
                var url = new URL(window.location.href);
                if (estado) {
                    url.searchParams.set('estado_cotizacion', estado);
                } else {
                    url.searchParams.delete('estado_cotizacion');
                }
                window.location.href = url.toString();
            });

            // Cambiar estado de pedido
            $(document).on('change', '.cambiar-estado-pedido', function() {
                const select = $(this);
                const ventaId = select.data('venta-id');
                const nuevoEstado = select.val();

                select.prop('disabled', true);

                fetch(`{{ url('ventas') }}/${ventaId}/actualizar-estado-pedido`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ estado_pedido: nuevoEstado })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Error al actualizar el estado', 'error');
                        select.val(select.data('estado-anterior'));
                    }
                    select.prop('disabled', false);
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Error al actualizar el estado del pedido', 'error');
                    select.val(select.data('estado-anterior'));
                    select.prop('disabled', false);
                });
            });
        });
    </script>
@stop

