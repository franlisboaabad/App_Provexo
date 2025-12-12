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
                                    <small class="form-text text-muted">
                                        <a href="#" class="text-primary" data-toggle="modal" data-target="#modalNuevoCliente">
                                            <i class="fas fa-plus-circle"></i> Nuevo Cliente +
                                        </a>
                                    </small>
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

                <!-- Buscador y Agregar Productos -->
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Agregar Productos</h5>
                        <span class="badge badge-primary" id="contador-productos">0 productos</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12 position-relative">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="text"
                                           class="form-control form-control-lg"
                                           id="buscador-productos"
                                           placeholder="Buscar producto por código o descripción... (presiona Enter para agregar rápido)">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" onclick="abrirModalProductos()">
                                            <i class="fas fa-list"></i> Ver Todos
                                        </button>
                                    </div>
                                </div>
                                <div id="resultados-busqueda" class="list-group position-absolute" style="z-index: 1050; display: none; max-height: 300px; overflow-y: auto; width: 100%; top: 100%; margin-top: 2px; left: 0;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Productos -->
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Productos de la Cotización</h5>
                        <button type="button" class="btn btn-sm btn-danger" onclick="limpiarProductos()">
                            <i class="fas fa-trash"></i> Limpiar Todo
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover table-bordered mb-0" id="tabla-productos">
                                <thead class="thead-light sticky-top">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">Código</th>
                                        <th width="25%">Descripción</th>
                                        <th width="8%" class="text-center">Cantidad</th>
                                        <th width="10%" class="text-right">Precio Unit.</th>
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

        // Búsqueda en tiempo real
        document.getElementById('buscador-productos').addEventListener('input', function(e) {
            const busqueda = e.target.value.toLowerCase().trim();
            const resultados = document.getElementById('resultados-busqueda');

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
        document.getElementById('buscador-productos').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const primeraResultado = document.querySelector('#resultados-busqueda .list-group-item-action');
                if (primeraResultado) {
                    primeraResultado.click();
                }
            }
        });

        // Ocultar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            const buscador = document.getElementById('buscador-productos');
            const resultados = document.getElementById('resultados-busqueda');
            const contenedor = buscador.closest('.position-relative');

            if (!contenedor.contains(e.target)) {
                resultados.style.display = 'none';
            }
        });

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

        // Búsqueda en modal
        document.getElementById('buscador-modal').addEventListener('input', function(e) {
            const busqueda = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.fila-producto-modal').forEach(fila => {
                const codigo = fila.getAttribute('data-codigo').toLowerCase();
                const descripcion = fila.getAttribute('data-descripcion').toLowerCase();
                if (codigo.includes(busqueda) || descripcion.includes(busqueda)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });

        // Validar formulario
        document.getElementById('formCotizacion').addEventListener('submit', function(e) {
            if (productosAgregados.size === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto a la cotización');
                return false;
            }

            // Limpiar localStorage solo si el formulario se va a enviar correctamente
            limpiarLocalStorage();
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
    </script>
@stop
