@extends('adminlte::page')

@section('title', 'Registrar Producto')

@section('content_header')
    <h1>Registrar Nuevo Producto</h1>
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

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.productos.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="proveedor_id">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control @error('proveedor_id') is-invalid @enderror"
                                    id="proveedor_id"
                                    name="proveedor_id"
                                    required>
                                <option value="">Seleccione un proveedor</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->user->name }} - {{ $proveedor->empresa ?? 'Sin empresa' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proveedor_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="codigo_producto">Código del Producto <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('codigo_producto') is-invalid @enderror"
                                   id="codigo_producto"
                                   name="codigo_producto"
                                   value="{{ old('codigo_producto') }}"
                                   required
                                   autofocus>
                            @error('codigo_producto')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Código único del producto</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('descripcion') is-invalid @enderror"
                           id="descripcion"
                           name="descripcion"
                           value="{{ old('descripcion') }}"
                           required>
                    @error('descripcion')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="precio_base">Precio Base (Costo) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">S/</span>
                                </div>
                                <input type="number"
                                       class="form-control @error('precio_base') is-invalid @enderror"
                                       id="precio_base"
                                       name="precio_base"
                                       value="{{ old('precio_base') }}"
                                       step="0.01"
                                       min="0"
                                       required>
                            </div>
                            @error('precio_base')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="precio_venta">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">S/</span>
                                </div>
                                <input type="number"
                                       class="form-control @error('precio_venta') is-invalid @enderror"
                                       id="precio_venta"
                                       name="precio_venta"
                                       value="{{ old('precio_venta') }}"
                                       step="0.01"
                                       min="0"
                                       required>
                            </div>
                            @error('precio_venta')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="impuesto">Impuesto/IVA (%)</label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control @error('impuesto') is-invalid @enderror"
                                       id="impuesto"
                                       name="impuesto"
                                       value="{{ old('impuesto', 0) }}"
                                       step="0.01"
                                       min="0"
                                       max="100">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            @error('impuesto')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Porcentaje de impuesto (ej: 18 para IGV)</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   id="stock"
                                   name="stock"
                                   value="{{ old('stock', 0) }}"
                                   min="0">
                            @error('stock')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="unidad_medida">Unidad de Medida</label>
                            <select class="form-control @error('unidad_medida') is-invalid @enderror"
                                    id="unidad_medida"
                                    name="unidad_medida">
                                <option value="unidad" {{ old('unidad_medida', 'unidad') == 'unidad' ? 'selected' : '' }}>Unidad</option>
                                <option value="kg" {{ old('unidad_medida') == 'kg' ? 'selected' : '' }}>Kilogramo (kg)</option>
                                <option value="litro" {{ old('unidad_medida') == 'litro' ? 'selected' : '' }}>Litro</option>
                                <option value="metro" {{ old('unidad_medida') == 'metro' ? 'selected' : '' }}>Metro</option>
                                <option value="paquete" {{ old('unidad_medida') == 'paquete' ? 'selected' : '' }}>Paquete</option>
                            </select>
                            @error('unidad_medida')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="categoria">Categoría</label>
                            <input type="text"
                                   class="form-control @error('categoria') is-invalid @enderror"
                                   id="categoria"
                                   name="categoria"
                                   value="{{ old('categoria') }}">
                            @error('categoria')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="marca">Marca</label>
                            <input type="text"
                                   class="form-control @error('marca') is-invalid @enderror"
                                   id="marca"
                                   name="marca"
                                   value="{{ old('marca') }}">
                            @error('marca')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="imagen">URL de Imagen</label>
                            <input type="text"
                                   class="form-control @error('imagen') is-invalid @enderror"
                                   id="imagen"
                                   name="imagen"
                                   value="{{ old('imagen') }}">
                            @error('imagen')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="activo"
                               name="activo"
                               value="1"
                               {{ old('activo', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">
                            Producto Activo
                        </label>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Registrar Producto
                    </button>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Formulario de registro de producto cargado');
    </script>
@stop

