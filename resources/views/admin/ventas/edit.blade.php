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

                    <!-- Estado de Entrega -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado_entrega">Estado de Entrega</label>
                            <select name="estado_entrega" id="estado_entrega" class="form-control">
                                <option value="registro_creado" {{ old('estado_entrega', $venta->estado_entrega ?? 'registro_creado') == 'registro_creado' ? 'selected' : '' }}>Registro Creado</option>
                                <option value="recogido" {{ old('estado_entrega', $venta->estado_entrega ?? '') == 'recogido' ? 'selected' : '' }}>Recogido</option>
                                <option value="en_bodega_origen" {{ old('estado_entrega', $venta->estado_entrega ?? '') == 'en_bodega_origen' ? 'selected' : '' }}>En Bodega Origen</option>
                                <option value="salida_almacen" {{ old('estado_entrega', $venta->estado_entrega ?? '') == 'salida_almacen' ? 'selected' : '' }}>Salida de Almacén</option>
                                <option value="en_transito" {{ old('estado_entrega', $venta->estado_entrega ?? '') == 'en_transito' ? 'selected' : '' }}>En Tránsito</option>
                                <option value="en_reparto" {{ old('estado_entrega', $venta->estado_entrega ?? '') == 'en_reparto' ? 'selected' : '' }}>En Reparto</option>
                                <option value="entregado" {{ old('estado_entrega', $venta->estado_entrega ?? '') == 'entregado' ? 'selected' : '' }}>Entregado</option>
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

                    <!-- Gastos de la Venta -->
                    <div class="col-md-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt"></i> Gastos de la Venta
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="gastos-container">
                                    @foreach($venta->gastos as $index => $gasto)
                                        <div class="card mb-2 gasto-item" data-gasto-index="{{ $index }}">
                                            <div class="card-body">
                                                <input type="hidden" name="gastos[{{ $index }}][id]" value="{{ $gasto->id }}">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Descripción <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                                   name="gastos[{{ $index }}][descripcion]"
                                                                   class="form-control form-control-sm"
                                                                   value="{{ old("gastos.$index.descripcion", $gasto->descripcion) }}"
                                                                   placeholder="Ej: Transporte, Embalaje, Seguro..."
                                                                   required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Monto (S/) <span class="text-danger">*</span></label>
                                                            <input type="number"
                                                                   step="0.01"
                                                                   min="0"
                                                                   name="gastos[{{ $index }}][monto]"
                                                                   class="form-control form-control-sm gasto-monto"
                                                                   value="{{ old("gastos.$index.monto", $gasto->monto) }}"
                                                                   placeholder="0.00"
                                                                   required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Fecha</label>
                                                            <input type="date"
                                                                   name="gastos[{{ $index }}][fecha]"
                                                                   class="form-control form-control-sm"
                                                                   value="{{ old("gastos.$index.fecha", $gasto->fecha ? $gasto->fecha->format('Y-m-d') : date('Y-m-d')) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>&nbsp;</label>
                                                            <button type="button" class="btn btn-sm btn-danger btn-block eliminar-gasto" data-gasto-id="{{ $gasto->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Observaciones</label>
                                                            <textarea name="gastos[{{ $index }}][observaciones]"
                                                                      class="form-control form-control-sm"
                                                                      rows="2"
                                                                      placeholder="Observaciones adicionales...">{{ old("gastos.$index.observaciones", $gasto->observaciones) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-success mt-2" id="btn-agregar-gasto">
                                    <i class="fas fa-plus"></i> Agregar Gasto
                                </button>
                                <div class="mt-3">
                                    <strong>Total de Gastos: <span id="total-gastos" class="text-danger">S/ {{ number_format($venta->total_gastos, 2) }}</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Código de Seguimiento -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo_seguimiento">Código de Seguimiento</label>
                            <input type="text"
                                   name="codigo_seguimiento"
                                   id="codigo_seguimiento"
                                   class="form-control"
                                   value="{{ old('codigo_seguimiento', $venta->codigo_seguimiento) }}"
                                   placeholder="Ej: ORD-2025-0001">
                            <small class="form-text text-muted">Código único para rastrear la venta</small>
                        </div>
                    </div>

                    <!-- Nota -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nota">Nota</label>
                            <textarea name="nota"
                                      id="nota"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Observaciones sobre la venta...">{{ old('nota', $venta->nota) }}</textarea>
                        </div>
                    </div>

                    <!-- Información de Entrega -->
                    <div class="col-md-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-truck"></i> Información de Entrega</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="direccion_entrega">Dirección de Entrega</label>
                                            <textarea name="direccion_entrega"
                                                      id="direccion_entrega"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Dirección completa de entrega...">{{ old('direccion_entrega', $venta->direccion_entrega) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="distrito">Distrito</label>
                                            <input type="text"
                                                   name="distrito"
                                                   id="distrito"
                                                   class="form-control"
                                                   value="{{ old('distrito', $venta->distrito) }}"
                                                   placeholder="Ej: San Isidro">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="provincia">Provincia</label>
                                            <input type="text"
                                                   name="provincia"
                                                   id="provincia"
                                                   class="form-control"
                                                   value="{{ old('provincia', $venta->provincia) }}"
                                                   placeholder="Ej: Lima">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ciudad">Ciudad</label>
                                            <input type="text"
                                                   name="ciudad"
                                                   id="ciudad"
                                                   class="form-control"
                                                   value="{{ old('ciudad', $venta->ciudad) }}"
                                                   placeholder="Ej: Lima">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="referencia">Referencia</label>
                                            <textarea name="referencia"
                                                      id="referencia"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Referencias adicionales (ej: Cerca del parque, Edificio X, etc.)">{{ old('referencia', $venta->referencia) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo_postal">Código Postal</label>
                                            <input type="text"
                                                   name="codigo_postal"
                                                   id="codigo_postal"
                                                   class="form-control"
                                                   value="{{ old('codigo_postal', $venta->codigo_postal) }}"
                                                   placeholder="Ej: 15001">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Márgenes (solo lectura) -->
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Margen Bruto:</strong>
                                    <span class="text-{{ $venta->margen_bruto_con_transporte >= 0 ? 'default' : 'danger' }}">
                                        S/ {{ number_format($venta->margen_bruto_con_transporte ?? 0, 2) }}
                                    </span>
                                    <br>
                                    <small>Monto Vendido - (Costo Productos + Total Gastos)</small>
                                </div>
                                <div class="col-md-6">
                                    <strong>Margen Neto:</strong>
                                    <span class="text-{{ ($venta->margen_neto ?? 0) >= 0 ? 'default' : 'danger' }}">
                                        S/ {{ number_format($venta->margen_neto ?? 0, 2) }}
                                    </span>
                                    <br>
                                    <small>Margen Bruto - Total Gastos</small>
                                </div>
                            </div>
                            <hr>
                            <small class="text-white">Se recalcularán automáticamente al guardar</small>
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

            // Contador de gastos (empezar después de los existentes)
            let contadorGastos = {{ $venta->gastos->count() }};
            const gastosEliminar = [];

            // Agregar nuevo gasto
            $('#btn-agregar-gasto').on('click', function() {
                const gastoHtml = `
                    <div class="card mb-2 gasto-item" data-gasto-index="${contadorGastos}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Descripción <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="gastos[${contadorGastos}][descripcion]"
                                               class="form-control form-control-sm"
                                               placeholder="Ej: Transporte, Embalaje, Seguro..."
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Monto (S/) <span class="text-danger">*</span></label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               name="gastos[${contadorGastos}][monto]"
                                               class="form-control form-control-sm gasto-monto"
                                               placeholder="0.00"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="date"
                                               name="gastos[${contadorGastos}][fecha]"
                                               class="form-control form-control-sm"
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-sm btn-danger btn-block eliminar-gasto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Observaciones</label>
                                        <textarea name="gastos[${contadorGastos}][observaciones]"
                                                  class="form-control form-control-sm"
                                                  rows="2"
                                                  placeholder="Observaciones adicionales..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#gastos-container').append(gastoHtml);
                contadorGastos++;
                actualizarTotalGastos();
            });

            // Eliminar gasto
            $(document).on('click', '.eliminar-gasto', function() {
                const gastoId = $(this).data('gasto-id');
                if (gastoId) {
                    // Si tiene ID, agregar a la lista de eliminados
                    gastosEliminar.push(gastoId);
                    // Crear campo hidden para enviar IDs a eliminar
                    if ($('#gastos-eliminar-container').length === 0) {
                        $('#formVenta').append('<div id="gastos-eliminar-container"></div>');
                    }
                    $('#gastos-eliminar-container').append(`<input type="hidden" name="gastos_eliminar[]" value="${gastoId}">`);
                }
                $(this).closest('.gasto-item').remove();
                actualizarTotalGastos();
            });

            // Actualizar total de gastos
            function actualizarTotalGastos() {
                let total = 0;
                $('.gasto-monto').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#total-gastos').text('S/ ' + total.toFixed(2));
            }

            // Recalcular total cuando cambia un monto de gasto
            $(document).on('input', '.gasto-monto', function() {
                actualizarTotalGastos();
            });

            // Inicializar total de gastos
            actualizarTotalGastos();
        });
    </script>
@stop

