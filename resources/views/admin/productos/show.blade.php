@extends('adminlte::page')

@section('title', 'Detalle del Producto')

@section('content_header')
    <h1>Detalle del Producto</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                @can('admin.productos.edit')
                    <a href="{{ route('admin.productos.edit', $producto) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endcan
                <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Información del Producto</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID:</th>
                            <td>{{ $producto->id }}</td>
                        </tr>
                        <tr>
                            <th>Código:</th>
                            <td><strong>{{ $producto->codigo_producto }}</strong></td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $producto->descripcion }}</td>
                        </tr>
                        <tr>
                            <th>Precio Base:</th>
                            <td><strong>S/ {{ number_format($producto->precio_base, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Precio de Venta:</th>
                            <td><strong class="text-success">S/ {{ number_format($producto->precio_venta, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Impuesto/IVA:</th>
                            <td>
                                <strong>{{ number_format($producto->impuesto, 2) }}%</strong>
                                @if($producto->impuesto > 0)
                                    <br><small class="text-muted">
                                        S/ {{ number_format(($producto->precio_venta * $producto->impuesto) / 100, 2) }} de impuesto
                                    </small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Ganancia:</th>
                            <td>
                                <strong class="text-primary">
                                    S/ {{ number_format($producto->precio_venta - $producto->precio_base, 2) }}
                                    ({{ number_format((($producto->precio_venta - $producto->precio_base) / $producto->precio_base) * 100, 2) }}%)
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Stock:</th>
                            <td>
                                <span class="badge badge-{{ $producto->stock > 0 ? 'success' : 'danger' }} badge-lg">
                                    {{ $producto->stock }} {{ $producto->unidad_medida }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($producto->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Información Adicional</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Proveedor:</th>
                            <td>
                                <strong>{{ $producto->proveedor->user->name ?? 'N/A' }}</strong>
                                @if($producto->proveedor->empresa)
                                    <br><small class="text-muted">{{ $producto->proveedor->empresa }}</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Unidad de Medida:</th>
                            <td>{{ ucfirst($producto->unidad_medida) }}</td>
                        </tr>
                        <tr>
                            <th>Categoría:</th>
                            <td>{{ $producto->categoria ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Marca:</th>
                            <td>{{ $producto->marca ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Imagen:</th>
                            <td>
                                @if($producto->imagen)
                                    <img src="{{ $producto->imagen }}" alt="{{ $producto->descripcion }}"
                                         class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    <br><small class="text-muted">{{ $producto->imagen }}</small>
                                @else
                                    <span class="text-muted">Sin imagen</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Registro:</th>
                            <td>{{ $producto->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $producto->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Vista de detalle de producto cargada');
    </script>
@stop

