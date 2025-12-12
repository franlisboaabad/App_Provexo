@extends('adminlte::page')

@section('title', 'Editar Cotización')

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
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Datos de la Cotización</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cliente_id">Cliente <span class="text-danger">*</span></label>
                                    <select class="form-control @error('cliente_id') is-invalid @enderror"
                                            id="cliente_id"
                                            name="cliente_id"
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

                            <div class="col-md-3">
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

                            <div class="col-md-3">
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
                        </div>

                        <div class="row">
                            <div class="col-md-4">
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

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="observaciones">Observaciones</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                              id="observaciones"
                                              name="observaciones"
                                              rows="2">{{ old('observaciones', $cotizacione->observaciones) }}</textarea>
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

                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Productos</h5>
                        <button type="button" class="btn btn-sm btn-success" onclick="agregarProducto()">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="productos-container">
                            <!-- Los productos se cargarán aquí -->
                        </div>
                        <div class="alert alert-info mt-3" id="sin-productos">
                            <i class="fas fa-info-circle"></i> No hay productos agregados. Haga clic en "Agregar Producto" para comenzar.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Resumen</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
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

                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-block">
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
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        let contadorProductos = 0;
        const productosDisponibles = @json($productos);
        const productosExistentes = @json($cotizacione->productos);

        // Cargar productos existentes al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            if (productosExistentes.length > 0) {
                productosExistentes.forEach(producto => {
                    agregarProducto({
                        producto_id: producto.producto_id,
                        cantidad: producto.cantidad,
                        precio_unitario: producto.precio_unitario,
                        descuento: producto.descuento
                    });
                });
            }
        });

        function agregarProducto(productoData = null) {
            contadorProductos++;
            const html = `
                <div class="card mb-3 producto-item" data-index="${contadorProductos}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">Producto ${contadorProductos}</h6>
                            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${contadorProductos})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Producto <span class="text-danger">*</span></label>
                                    <select class="form-control producto-select"
                                            name="productos[${contadorProductos}][producto_id]"
                                            required
                                            onchange="seleccionarProducto(${contadorProductos}, this.value)">
                                        <option value="">Seleccione un producto</option>
                                        ${productosDisponibles.map(p => `
                                            <option value="${p.id}"
                                                    data-precio="${p.precio_venta}"
                                                    data-impuesto="${p.impuesto || 0}"
                                                    ${productoData && productoData.producto_id == p.id ? 'selected' : ''}>
                                                ${p.codigo_producto} - ${p.descripcion}
                                            </option>
                                        `).join('')}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Cantidad <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control cantidad-input"
                                           name="productos[${contadorProductos}][cantidad]"
                                           value="${productoData ? productoData.cantidad : 1}"
                                           min="1"
                                           required
                                           onchange="calcularSubtotal(${contadorProductos})">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Precio Unit. <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control precio-input"
                                           name="productos[${contadorProductos}][precio_unitario]"
                                           value="${productoData ? productoData.precio_unitario : ''}"
                                           step="0.01"
                                           min="0"
                                           required
                                           onchange="calcularSubtotal(${contadorProductos})">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Descuento %</label>
                                    <input type="number"
                                           class="form-control descuento-input"
                                           name="productos[${contadorProductos}][descuento]"
                                           value="${productoData ? productoData.descuento : 0}"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           onchange="calcularSubtotal(${contadorProductos})">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    Subtotal: <strong class="subtotal-producto">S/ 0.00</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('sin-productos').style.display = 'none';
            document.getElementById('productos-container').insertAdjacentHTML('beforeend', html);

            if (productoData) {
                calcularSubtotal(contadorProductos);
            }
        }

        function eliminarProducto(index) {
            const productoItem = document.querySelector(`.producto-item[data-index="${index}"]`);
            if (productoItem) {
                productoItem.remove();
                calcularTotal();

                if (document.querySelectorAll('.producto-item').length === 0) {
                    document.getElementById('sin-productos').style.display = 'block';
                }
            }
        }

        function seleccionarProducto(index, productoId) {
            const select = document.querySelector(`.producto-item[data-index="${index}"] .producto-select`);
            const option = select.options[select.selectedIndex];
            const precio = parseFloat(option.getAttribute('data-precio')) || 0;
            const precioInput = document.querySelector(`.producto-item[data-index="${index}"] .precio-input`);
            if (!precioInput.value) {
                precioInput.value = precio.toFixed(2);
            }
            calcularSubtotal(index);
        }

        function calcularSubtotal(index) {
            const productoItem = document.querySelector(`.producto-item[data-index="${index}"]`);
            const cantidad = parseFloat(productoItem.querySelector('.cantidad-input').value) || 0;
            const precioUnitario = parseFloat(productoItem.querySelector('.precio-input').value) || 0;
            const descuento = parseFloat(productoItem.querySelector('.descuento-input').value) || 0;
            const select = productoItem.querySelector('.producto-select');
            const option = select.options[select.selectedIndex];
            const impuesto = parseFloat(option.getAttribute('data-impuesto')) || 0;

            const precioConDescuento = precioUnitario - (precioUnitario * (descuento / 100));
            const subtotalSinImpuesto = precioConDescuento * cantidad;
            const impuestoMonto = subtotalSinImpuesto * (impuesto / 100);
            const subtotal = subtotalSinImpuesto + impuestoMonto;

            productoItem.querySelector('.subtotal-producto').textContent = 'S/ ' + subtotal.toFixed(2);
            calcularTotal();
        }

        function calcularTotal() {
            let subtotal = 0;
            let impuestoTotal = 0;

            document.querySelectorAll('.producto-item').forEach(item => {
                const cantidad = parseFloat(item.querySelector('.cantidad-input').value) || 0;
                const precioUnitario = parseFloat(item.querySelector('.precio-input').value) || 0;
                const descuento = parseFloat(item.querySelector('.descuento-input').value) || 0;
                const select = item.querySelector('.producto-select');
                const option = select.options[select.selectedIndex];
                const impuesto = parseFloat(option.getAttribute('data-impuesto')) || 0;

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

        // Validar formulario antes de enviar
        document.getElementById('formCotizacion').addEventListener('submit', function(e) {
            const productosItems = document.querySelectorAll('.producto-item');
            if (productosItems.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto a la cotización');
                return false;
            }
        });

        // Calcular total al cargar la página si hay descuento
        document.getElementById('descuento-global').addEventListener('change', calcularTotal);
        document.getElementById('descuento-global').addEventListener('input', calcularTotal);
    </script>
@stop

