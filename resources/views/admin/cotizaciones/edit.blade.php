@extends('adminlte::page')

@section('title', 'Editar Cotización')

@section('plugins.Select2', true)

@section('content_header')
    <h1>Editar Cotización: {{ $cotizacione->numero_cotizacion }}</h1>
@stop

@section('content')
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

    <form action="{{ route('admin.cotizaciones.update', $cotizacione) }}" method="POST" id="formCotizacion">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <!-- Datos de la Cotización -->
                <div class="card">
                    <div class="card-header">
                        <h5>Datos de la Cotización</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cliente_id">Cliente <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('cliente_id') is-invalid @enderror"
                                            id="cliente_id"
                                            name="cliente_id"
                                            style="width: 100%;"
                                            required>
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" {{ old('cliente_id', $cotizacione->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->user->name }} - {{ $cliente->empresa ?? 'Sin empresa' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cliente_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-auto">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalNuevoCliente">
                                        <i class="fas fa-plus-circle"></i> Nuevo Cliente
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fecha_emision">Fecha Emisión <span class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control @error('fecha_emision') is-invalid @enderror"
                                           id="fecha_emision"
                                           name="fecha_emision"
                                           value="{{ old('fecha_emision', $cotizacione->fecha_emision->format('Y-m-d')) }}"
                                           required>
                                    @error('fecha_emision')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fecha_vencimiento">Fecha Vencimiento</label>
                                    <input type="date"
                                           class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                                           id="fecha_vencimiento"
                                           name="fecha_vencimiento"
                                           value="{{ old('fecha_vencimiento', $cotizacione->fecha_vencimiento ? $cotizacione->fecha_vencimiento->format('Y-m-d') : '') }}">
                                    @error('fecha_vencimiento')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="estado">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control @error('estado') is-invalid @enderror"
                                            id="estado"
                                            name="estado"
                                            required>
                                        <option value="pendiente" {{ old('estado', $cotizacione->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="aprobada" {{ old('estado', $cotizacione->estado) == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                        <option value="rechazada" {{ old('estado', $cotizacione->estado) == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                        <option value="vencida" {{ old('estado', $cotizacione->estado) == 'vencida' ? 'selected' : '' }}>Vencida</option>
                                    </select>
                                    @error('estado')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <input type="text"
                                           class="form-control @error('observaciones') is-invalid @enderror"
                                           id="observaciones"
                                           name="observaciones"
                                           value="{{ old('observaciones', $cotizacione->observaciones) }}"
                                           placeholder="Observaciones adicionales...">
                                    @error('observaciones')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Productos -->
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-shopping-cart"></i> Productos de la Cotización</h5>
                        <span class="badge badge-primary" id="contador-productos">0 productos</span>
                    </div>
                    <div class="card-body pb-2">
                        <div class="d-flex gap-2 mb-3 flex-wrap">
                            <button type="button" class="btn btn-success btn-sm mr-2" onclick="abrirModalProductos()">
                                <i class="fas fa-plus-circle"></i> Agregar Producto
                            </button>
                            <button type="button" class="btn btn-primary btn-sm mr-2" onclick="abrirModalNuevoProducto()">
                                <i class="fas fa-box"></i> Nuevo Producto
                            </button>
                            <button type="button" class="btn btn-danger btn-sm mr-2" onclick="limpiarProductos()">
                                <i class="fas fa-trash"></i> Limpiar Todo
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-0 p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover table-bordered mb-0" id="tabla-productos">
                                <thead class="thead-light sticky-top">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">Código</th>
                                        <th width="25%">Descripción</th>
                                        <th width="8%" class="text-center">Cantidad</th>
                                        <th width="10%" class="text-right">Precio Base</th>
                                        <th width="8%" class="text-center">Desc. %</th>
                                        <th width="8%" class="text-center">Imp. %</th>
                                        <th width="10%" class="text-right">Subtotal</th>
                                        <th width="5%" class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-productos">
                                    <!-- Los productos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Resumen y Botones -->
                <div class="row mt-3">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group mb-0">
                                    <label for="observaciones-detalle">Observaciones detalladas</label>
                                    <textarea class="form-control"
                                              id="observaciones-detalle"
                                              name="observaciones-detalle"
                                              rows="3"
                                              placeholder="Notas adicionales sobre la cotización...">{{ old('observaciones-detalle', $cotizacione->observaciones) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Resumen</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-right"><span id="subtotal">S/ 0.00</span></td>
                                    </tr>
                                    <tr>
                                        <th>Descuento:</th>
                                        <td class="text-right">
                                            <input type="number"
                                                   class="form-control form-control-sm text-right"
                                                   id="descuento-global"
                                                   name="descuento"
                                                   value="{{ old('descuento', $cotizacione->descuento) }}"
                                                   step="0.01"
                                                   min="0"
                                                   onchange="calcularTotal()">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Impuesto:</th>
                                        <td class="text-right"><span id="impuesto-total">S/ 0.00</span></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th><strong>TOTAL:</strong></th>
                                        <td class="text-right"><strong><span id="total">S/ 0.00</span></strong></td>
                                    </tr>
                                </table>
                                <hr>
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-success btn-block btn-lg">
                                        <i class="fas fa-save"></i> Actualizar Cotización
                                    </button>
                                    <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal para nuevo cliente -->
    <div class="modal fade" id="modalNuevoCliente" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formNuevoCliente">
                    <div class="modal-body">
                        <div id="alertNuevoCliente"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nuevo_cliente_name">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           id="nuevo_cliente_name"
                                           name="name"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nuevo_cliente_email">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control"
                                           id="nuevo_cliente_email"
                                           name="email"
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nuevo_cliente_password">Contraseña <span class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control"
                                           id="nuevo_cliente_password"
                                           name="password"
                                           required
                                           minlength="8">
                                    <small class="form-text text-muted">Mínimo 8 caracteres</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nuevo_cliente_password_confirmation">Confirmar Contraseña <span class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control"
                                           id="nuevo_cliente_password_confirmation"
                                           name="password_confirmation"
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nuevo_cliente_celular">Celular</label>
                                    <input type="text"
                                           class="form-control"
                                           id="nuevo_cliente_celular"
                                           name="celular"
                                           maxlength="20">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nuevo_cliente_empresa">Empresa</label>
                                    <input type="text"
                                           class="form-control"
                                           id="nuevo_cliente_empresa"
                                           name="empresa">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nuevo_cliente_ruc">RUC</label>
                                    <input type="text"
                                           class="form-control"
                                           id="nuevo_cliente_ruc"
                                           name="ruc"
                                           maxlength="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para cálculo de fletes -->
    <div class="modal fade" id="modalCalculoFletes" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-calculator"></i> Cálculo de Fletes y Margen
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instrucciones:</strong> Complete los campos de Peso x Unidad, Flete x Tonelada y % Margen para cada producto.
                        Los cálculos se realizarán automáticamente.
                    </div>
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered" id="tabla-fletes">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th width="4%" class="text-center">#</th>
                                    <th width="10%">Código</th>
                                    <th width="15%">Descripción</th>
                                    <th width="6%" class="text-center">Cant.</th>
                                    <th width="9%" class="text-right">Precio Unit.</th>
                                    <th width="9%" class="text-center">Peso x Unidad (kg)</th>
                                    <th width="9%" class="text-center">Flete x Ton. (S/)</th>
                                    <th width="8%" class="text-center">% Margen</th>
                                    <th width="9%" class="text-right">Flete Unit.</th>
                                    <th width="9%" class="text-right">Costo + Flete</th>
                                    <th width="8%" class="text-right">Total KG</th>
                                    <th width="9%" class="text-right">Margen Total</th>
                                    <th width="9%" class="text-right">Flete Total (S/)</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-fletes">
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Resumen de Fletes</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Total KG:</strong> <span id="resumen-total-kg">0.00</span> kg
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total Flete:</strong> S/ <span id="resumen-total-flete">0.00</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total Margen:</strong> S/ <span id="resumen-total-margen">0.00</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total Costo + Flete:</strong> S/ <span id="resumen-total-costo-flete">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="actualizar-precio-base" checked>
                                    <label class="form-check-label" for="actualizar-precio-base">
                                        <strong>Actualizar precio base del producto automáticamente</strong>
                                        <br>
                                        <small>Si está marcado, el precio base del producto se actualizará con el nuevo valor ingresado en esta cotización.</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btn-confirmar-fletes">
                        <i class="fas fa-check"></i> Confirmar y Actualizar Cotización
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para seleccionar productos -->
    <div class="modal fade" id="modalProductos" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar Productos</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text"
                               class="form-control"
                               id="buscador-modal"
                               placeholder="Buscar en la lista...">
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-sm">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th width="5%"></th>
                                    <th width="15%">Código</th>
                                    <th width="35%">Descripción</th>
                                    <th width="15%" class="text-right">Precio</th>
                                    <th width="10%" class="text-center">Stock</th>
                                    <th width="10%" class="text-center">Imp. %</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-modal-productos">
                                @foreach($productos as $producto)
                                    <tr data-producto-id="{{ $producto->id }}"
                                        data-codigo="{{ $producto->codigo_producto }}"
                                        data-descripcion="{{ $producto->descripcion }}"
                                        data-precio="{{ $producto->precio_venta }}"
                                        data-impuesto="{{ $producto->impuesto ?? 0 }}"
                                        data-unidad="{{ $producto->unidad_medida }}"
                                        class="fila-producto-modal">
                                        <td>
                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    onclick="agregarProductoDesdeModal({{ $producto->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                        <td><strong>{{ $producto->codigo_producto }}</strong></td>
                                        <td>{{ $producto->descripcion }}</td>
                                        <td class="text-right">S/ {{ number_format($producto->precio_venta, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $producto->stock > 0 ? 'success' : 'warning' }}">
                                                {{ $producto->stock }} {{ $producto->unidad_medida }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ number_format($producto->impuesto ?? 0, 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo Producto -->
    <div class="modal fade" id="modalNuevoProducto" tabindex="-1" role="dialog" aria-labelledby="modalNuevoProductoLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalNuevoProductoLabel">
                        <i class="fas fa-box"></i> Registrar Nuevo Producto
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formNuevoProducto">
                    @csrf
                    <div class="modal-body">
                        <div id="alertNuevoProducto"></div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nuevo_proveedor_id">Proveedor</label>
                                    <select class="form-control" id="nuevo_proveedor_id" name="proveedor_id">
                                        <option value="">Seleccione un proveedor (Opcional)</option>
                                        @foreach(\App\Models\Proveedor::all() as $proveedor)
                                            <option value="{{ $proveedor->id }}">
                                                {{ $proveedor->nombre }} {{ $proveedor->empresa ? '- ' . $proveedor->empresa : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nuevo_codigo_producto">Código del Producto <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           id="nuevo_codigo_producto"
                                           name="codigo_producto"
                                           required
                                           autofocus>
                                    <small class="form-text text-muted">Código único del producto</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nuevo_descripcion">Descripción <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="nuevo_descripcion"
                                   name="descripcion"
                                   required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nuevo_precio_base">Precio Base (Costo) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">S/</span>
                                        </div>
                                        <input type="number"
                                               class="form-control"
                                               id="nuevo_precio_base"
                                               name="precio_base"
                                               step="0.01"
                                               min="0"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nuevo_precio_venta">Precio de Venta <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">S/</span>
                                        </div>
                                        <input type="number"
                                               class="form-control"
                                               id="nuevo_precio_venta"
                                               name="precio_venta"
                                               step="0.01"
                                               min="0"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nuevo_impuesto">Impuesto/IVA (%)</label>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control"
                                               id="nuevo_impuesto"
                                               name="impuesto"
                                               value="0"
                                               step="0.01"
                                               min="0"
                                               max="100">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nuevo_stock">Stock</label>
                                    <input type="number"
                                           class="form-control"
                                           id="nuevo_stock"
                                           name="stock"
                                           value="0"
                                           min="0">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nuevo_unidad_medida">Unidad de Medida</label>
                                    <input type="text"
                                           class="form-control"
                                           id="nuevo_unidad_medida"
                                           name="unidad_medida"
                                           value="unidad"
                                           placeholder="Ej: unidad, kg, m">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar y Agregar a Cotización
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        #resultados-busqueda {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
        }
        #resultados-busqueda .list-group-item {
            border-left: none;
            border-right: none;
            border-top: none;
        }
        #resultados-busqueda .list-group-item:last-child {
            border-bottom: none;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
        #tabla-productos thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }
        .input-producto {
            border: none;
            background: transparent;
            width: 100%;
            text-align: center;
        }
        .input-producto:focus {
            background-color: #fff;
            border: 1px solid #80bdff;
            border-radius: 4px;
        }
        .position-relative {
            position: relative;
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        /* Alineación del botón Nuevo Cliente */
        .col-md-auto .form-group {
            display: flex;
            flex-direction: column;
        }
        .col-md-auto .form-group label {
            height: 20px;
            margin-bottom: 0.5rem;
        }
        .col-md-auto .form-group button {
            margin-top: 0;
            height: 38px;
            align-self: flex-start;
        }
        /* Estilos para modal de fletes - más compacto */
        #tabla-fletes {
            font-size: 0.85rem;
        }
        #tabla-fletes thead th {
            font-size: 0.8rem;
            padding: 8px 6px;
            white-space: nowrap;
            font-weight: 600;
        }
        #tabla-fletes tbody td {
            padding: 8px 6px;
            vertical-align: middle;
        }
        #tabla-fletes .form-control-sm {
            font-size: 0.8rem;
            padding: 4px 6px;
            height: 28px;
        }
        #tabla-fletes small {
            font-size: 0.7rem;
        }
        #tabla-fletes tbody td strong {
            font-size: 0.85rem;
        }
    </style>
@stop

@section('js')
    <script>
        let productosAgregados = new Map();
        let contadorProductos = 0;
        const productosDisponibles = @json($productos);
        const productosExistentes = @json($cotizacione->productos);
        const STORAGE_KEY = 'cotizacion_edit_temporal_' + {{ $cotizacione->id }};

        // Inicializar Select2 para cliente
        $(document).ready(function() {
            $('#cliente_id').select2({
                placeholder: 'Seleccione un cliente',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    }
                }
            });

            // Cargar productos existentes al iniciar
            if (productosExistentes.length > 0) {
                productosExistentes.forEach(producto => {
                    const productoCompleto = productosDisponibles.find(p => p.id === producto.producto_id);
                    if (productoCompleto) {
                        agregarProductoATabla(productoCompleto, {
                            cantidad: producto.cantidad,
                            precio_unitario: producto.precio_unitario,
                            descuento: producto.descuento
                        });
                    }
                });
            }

            // Guardar en localStorage cuando cambia el cliente
            $('#cliente_id').on('change', function() {
                guardarEnLocalStorage();
            });

            // Guardar cada vez que cambien los datos del formulario
            document.getElementById('fecha_emision').addEventListener('change', guardarEnLocalStorage);
            document.getElementById('fecha_vencimiento').addEventListener('change', guardarEnLocalStorage);
            document.getElementById('estado').addEventListener('change', guardarEnLocalStorage);
            document.getElementById('observaciones-detalle').addEventListener('input', guardarEnLocalStorage);
        });

        // Guardar datos en localStorage
        function guardarEnLocalStorage() {
            try {
                const datos = {
                    cliente_id: document.getElementById('cliente_id').value,
                    fecha_emision: document.getElementById('fecha_emision').value,
                    fecha_vencimiento: document.getElementById('fecha_vencimiento').value,
                    estado: document.getElementById('estado').value,
                    observaciones: document.getElementById('observaciones-detalle').value,
                    descuento: document.getElementById('descuento-global').value,
                    productos: []
                };

                // Guardar productos
                document.querySelectorAll('#tbody-productos tr[data-producto-id]').forEach(fila => {
                    const productoId = parseInt(fila.getAttribute('data-producto-id'));
                    datos.productos.push({
                        producto_id: productoId,
                        cantidad: parseInt(fila.querySelector('.cantidad-input').value),
                        precio_unitario: parseFloat(fila.querySelector('.precio-input').value),
                        descuento: parseFloat(fila.querySelector('.descuento-input').value) || 0
                    });
                });

                localStorage.setItem(STORAGE_KEY, JSON.stringify(datos));
            } catch (e) {
                console.error('Error al guardar en localStorage:', e);
            }
        }

        // Limpiar localStorage
        function limpiarLocalStorage() {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (e) {
                console.error('Error al limpiar localStorage:', e);
            }
        }

        // Búsqueda en tiempo real (solo si existe el elemento)
        const buscadorProductos = document.getElementById('buscador-productos');
        if (buscadorProductos) {
            buscadorProductos.addEventListener('input', function(e) {
                const busqueda = e.target.value.toLowerCase().trim();
                const resultados = document.getElementById('resultados-busqueda');
                if (!resultados) return;

                if (busqueda.length < 2) {
                    resultados.style.display = 'none';
                    return;
                }

                const coincidencias = productosDisponibles.filter(p =>
                    p.codigo_producto.toLowerCase().includes(busqueda) ||
                    p.descripcion.toLowerCase().includes(busqueda)
                ).slice(0, 10);

                if (coincidencias.length === 0) {
                    resultados.innerHTML = '<div class="list-group-item">No se encontraron productos</div>';
                    resultados.style.display = 'block';
                    return;
                }

                resultados.innerHTML = coincidencias.map(p => `
                    <div class="list-group-item list-group-item-action"
                         onclick="agregarProductoRapido(${p.id})"
                         style="cursor: pointer;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${p.codigo_producto}</strong> - ${p.descripcion}
                            </div>
                            <div class="text-right">
                                <small class="text-muted">S/ ${parseFloat(p.precio_venta).toFixed(2)}</small>
                                <button class="btn btn-sm btn-success ml-2" onclick="event.stopPropagation(); agregarProductoRapido(${p.id});">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');

                resultados.style.display = 'block';
            });

            // Agregar producto con Enter
            buscadorProductos.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const primeraResultado = document.querySelector('#resultados-busqueda .list-group-item-action');
                    if (primeraResultado) {
                        primeraResultado.click();
                    }
                }
            });
        }

        // Ocultar resultados al hacer clic fuera (solo si existe el elemento)
        if (buscadorProductos) {
            document.addEventListener('click', function(e) {
                const resultados = document.getElementById('resultados-busqueda');
                if (!resultados) return;
                const contenedor = buscadorProductos.closest('.position-relative');
                if (!contenedor) return;

                if (!contenedor.contains(e.target)) {
                    resultados.style.display = 'none';
                }
            });
        }

        // Agregar producto rápido desde búsqueda
        function agregarProductoRapido(productoId) {
            const producto = productosDisponibles.find(p => p.id === productoId);
            if (!producto) return;

            if (productosAgregados.has(productoId)) {
                // Si ya existe, incrementar cantidad
                const fila = document.querySelector(`tr[data-producto-id="${productoId}"]`);
                const cantidadInput = fila.querySelector('.cantidad-input');
                cantidadInput.value = parseInt(cantidadInput.value) + 1;
                calcularFila(fila);
                guardarEnLocalStorage();
            } else {
                agregarProductoATabla(producto);
            }

            document.getElementById('buscador-productos').value = '';
            document.getElementById('resultados-busqueda').style.display = 'none';
            document.getElementById('buscador-productos').focus();
        }

        // Agregar producto desde modal
        function agregarProductoDesdeModal(productoId) {
            const producto = productosDisponibles.find(p => p.id === productoId);
            if (!producto) return;

            if (productosAgregados.has(productoId)) {
                const fila = document.querySelector(`tr[data-producto-id="${productoId}"]`);
                const cantidadInput = fila.querySelector('.cantidad-input');
                cantidadInput.value = parseInt(cantidadInput.value) + 1;
                calcularFila(fila);
                guardarEnLocalStorage();
            } else {
                agregarProductoATabla(producto);
            }
        }

        // Agregar producto a la tabla
        function agregarProductoATabla(producto, productoData = null) {
            if (productosAgregados.has(producto.id)) return;

            productosAgregados.set(producto.id, producto);
            contadorProductos++;

            const cantidad = productoData ? productoData.cantidad : 1;
            const precio = productoData ? productoData.precio_unitario : parseFloat(producto.precio_venta).toFixed(2);
            const descuento = productoData ? (productoData.descuento || 0) : 0;

            const fila = document.createElement('tr');
            fila.setAttribute('data-producto-id', producto.id);
            fila.innerHTML = `
                <td>${contadorProductos}</td>
                <td><strong>${producto.codigo_producto}</strong></td>
                <td>${producto.descripcion}</td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm cantidad-input text-center"
                           name="productos[${producto.id}][cantidad]"
                           value="${cantidad}"
                           min="1"
                           required
                           onchange="calcularFila(this.closest('tr')); guardarEnLocalStorage();"
                           onkeyup="calcularFila(this.closest('tr')); guardarEnLocalStorage();">
                </td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm precio-input text-right"
                           name="productos[${producto.id}][precio_unitario]"
                           value="${precio}"
                           step="0.01"
                           min="0"
                           required
                           onchange="calcularFila(this.closest('tr')); guardarEnLocalStorage();"
                           onkeyup="calcularFila(this.closest('tr')); guardarEnLocalStorage();">
                </td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm descuento-input text-center"
                           name="productos[${producto.id}][descuento]"
                           value="${descuento}"
                           step="0.01"
                           min="0"
                           max="100"
                           onchange="calcularFila(this.closest('tr')); guardarEnLocalStorage();"
                           onkeyup="calcularFila(this.closest('tr')); guardarEnLocalStorage();">
                </td>
                <td class="text-center impuesto-display">${parseFloat(producto.impuesto || 0).toFixed(2)}%</td>
                <td class="text-right"><strong class="subtotal-fila">S/ 0.00</strong></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${producto.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <input type="hidden" name="productos[${producto.id}][producto_id]" value="${producto.id}">
                    <input type="hidden" class="impuesto-valor" value="${producto.impuesto || 0}">
                </td>
            `;

            const tbody = document.getElementById('tbody-productos');
            tbody.appendChild(fila);
            calcularFila(fila);
            actualizarContador();
            guardarEnLocalStorage();
        }

        // Calcular subtotal de una fila
        function calcularFila(fila) {
            const cantidad = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
            const precioUnitario = parseFloat(fila.querySelector('.precio-input').value) || 0;
            const descuento = parseFloat(fila.querySelector('.descuento-input').value) || 0;
            const impuesto = parseFloat(fila.querySelector('.impuesto-valor').value) || 0;

            const precioConDescuento = precioUnitario - (precioUnitario * (descuento / 100));
            const subtotalSinImpuesto = precioConDescuento * cantidad;
            const impuestoMonto = subtotalSinImpuesto * (impuesto / 100);
            const subtotal = subtotalSinImpuesto + impuestoMonto;

            fila.querySelector('.subtotal-fila').textContent = 'S/ ' + subtotal.toFixed(2);
            calcularTotal();
        }

        // Eliminar producto
        function eliminarProducto(productoId) {
            const fila = document.querySelector(`tr[data-producto-id="${productoId}"]`);
            if (fila) {
                fila.remove();
                productosAgregados.delete(productoId);
                renumerarFilas();
                actualizarContador();
                calcularTotal();
                guardarEnLocalStorage();
            }

            if (productosAgregados.size === 0) {
                const tbody = document.getElementById('tbody-productos');
                tbody.innerHTML = '<tr id="fila-vacia"><td colspan="9" class="text-center text-muted py-5"><i class="fas fa-box-open fa-3x mb-3"></i><br>No hay productos agregados. Busca y agrega productos arriba.</td></tr>';
            }
        }

        // Renumerar filas
        function renumerarFilas() {
            let contador = 0;
            document.querySelectorAll('#tbody-productos tr[data-producto-id]').forEach(fila => {
                contador++;
                fila.querySelector('td:first-child').textContent = contador;
            });
        }

        // Calcular total general
        function calcularTotal() {
            let subtotal = 0;
            let impuestoTotal = 0;

            document.querySelectorAll('#tbody-productos tr[data-producto-id]').forEach(fila => {
                const cantidad = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
                const precioUnitario = parseFloat(fila.querySelector('.precio-input').value) || 0;
                const descuento = parseFloat(fila.querySelector('.descuento-input').value) || 0;
                const impuesto = parseFloat(fila.querySelector('.impuesto-valor').value) || 0;

                const precioConDescuento = precioUnitario - (precioUnitario * (descuento / 100));
                const subtotalSinImpuesto = precioConDescuento * cantidad;
                const impuestoMonto = subtotalSinImpuesto * (impuesto / 100);

                subtotal += subtotalSinImpuesto;
                impuestoTotal += impuestoMonto;
            });

            const descuentoGlobal = parseFloat(document.getElementById('descuento-global').value) || 0;
            const total = (subtotal + impuestoTotal) - descuentoGlobal;

            document.getElementById('subtotal').textContent = 'S/ ' + subtotal.toFixed(2);
            document.getElementById('impuesto-total').textContent = 'S/ ' + impuestoTotal.toFixed(2);
            document.getElementById('total').textContent = 'S/ ' + total.toFixed(2);
        }

        // Actualizar contador
        function actualizarContador() {
            const cantidad = productosAgregados.size;
            document.getElementById('contador-productos').textContent = `${cantidad} producto${cantidad !== 1 ? 's' : ''}`;
        }

        // Limpiar todos los productos
        function limpiarProductos() {
            if (confirm('¿Está seguro de eliminar todos los productos?')) {
                productosAgregados.clear();
                document.getElementById('tbody-productos').innerHTML = '<tr id="fila-vacia"><td colspan="9" class="text-center text-muted py-5"><i class="fas fa-box-open fa-3x mb-3"></i><br>No hay productos agregados. Busca y agrega productos arriba.</td></tr>';
                actualizarContador();
                calcularTotal();
                guardarEnLocalStorage();
            }
        }

        // Abrir modal de productos
        function abrirModalProductos() {
            $('#modalProductos').modal('show');
        }

        // Búsqueda en modal (solo si existe el elemento)
        $(document).ready(function() {
            const buscadorModal = document.getElementById('buscador-modal');
            if (buscadorModal) {
                buscadorModal.addEventListener('input', function(e) {
                    const busqueda = e.target.value.toLowerCase().trim();
                    document.querySelectorAll('.fila-producto-modal').forEach(fila => {
                        const codigo = fila.getAttribute('data-codigo')?.toLowerCase() || '';
                        const descripcion = fila.getAttribute('data-descripcion')?.toLowerCase() || '';
                        if (codigo.includes(busqueda) || descripcion.includes(busqueda)) {
                            fila.style.display = '';
                        } else {
                            fila.style.display = 'none';
                        }
                    });
                });
            }
        });

        // Interceptar submit del formulario - abrir modal de fletes
        $(document).ready(function() {
            const formCotizacion = document.getElementById('formCotizacion');
            if (formCotizacion) {
                formCotizacion.addEventListener('submit', function(e) {
                    if (productosAgregados.size === 0) {
                        e.preventDefault();
                        alert('Debe agregar al menos un producto a la cotización');
                        return false;
                    }

                    e.preventDefault();

                    // Llenar el modal de fletes con los productos
                    llenarModalFletes();

                    // Abrir el modal
                    $('#modalCalculoFletes').modal('show');
                });
            }
        });

        // Función para llenar el modal de fletes
        function llenarModalFletes() {
            const tbody = document.getElementById('tbody-fletes');
            tbody.innerHTML = '';

            let contador = 0;
            document.querySelectorAll('#tbody-productos tr[data-producto-id]').forEach(fila => {
                contador++;
                const productoId = fila.getAttribute('data-producto-id');

                // Buscar el producto en productosDisponibles para obtener precio_base
                const producto = productosDisponibles.find(p => p.id == productoId);
                if (!producto) return;

                const codigo = producto.codigo_producto;
                const descripcion = producto.descripcion;
                const cantidad = fila.querySelector('.cantidad-input').value;
                const precioBase = parseFloat(producto.precio_base) || 0;

                // Obtener valores guardados si existen (de localStorage o de productos existentes)
                const datosGuardados = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
                let productoGuardado = datosGuardados.productos?.find(p => p.producto_id == productoId);

                // Si no hay en localStorage, buscar en productos existentes
                if (!productoGuardado && productosExistentes) {
                    const productoExistente = productosExistentes.find(p => p.producto_id == productoId);
                    if (productoExistente) {
                        productoGuardado = {
                            peso_unidad: productoExistente.peso_unidad,
                            flete_tonelada: productoExistente.flete_tonelada,
                            margen_porcentaje: productoExistente.margen_porcentaje,
                            precio_base_cotizacion: productoExistente.precio_base_cotizacion
                        };
                    }
                }

                const filaFlete = document.createElement('tr');
                filaFlete.setAttribute('data-producto-id', productoId);
                filaFlete.setAttribute('data-precio-base-original', precioBase);
                const precioBaseCotizacion = productoGuardado?.precio_base_cotizacion || precioBase;
                filaFlete.innerHTML = `
                    <td>${contador}</td>
                    <td><strong>${codigo}</strong></td>
                    <td>${descripcion}</td>
                    <td class="text-center">${cantidad}</td>
                    <td>
                        <input type="number"
                               class="form-control form-control-sm precio-base-input text-right"
                               step="0.01"
                               min="0"
                               value="${precioBaseCotizacion.toFixed(2)}"
                               onchange="calcularFilaFlete(this.closest('tr'))"
                               onkeyup="calcularFilaFlete(this.closest('tr'))">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            Base: S/ ${precioBase.toFixed(2)}
                        </small>
                    </td>
                    <td>
                        <input type="number"
                               class="form-control form-control-sm peso-unidad text-center"
                               step="0.0001"
                               min="0"
                               value="${productoGuardado?.peso_unidad || ''}"
                               onchange="calcularFilaFlete(this.closest('tr'))"
                               onkeyup="calcularFilaFlete(this.closest('tr'))">
                    </td>
                    <td>
                        <input type="number"
                               class="form-control form-control-sm flete-tonelada text-center"
                               step="0.01"
                               min="0"
                               value="${productoGuardado?.flete_tonelada || ''}"
                               onchange="calcularFilaFlete(this.closest('tr'))"
                               onkeyup="calcularFilaFlete(this.closest('tr'))">
                    </td>
                    <td>
                        <input type="number"
                               class="form-control form-control-sm margen-porcentaje text-center"
                               step="0.01"
                               min="0"
                               value="${productoGuardado?.margen_porcentaje || ''}"
                               onchange="calcularFilaFlete(this.closest('tr'))"
                               onkeyup="calcularFilaFlete(this.closest('tr'))">
                    </td>
                    <td class="text-right flete-unitario">S/ 0.00</td>
                    <td class="text-right costo-mas-flete">S/ 0.00</td>
                    <td class="text-right total-kg">0.00</td>
                    <td class="text-right margen-total">S/ 0.00</td>
                    <td class="text-right flete-total">S/ 0.00</td>
                `;

                tbody.appendChild(filaFlete);

                // Calcular la fila inicialmente
                calcularFilaFlete(filaFlete);
            });

            actualizarResumenFletes();
        }

        // Calcular valores de una fila de flete
        function calcularFilaFlete(fila) {
            const cantidad = parseFloat(fila.querySelector('td:nth-child(4)').textContent) || 0;
            // Usar precio_base ingresado en el modal (puede ser diferente al precio base del producto)
            const precioBase = parseFloat(fila.querySelector('.precio-base-input').value) || 0;
            const pesoUnidad = parseFloat(fila.querySelector('.peso-unidad').value) || 0;
            const fleteTonelada = parseFloat(fila.querySelector('.flete-tonelada').value) || 0;
            const margenPorcentaje = parseFloat(fila.querySelector('.margen-porcentaje').value) || 0;

            // Flete Unitario = (Peso x Unidad / 1000) × Flete x Tonelada
            const fleteUnitario = (pesoUnidad / 1000) * fleteTonelada;

            // Costo + Flete = Precio Base + Flete Unitario
            const costoMasFlete = precioBase + fleteUnitario;

            // Total KG = Cantidad × Peso x Unidad
            const totalKg = cantidad * pesoUnidad;

            // Margen Total = (costo + flete) × (% margen / 100) × Cantidad
            const margenTotal = costoMasFlete * (margenPorcentaje / 100) * cantidad;

            // Flete Total = Flete Unitario × Cantidad
            const fleteTotal = fleteUnitario * cantidad;

            // Actualizar celdas
            fila.querySelector('.flete-unitario').textContent = 'S/ ' + fleteUnitario.toFixed(4);
            fila.querySelector('.costo-mas-flete').textContent = 'S/ ' + costoMasFlete.toFixed(2);
            fila.querySelector('.total-kg').textContent = totalKg.toFixed(4);
            fila.querySelector('.margen-total').textContent = 'S/ ' + margenTotal.toFixed(2);
            fila.querySelector('.flete-total').textContent = 'S/ ' + fleteTotal.toFixed(2);

            // Actualizar resumen
            actualizarResumenFletes();
        }

        // Actualizar resumen de fletes
        function actualizarResumenFletes() {
            let totalKg = 0;
            let totalFlete = 0;
            let totalMargen = 0;
            let totalCostoFlete = 0;

            document.querySelectorAll('#tbody-fletes tr[data-producto-id]').forEach(fila => {
                totalKg += parseFloat(fila.querySelector('.total-kg').textContent) || 0;
                totalFlete += parseFloat(fila.querySelector('.flete-total').textContent.replace('S/ ', '').trim()) || 0;
                totalMargen += parseFloat(fila.querySelector('.margen-total').textContent.replace('S/ ', '').trim()) || 0;
                totalCostoFlete += parseFloat(fila.querySelector('.costo-mas-flete').textContent.replace('S/ ', '').trim()) || 0;
            });

            document.getElementById('resumen-total-kg').textContent = totalKg.toFixed(4);
            document.getElementById('resumen-total-flete').textContent = totalFlete.toFixed(2);
            document.getElementById('resumen-total-margen').textContent = totalMargen.toFixed(2);
            document.getElementById('resumen-total-costo-flete').textContent = totalCostoFlete.toFixed(2);
        }

        // Confirmar y guardar cotización con datos de flete
        document.getElementById('btn-confirmar-fletes').addEventListener('click', function() {
            // Recopilar datos de flete y agregarlos al formulario
            const productosFlete = [];

            document.querySelectorAll('#tbody-fletes tr[data-producto-id]').forEach(fila => {
                const productoId = fila.getAttribute('data-producto-id');
                const precioBaseOriginal = parseFloat(fila.getAttribute('data-precio-base-original')) || 0;
                const precioBaseCotizacion = parseFloat(fila.querySelector('.precio-base-input').value) || 0;
                const pesoUnidad = parseFloat(fila.querySelector('.peso-unidad').value) || 0;
                const fleteTonelada = parseFloat(fila.querySelector('.flete-tonelada').value) || 0;
                const margenPorcentaje = parseFloat(fila.querySelector('.margen-porcentaje').value) || 0;
                const fleteUnitario = parseFloat(fila.querySelector('.flete-unitario').textContent.replace('S/ ', '').trim()) || 0;
                const costoMasFlete = parseFloat(fila.querySelector('.costo-mas-flete').textContent.replace('S/ ', '').trim()) || 0;
                const totalKg = parseFloat(fila.querySelector('.total-kg').textContent) || 0;
                const margenTotal = parseFloat(fila.querySelector('.margen-total').textContent.replace('S/ ', '').trim()) || 0;
                const fleteTotal = parseFloat(fila.querySelector('.flete-total').textContent.replace('S/ ', '').trim()) || 0;

                productosFlete.push({
                    producto_id: productoId,
                    precio_base_original: precioBaseOriginal,
                    precio_base_cotizacion: precioBaseCotizacion,
                    peso_unidad: pesoUnidad,
                    flete_tonelada: fleteTonelada,
                    margen_porcentaje: margenPorcentaje,
                    flete_unitario: fleteUnitario,
                    costo_mas_flete: costoMasFlete,
                    total_kg: totalKg,
                    margen_total: margenTotal,
                    flete_total: fleteTotal
                });
            });

            // Agregar campos hidden al formulario con los datos de flete
            productosFlete.forEach((prod, index) => {
                const productoId = prod.producto_id;
                Object.keys(prod).forEach(key => {
                    if (key !== 'producto_id') {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `productos[${productoId}][${key}]`;
                        input.value = prod[key];
                        document.getElementById('formCotizacion').appendChild(input);
                    }
                });
            });

            // Agregar campo para indicar si se debe actualizar el precio base
            const actualizarPrecioBase = document.getElementById('actualizar-precio-base').checked;
            const inputActualizar = document.createElement('input');
            inputActualizar.type = 'hidden';
            inputActualizar.name = 'actualizar_precio_base';
            inputActualizar.value = actualizarPrecioBase ? '1' : '0';
            document.getElementById('formCotizacion').appendChild(inputActualizar);

            // Cerrar modal
            $('#modalCalculoFletes').modal('hide');

            // Limpiar localStorage antes de enviar
            limpiarLocalStorage();

            // Enviar formulario
            document.getElementById('formCotizacion').submit();
        });

        // Calcular total al cargar
        document.getElementById('descuento-global').addEventListener('change', function() {
            calcularTotal();
            guardarEnLocalStorage();
        });
        document.getElementById('descuento-global').addEventListener('input', function() {
            calcularTotal();
            guardarEnLocalStorage();
        });

        // Manejar creación de nuevo cliente
        document.getElementById('formNuevoCliente').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const alertDiv = document.getElementById('alertNuevoCliente');
            const submitBtn = this.querySelector('button[type="submit"]');

            // Limpiar alertas anteriores
            alertDiv.innerHTML = '';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            fetch('{{ route("admin.clientes.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar nueva opción al select2
                    const newOption = new Option(
                        data.cliente.name + ' - ' + (data.cliente.empresa || 'Sin empresa'),
                        data.cliente.id,
                        true,
                        true
                    );
                    $('#cliente_id').append(newOption).trigger('change');

                    // Guardar en localStorage
                    guardarEnLocalStorage();

                    // Cerrar modal y limpiar formulario
                    $('#modalNuevoCliente').modal('hide');
                    document.getElementById('formNuevoCliente').reset();
                    alertDiv.innerHTML = '';

                    // Mostrar mensaje de éxito
                    alert('Cliente creado exitosamente y seleccionado');
                } else {
                    // Mostrar errores
                    let errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            data.errors[key].forEach(error => {
                                errorHtml += '<li>' + error + '</li>';
                            });
                        });
                    } else {
                        errorHtml += '<li>' + (data.message || 'Error al crear el cliente') + '</li>';
                    }
                    errorHtml += '</ul></div>';
                    alertDiv.innerHTML = errorHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alertDiv.innerHTML = '<div class="alert alert-danger">Error al crear el cliente. Por favor, intente nuevamente.</div>';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Cliente';
            });
        });

        // Limpiar formulario cuando se cierra el modal
        $('#modalNuevoCliente').on('hidden.bs.modal', function () {
            document.getElementById('formNuevoCliente').reset();
            document.getElementById('alertNuevoCliente').innerHTML = '';
        });
        // Función para abrir modal de nuevo producto
        function abrirModalNuevoProducto() {
            $('#formNuevoProducto')[0].reset();
            $('#alertNuevoProducto').html('');
            $('#modalNuevoProducto').modal('show');
        }

        // Guardar nuevo producto vía AJAX
        document.getElementById('formNuevoProducto').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const alertDiv = document.getElementById('alertNuevoProducto');
            const submitBtn = this.querySelector('button[type="submit"]');

            // Deshabilitar botón
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            fetch('{{ route("admin.productos.store-ajax") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertDiv.innerHTML = '<div class="alert alert-success">¡Producto creado exitosamente!</div>';

                    // Agregar el producto a productosDisponibles
                    productosDisponibles.push({
                        id: data.producto.id,
                        codigo_producto: data.producto.codigo_producto,
                        descripcion: data.producto.descripcion,
                        precio_base: parseFloat(data.producto.precio_base),
                        precio_venta: parseFloat(data.producto.precio_venta),
                        impuesto: parseFloat(data.producto.impuesto),
                        stock: parseInt(data.producto.stock),
                        unidad_medida: data.producto.unidad_medida
                    });

                    // Agregar fila al modal de productos
                    const tbody = document.getElementById('tbody-modal-productos');
                    const nuevaFila = document.createElement('tr');
                    nuevaFila.setAttribute('data-producto-id', data.producto.id);
                    nuevaFila.setAttribute('data-codigo', data.producto.codigo_producto);
                    nuevaFila.setAttribute('data-descripcion', data.producto.descripcion);
                    nuevaFila.setAttribute('data-precio', data.producto.precio_venta);
                    nuevaFila.setAttribute('data-precio-base', data.producto.precio_base);
                    nuevaFila.setAttribute('data-impuesto', data.producto.impuesto);
                    nuevaFila.setAttribute('data-unidad', data.producto.unidad_medida);
                    nuevaFila.className = 'fila-producto-modal';

                    nuevaFila.innerHTML = `
                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-sm btn-success"
                                    onclick="agregarProductoDesdeModal(${data.producto.id})"
                                    title="Agregar producto">
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                        <td><strong>${data.producto.codigo_producto}</strong></td>
                        <td>${data.producto.descripcion}</td>
                        <td class="text-right">S/ ${parseFloat(data.producto.precio_base).toFixed(2)}</td>
                        <td class="text-right">S/ ${parseFloat(data.producto.precio_venta).toFixed(2)}</td>
                        <td class="text-center">
                            <span class="badge badge-success">
                                ${data.producto.stock} ${data.producto.unidad_medida}
                            </span>
                        </td>
                        <td class="text-center">${parseFloat(data.producto.impuesto).toFixed(2)}%</td>
                        <td class="text-center">
                            <span class="badge badge-success">Activo</span>
                        </td>
                    `;

                    tbody.appendChild(nuevaFila);

                    // Actualizar contador
                    const contador = parseInt(document.getElementById('contador-modal-productos').textContent);
                    document.getElementById('contador-modal-productos').textContent = contador + 1;

                    // Cerrar modal y agregar producto automáticamente
                    setTimeout(() => {
                        $('#modalNuevoProducto').modal('hide');
                        agregarProductoDesdeModal(data.producto.id);
                        $('#modalProductos').modal('show');

                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Producto creado y agregado a la cotización',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 1000);
                } else {
                    alertDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar y Agregar a Cotización';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alertDiv.innerHTML = '<div class="alert alert-danger">Error al crear el producto. Por favor, intente nuevamente.</div>';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar y Agregar a Cotización';
            });
        });
    </script>
@stop
