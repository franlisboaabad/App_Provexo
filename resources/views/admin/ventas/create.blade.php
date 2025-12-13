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
                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}" target="_blank" class="btn btn-sm btn-info ml-2">
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

                    <!-- Estado de la Venta -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado_venta">Estado de la Venta <span class="text-danger">*</span></label>
                            <select name="estado_venta" id="estado_venta" class="form-control" required>
                                <option value="ganado" {{ old('estado_venta') == 'ganado' ? 'selected' : '' }}>Ganado</option>
                                <option value="perdido" {{ old('estado_venta') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                            </select>
                            <small class="form-text text-muted">Este será el nuevo estado de la cotización</small>
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
                                   value="{{ old('monto_transporte', 0) }}">
                        </div>
                    </div>

                    <!-- Nombre de Transporte -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre_transporte">Nombre de Transporte</label>
                            <input type="text"
                                   name="nombre_transporte"
                                   id="nombre_transporte"
                                   class="form-control"
                                   value="{{ old('nombre_transporte') }}"
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
                                      placeholder="Observaciones sobre la venta...">{{ old('nota') }}</textarea>
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
        });
    </script>
@stop

