@extends('adminlte::page')

@section('title', 'Editar Venta')

@section('content_header')
    <h1>Editar Venta</h1>
@stop

@section('content')
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.ventas.show', $venta) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ventas.update', $venta) }}" method="POST" id="formVenta">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <!-- Información de Cotización (solo lectura) -->
                    <div class="col-md-12 mb-3">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Cotización Asociada</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Número:</strong>
                                        <a href="{{ route('admin.cotizaciones.show', $venta->cotizacion) }}" target="_blank">
                                            {{ $venta->cotizacion->numero_cotizacion }}
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Cliente:</strong> {{ $venta->cotizacion->cliente->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Estado Cotización:</strong>
                                        <span class="badge badge-{{ $venta->cotizacion->estado == 'ganado' ? 'success' : 'danger' }}">
                                            {{ ucfirst($venta->cotizacion->estado) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monto Vendido -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="monto_vendido">Monto Vendido (S/) <span class="text-danger">*</span></label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   name="monto_vendido"
                                   id="monto_vendido"
                                   class="form-control"
                                   value="{{ old('monto_vendido', $venta->monto_vendido) }}"
                                   required>
                            <small class="form-text text-muted">Monto total de la venta</small>
                        </div>
                    </div>

                    <!-- Estado del Pedido -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado_pedido">Estado del Pedido <span class="text-danger">*</span></label>
                            <select name="estado_pedido" id="estado_pedido" class="form-control" required>
                                <option value="pendiente" {{ old('estado_pedido', $venta->estado_pedido) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_proceso" {{ old('estado_pedido', $venta->estado_pedido) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="entregado" {{ old('estado_pedido', $venta->estado_pedido) == 'entregado' ? 'selected' : '' }}>Entregado</option>
                                <option value="cancelado" {{ old('estado_pedido', $venta->estado_pedido) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Adelanto -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adelanto">Adelanto (S/)</label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   name="adelanto"
                                   id="adelanto"
                                   class="form-control"
                                   value="{{ old('adelanto', $venta->adelanto) }}">
                        </div>
                    </div>

                    <!-- Restante (calculado) -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="restante">Restante (S/)</label>
                            <input type="text"
                                   id="restante"
                                   class="form-control"
                                   readonly
                                   value="S/ {{ number_format($venta->restante, 2) }}">
                            <small class="form-text text-muted">Se calcula automáticamente</small>
                        </div>
                    </div>

                    <!-- Transporte -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="monto_transporte">Transporte (S/)</label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   name="monto_transporte"
                                   id="monto_transporte"
                                   class="form-control"
                                   value="{{ old('monto_transporte', $venta->monto_transporte) }}">
                        </div>
                    </div>

                    <!-- Nombre de Transporte -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_transporte">Nombre de Transporte</label>
                            <input type="text"
                                   name="nombre_transporte"
                                   id="nombre_transporte"
                                   class="form-control"
                                   value="{{ old('nombre_transporte', $venta->nombre_transporte) }}"
                                   placeholder="Ej: Transportes ABC S.A.">
                        </div>
                    </div>

                    <!-- Nota -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nota">Nota</label>
                            <textarea name="nota"
                                      id="nota"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Observaciones sobre la venta...">{{ old('nota', $venta->nota) }}</textarea>
                        </div>
                    </div>

                    <!-- Información de Margen (solo lectura) -->
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>Margen Bruto con Transporte:</strong>
                            <span class="text-{{ $venta->margen_bruto_con_transporte >= 0 ? 'success' : 'danger' }}">
                                S/ {{ number_format($venta->margen_bruto_con_transporte, 2) }}
                            </span>
                            <br>
                            <small>Se recalculará automáticamente al guardar</small>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Venta
                    </button>
                    <a href="{{ route('admin.ventas.show', $venta) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Calcular restante cuando cambian monto_vendido o adelanto
            $('#monto_vendido, #adelanto').on('input', calcularRestante);

            function calcularRestante() {
                const montoVendido = parseFloat($('#monto_vendido').val()) || 0;
                const adelanto = parseFloat($('#adelanto').val()) || 0;
                const restante = Math.max(0, montoVendido - adelanto);
                $('#restante').val('S/ ' + restante.toFixed(2));
            }
        });
    </script>
@stop

