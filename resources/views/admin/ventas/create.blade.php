@extends('adminlte::page')

@section('title', 'Nueva Venta')

@section('content_header')
    <h1>Nueva Venta</h1>
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
            <a href="{{ route('admin.ventas.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ventas.store') }}" method="POST" id="formVenta">
                @csrf

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
                    <!-- Selección de Cotización -->
                    <div class="col-md-12 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Cotización</h5>
                            </div>
                            <div class="card-body">
                                @if($cotizacion)
                                    <div class="alert alert-info">
                                        <strong>Cotización seleccionada:</strong> {{ $cotizacion->numero_cotizacion }}
                                        <br>
                                        <strong>Cliente:</strong> {{ $cotizacion->cliente->user->name ?? 'N/A' }}
                                        <br>
                                        <strong>Monto Cotizado:</strong> S/ {{ number_format($cotizacion->total, 2) }}
                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}" target="_blank" class="btn btn-sm btn-success text-black ml-2">
                                            <i class="fas fa-eye"></i> Ver Cotización
                                        </a>
                                    </div>
                                    <input type="hidden" name="cotizacion_id" value="{{ $cotizacion->id }}">
                                @else
                                    <div class="form-group">
                                        <label for="cotizacion_id">Seleccionar Cotización <span class="text-danger">*</span></label>
                                        <select name="cotizacion_id" id="cotizacion_id" class="form-control" required>
                                            <option value="">-- Seleccione una cotización --</option>
                                            @foreach($cotizacionesDisponibles as $cot)
                                                <option value="{{ $cot->id }}" data-monto="{{ $cot->total }}">
                                                    {{ $cot->numero_cotizacion }} - {{ $cot->cliente->user->name ?? 'N/A' }} (S/ {{ number_format($cot->total, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Solo se muestran cotizaciones sin venta asociada</small>
                                    </div>
                                @endif
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
                                   value="{{ old('monto_vendido', $cotizacion->total ?? '') }}"
                                   required>
                            <small class="form-text text-muted">Monto total de la venta</small>
                        </div>
                    </div>

                    <!-- Estado del Pedido -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado_pedido">Estado del Pedido</label>
                            <select name="estado_pedido" id="estado_pedido" class="form-control">
                                <option value="pendiente" {{ old('estado_pedido', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_proceso" {{ old('estado_pedido') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="entregado" {{ old('estado_pedido') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                                <option value="cancelado" {{ old('estado_pedido') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Estado de Entrega -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado_entrega">Estado de Entrega</label>
                            <select name="estado_entrega" id="estado_entrega" class="form-control">
                                @foreach(\App\Models\Venta::getEstadosEntregaParaSelect() as $valor => $texto)
                                    <option value="{{ $valor }}" {{ old('estado_entrega', \App\Models\Venta::getEstadoEntregaDefault()) == $valor ? 'selected' : '' }}>{{ $texto }}</option>
                                @endforeach
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
                                   value="{{ old('adelanto', 0) }}">
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
                                   value="S/ 0.00">
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
                                    <!-- Los gastos se agregarán dinámicamente aquí -->
                                </div>
                                <button type="button" class="btn btn-sm btn-success mt-2" id="btn-agregar-gasto">
                                    <i class="fas fa-plus"></i> Agregar Gasto
                                </button>
                                <div class="mt-3">
                                    <strong>Total de Gastos: <span id="total-gastos" class="text-danger">S/ 0.00</span></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Código de Seguimiento (autogenerado) -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo_seguimiento">Código de Seguimiento</label>
                            <input type="text"
                                   name="codigo_seguimiento"
                                   id="codigo_seguimiento"
                                   class="form-control"
                                   value="{{ old('codigo_seguimiento') }}"
                                   placeholder="Se generará automáticamente (ej: ORD-2025-0001)">
                            <small class="form-text text-muted">Se generará automáticamente al guardar. Puedes personalizarlo si lo deseas.</small>
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
                                      placeholder="Observaciones sobre la venta...">{{ old('nota') }}</textarea>
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
                                                      placeholder="Dirección completa de entrega...">{{ old('direccion_entrega') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="distrito">Distrito</label>
                                            <input type="text"
                                                   name="distrito"
                                                   id="distrito"
                                                   class="form-control"
                                                   value="{{ old('distrito') }}"
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
                                                   value="{{ old('provincia') }}"
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
                                                   value="{{ old('ciudad') }}"
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
                                                      placeholder="Referencias adicionales (ej: Cerca del parque, Edificio X, etc.)">{{ old('referencia') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo_postal">Código Postal</label>
                                            <input type="text"
                                                   name="codigo_postal"
                                                   id="codigo_postal"
                                                   class="form-control"
                                                   value="{{ old('codigo_postal') }}"
                                                   placeholder="Ej: 15001">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Venta
                    </button>
                    <a href="{{ route('admin.ventas.index') }}" class="btn btn-secondary">
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

            // Si hay una cotización preseleccionada, establecer el monto
            @if($cotizacion)
                $('#monto_vendido').val({{ $cotizacion->total }});
                calcularRestante();
            @endif

            // Si se selecciona una cotización del dropdown, actualizar el monto
            $('#cotizacion_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const monto = selectedOption.data('monto');
                if (monto) {
                    $('#monto_vendido').val(monto);
                    calcularRestante();
                }
            });

            function calcularRestante() {
                const montoVendido = parseFloat($('#monto_vendido').val()) || 0;
                const adelanto = parseFloat($('#adelanto').val()) || 0;
                const restante = Math.max(0, montoVendido - adelanto);
                $('#restante').val('S/ ' + restante.toFixed(2));
            }

            // Contador de gastos
            let contadorGastos = 0;

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
        });
    </script>
@stop

